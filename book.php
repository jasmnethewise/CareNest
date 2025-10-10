<?php
include 'config.php';
session_start();

if (!isset($_GET['doctor_id'])) {
    header("Location: services.php");
    exit();
}

$doctor_id = $_GET['doctor_id'];
$query = "SELECT name, profile_pic, specialization, phone, location, available_times, hospital_locations 
          FROM users 
          WHERE id = '$doctor_id'";
$result = mysqli_query($conn, $query);
$doctor = mysqli_fetch_assoc($result);

if (!$doctor) {
    echo "Doctor not found.";
    exit();
}

$profile_pic = !empty($doctor['profile_pic']) ? $doctor['profile_pic'] : 'images/pfp.png';

// فك الشيفرة بتاعة المواعيد
$available_times = !empty($doctor['available_times']) ? json_decode($doctor['available_times'], true) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="book.css">
</head>
<body>

    <div class="book-container">
        <div class="doctor-card">
            
            <div class="left-side">
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Doctor Picture" class="doctor-pic">
                <div class="doctor-info">
                    <h2><?php echo htmlspecialchars($doctor['name']); ?></h2>
                    <p class="specialization"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                </div>
            </div>

            <div class="right-side">
                <p><strong>📞 Phone:</strong> <?php echo htmlspecialchars($doctor['phone']); ?></p>
                <p><strong>📍 Location:</strong> <?php echo htmlspecialchars($doctor['location']); ?></p>

                <div class="btn-group">
                    <a href="#" class="book-btn online" id="openOnlineFormBtn">Online Consultation</a>
                    <a href="#" class="book-btn inperson" id="openInPersonFormBtn">In-Person Visit</a>
                </div>

                <!-- Modal -->
                <div class="modal" id="bookingModal">
                    <div class="modal-content">
                        <span class="close-btn" id="closeFormBtn">&times;</span>
                        <h2 id="formTitle">Book Appointment</h2>
                        <form action="confirm_booking.php" method="POST">
                            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                            <input type="hidden" name="appointment_type" id="appointment_type" value="">

                            <!-- اختيار اليوم -->
                            <label for="day">Choose Day:</label>
                            <select name="day" id="day" required>
                                <option value="">Select...</option>
                                <?php
                                    if (!empty($available_times)) {
                                        $days = array_unique(array_column($available_times, 'day'));
                                        foreach ($days as $day) {
                                            echo "<option value='" . htmlspecialchars($day) . "'>$day</option>";
                                        }
                                    } else {
                                        echo "<option disabled>No days set by doctor</option>";
                                    }
                                ?>
                            </select>

                            <!-- اختيار الوقت -->
                            <label for="time">Choose Time:</label>
                            <select name="time" id="time" required>
                                <option value="">Select day first...</option>
                            </select>

                            <!-- اختيار المكان -->
                            <div id="locationField" style="display:none;">
                                <label for="location">Select Location:</label>
                                <select name="location" id="location">
                                    <option value="">Select...</option>
                                    <?php
                                    if (!empty($doctor['hospital_locations'])) {
                                        $hospitals = array_filter(array_map('trim', explode(',', $doctor['hospital_locations'])));
                                        foreach ($hospitals as $hospital) {
                                            $clean = htmlspecialchars($hospital);
                                            echo "<option value='$clean'>$clean</option>";
                                        }
                                    } else {
                                        echo "<option disabled>No hospitals added by doctor</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- وسيلة التواصل -->
                            <div id="methodField">
                                <label for="contact">Contact Method:</label>
                                <select name="contact" id="contact">
                                    <option value="">Select...</option>
                                    <option>Phone Call</option>
                                    <option>WhatsApp</option>
                                    <option>Zoom</option>
                                </select>
                            </div>

                            <button type="submit" class="submit-btn">Confirm Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
const modal = document.getElementById('bookingModal');
const closeBtn = document.getElementById('closeFormBtn');
const onlineBtn = document.getElementById('openOnlineFormBtn');
const inPersonBtn = document.getElementById('openInPersonFormBtn');
const appointmentType = document.getElementById('appointment_type');
const locationField = document.getElementById('locationField');
const methodField = document.getElementById('methodField');

// بيانات الأوقات من PHP
const availableTimes = <?php echo json_encode($available_times); ?>;

// لما المستخدم يختار يوم
const daySelect = document.getElementById('day');
const timeSelect = document.getElementById('time');

daySelect.addEventListener('change', function() {
    const selectedDay = this.value;
    timeSelect.innerHTML = '<option value="">Select...</option>';

    const slots = availableTimes.filter(slot => slot.day.trim().toLowerCase() === selectedDay.trim().toLowerCase());
    if (slots.length > 0) {
        const slot = slots[0]; // Assuming one slot per day
        const fromTime = slot.from;
        const toTime = slot.to;

        // توليد أوقات بين from و to كل نص ساعة
        let start = new Date(`1970-01-01T${fromTime}:00`);
        let end = new Date(`1970-01-01T${toTime}:00`);
        while (start < end) {
            const timeStr = start.toTimeString().slice(0,5);
            const option = document.createElement('option');
            option.value = timeStr;
            option.textContent = timeStr;
            timeSelect.appendChild(option);
            start.setMinutes(start.getMinutes() + 30);
        }
    } else {
        const option = document.createElement('option');
        option.textContent = 'No available times for this day';
        option.disabled = true;
        timeSelect.appendChild(option);
    }
});



function openModal(type) {
    appointmentType.value = type;
    modal.style.display = 'flex';
    if (type === 'inperson') {
        locationField.style.display = 'block';
        methodField.style.display = 'none';
    } else {
        locationField.style.display = 'none';
        methodField.style.display = 'block';
    }
}

onlineBtn.onclick = function(e) {
    e.preventDefault();
    openModal('online');
};

inPersonBtn.onclick = function(e) {
    e.preventDefault();
    openModal('inperson');
};

closeBtn.onclick = function() {
    modal.style.display = 'none';
};

window.onclick = function(e) {
    if (e.target == modal) {
        modal.style.display = 'none';
    }
};
</script>

</body>
</html>
