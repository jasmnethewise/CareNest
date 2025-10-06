<?php
session_start();
session_unset(); // يمسح كل بيانات السيشن
session_destroy(); // يدمر الجلسة نفسها
header("Location: index.html"); // يرجع المستخدم لصفحة تسجيل الدخول
exit();
?>