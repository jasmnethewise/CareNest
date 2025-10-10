<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$viewer_id = $_SESSION['id'];
$viewer_role = $_SESSION['role'] ?? null;

// من نعدل؟ افتراضي نفس اللي في الجلسة، اما اذا الادمن مرر doctor_id نستخدمه
$edit_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : $viewer_id;

// صلاحية: لو بتحاول تعدل حساب غيرك وانت مش admin -> ممنوع
if ($edit_id !== $viewer_id && $viewer_role !== 'admin') {
    die("Not authorized to edit this profile.");
}

// جلب بيانات المستخدم (الدكتور)
$stmt = $conn->prepare("SELECT id, name, profile_pic, specialization, phone, location, available_days, available_times, pronouns, hospital_locations FROM users WHERE id = ?");
$stmt->bind_param("i", $edit_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "User not found.";
    exit();
}
$user = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitize basic fields
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $pronouns = mysqli_real_escape_string($conn, $_POST['pronouns'] ?? '');
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
    $available_days_json = !empty($_POST['available_days']) 
    ? json_encode($_POST['available_days'], JSON_UNESCAPED_UNICODE) 
    : json_encode([]);

// ✅ نحفظ الأوقات كـ JSON
$available_times_json = [];
if (!empty($_POST['available_times']) && is_array($_POST['available_times'])) {
    foreach ($_POST['available_times'] as $slot) {
        if (!empty($slot['day']) && !empty($slot['from']) && !empty($slot['to'])) {
            $available_times_json[] = $slot;
        }
    }
}
$available_times_json = json_encode($available_times_json, JSON_UNESCAPED_UNICODE);

    // معالجة مستشفيات متعددة
    $hospitals = array();
    if (!empty($_POST['hospital_locations']) && is_array($_POST['hospital_locations'])) {
        foreach ($_POST['hospital_locations'] as $h) {
            $htrim = trim($h);
            if ($htrim !== '') $hospitals[] = $htrim;
        }
    }
    $hospital_locations = mysqli_real_escape_string($conn, implode(", ", $hospitals));

    // رفع الصورة إن وجدت
    $profile_pic = $user['profile_pic'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $safe_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['profile_pic']['name']));
        $target_file = $target_dir . $safe_name;
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            $profile_pic = $target_file;
        }
    }

    // تحديث بيانات المستخدم (prepared statement)
    $upd = $conn->prepare("UPDATE users SET name=?, pronouns=?, specialization=?, phone=?, location=?, available_days=?, available_times=?, hospital_locations=?, profile_pic=? WHERE id=?");
    $upd->bind_param("sssssssssi", $name, $pronouns, $specialization, $phone, $location, $available_days_json, $available_times_json, $hospital_locations, $profile_pic, $edit_id);
    $upd->execute();

    // بعد الحفظ: لو الادمن بيعدل غيره نرجع لصفحة الدكتور اللي عدّلناه، والا نرجع لصفحتنا
    if ($viewer_role === 'admin' && $edit_id !== $viewer_id) {
        header("Location: doctor_profile.php?doctor_id=".$edit_id);
    } else {
        header("Location: doctor_profile.php");
    }
    exit();
}

// لتحضير الحقول المتعددة (عرض أولي)
$existing_hospitals = array_filter(array_map('trim', explode(',', $user['hospital_locations'] ?? '')));
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Doctor Profile</title>
<link rel="stylesheet" href="edit_doctor_profile.css">
</head>
<body>
<div class="profile-container">
  <h2>Edit Profile</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="profile-pic"><img src="<?php echo htmlspecialchars($user['profile_pic'] ?: 'images/pfp.png'); ?>" alt="Profile"></div>

    <label>Change Picture</label>
    <input type="file" name="profile_pic" accept="image/*">

    <label>Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

    <label>Pronouns</label>
    <input type="text" name="pronouns" value="<?php echo htmlspecialchars($user['pronouns'] ?? ''); ?>">

    <label>Specialization</label>
    <input type="text" name="specialization" value="<?php echo htmlspecialchars($user['specialization'] ?? ''); ?>">

    <label>Phone</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

    <label>Main Location</label>
    <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">

    <label>Hospital Locations</label>
    <div id="hospitalFields">
      <?php
        if (count($existing_hospitals) > 0) {
            $i=1;
            foreach ($existing_hospitals as $h) {
                echo '<input type="text" name="hospital_locations[]" value="'.htmlspecialchars($h).'" placeholder="Hospital '.$i.'">';
                $i++;
            }
        } else {
            echo '<input type="text" name="hospital_locations[]" placeholder="Hospital 1">';
        }
      ?>
    </div>
    <button type="button" id="addHospitalBtn" class="add-btn">+ Add Hospital</button>

    <label>Available Days</label>
    <div class="days-options">
        <?php 
            $days = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
            $selected_days = json_decode($user['available_days'] ?? '[]', true);
            if (!is_array($selected_days)) $selected_days = [];
            foreach ($days as $day) {
                $checked = in_array($day, $selected_days) ? 'checked' : '';
                echo "<label><input type='checkbox' name='available_days[]' value='$day' $checked> $day</label> ";
            }
        ?>
    </div>

    <label>Available Times</label>
