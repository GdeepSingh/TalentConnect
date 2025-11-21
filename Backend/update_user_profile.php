<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['uid'])) {
    die("Not logged in");
}

$uid = (int)$_SESSION['uid'];

/* get old email and role so they don't get overwritten */
$stmt0 = $conn->prepare("SELECT email, role FROM users WHERE uid = ?");
$stmt0->bind_param("i", $uid);
$stmt0->execute();
$row = $stmt0->get_result()->fetch_assoc();

$fixedEmail = $row['email'];
$fixedRole  = $row['role'];

/* safe fields */
$fname  = trim($_POST["fname"] ?? "");
$lname  = trim($_POST["lname"] ?? "");
$age    = isset($_POST["age"]) ? (int)$_POST["age"] : 0;
$gender = trim($_POST["gender"] ?? "");
$phone  = trim($_POST["phone_number"] ?? "");

/* update user without email/password */
$stmt = $conn->prepare("
    UPDATE users
    SET fname = ?, lname = ?, age = ?, gender = ?, phone_number = ?, role = ?
    WHERE uid = ?
");
$stmt->bind_param("ssisssi",
    $fname,
    $lname,
    $age,
    $gender,
    $phone,
    $fixedRole,
    $uid
);
$stmt->execute();

/* FILE UPLOAD CODE SAME AS BEFORE ... */

echo "<script>alert('Profile Updated!'); window.location.href='../Frontend/User/user_profile.html';</script>";
exit;
?>