<div id="timeSlotsContainer">
<?php
$days = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
$timeSlots = [];

if (!empty($user['available_times'])) {
    $decoded = json_decode($user['available_times'], true);
    if (is_array($decoded)) {
        $timeSlots = $decoded;
    }
}
if (empty($timeSlots)) {
    $timeSlots[] = ['day' => '', 'from' => '', 'to' => ''];
}

$index = 0;
foreach ($timeSlots as $slot) {
?>
  <div class="time-slot" style="margin-bottom:10px;">
    <select name="available_times[<?php echo $index; ?>][day]" required>
      <option value="">Select Day</option>
      <?php foreach ($days as $day): ?>
        <option value="<?php echo $day; ?>" <?php echo ($slot['day'] === $day) ? 'selected' : ''; ?>>
          <?php echo $day; ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>From</label>
    <input type="time" name="available_times[<?php echo $index; ?>][from]" value="<?php echo htmlspecialchars($slot['from']); ?>" required>

    <label>To</label>
    <input type="time" name="available_times[<?php echo $index; ?>][to]" value="<?php echo htmlspecialchars($slot['to']); ?>" required>

    <button type="button" class="delete-slot" style="background:red;color:white;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;">🗑</button>
  </div>
<?php
$index++;
}
?>
</div>

<button type="button" id="addTimeSlotBtn" class="add-btn">+ Add Time Slot</button>

    <button type="submit" class="edit-btn">Save Changes</button>
    <a href="doctor_profile.php" class="logout-btn">Cancel</a>
  </form>
</div>

<script>
const addBtn = document.getElementById('addHospitalBtn');
const hospitalContainer = document.getElementById('hospitalFields');
let hospitalCount = hospitalContainer.querySelectorAll('input').length || 1;

addBtn.addEventListener('click', ()=> {
  hospitalCount++;
  const ni = document.createElement('input');
  ni.type = 'text';
  ni.name = 'hospital_locations[]';
  ni.placeholder = 'Hospital ' + hospitalCount;
  hospitalContainer.appendChild(ni);
});

const addTimeSlotBtn = document.getElementById('addTimeSlotBtn');
const timeSlotsContainer = document.getElementById('timeSlotsContainer');
let slotCount = timeSlotsContainer.querySelectorAll('.time-slot').length;

// ✅ زرار الإضافة
addTimeSlotBtn.addEventListener('click', ()=> {
  const div = document.createElement('div');
  div.classList.add('time-slot');
  div.style.marginBottom = '10px';
  div.innerHTML = `
    <select name="available_times[${slotCount}][day]" required>
      <option value="">Select Day</option>
      <option value="Saturday">Saturday</option>
      <option value="Sunday">Sunday</option>
      <option value="Monday">Monday</option>
      <option value="Tuesday">Tuesday</option>
      <option value="Wednesday">Wednesday</option>
      <option value="Thursday">Thursday</option>
      <option value="Friday">Friday</option>
    </select>

    <label>From</label>
    <input type="time" name="available_times[${slotCount}][from]" required>

    <label>To</label>
    <input type="time" name="available_times[${slotCount}][to]" required>

    <button type="button" class="delete-slot" style="background:red;color:white;border:none;padding:4px 8px;border-radius:4px;cursor:pointer;">🗑</button>
  `;
  timeSlotsContainer.appendChild(div);
  slotCount++;
});

// ✅ زرار الحذف
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('delete-slot')) {
    e.target.parentElement.remove();
  }
});
</script>
</body>
</html>
