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
/* ---------- FILE UPLOADS (resume + photo) ---------- */

// Directory: Backend/../Uploads/  (same folder used in get_user_profile.php)
$uploadDir = __DIR__ . "/../Uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* Resume upload (PDF) */
if (!empty($_FILES['resume']['name']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
    if ($ext === 'pdf') {
        $newName = "resume_{$uid}_" . time() . ".pdf";
        $target  = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $target)) {
            $stmtR = $conn->prepare("UPDATE users SET resume = ? WHERE uid = ?");
            $stmtR->bind_param("si", $newName, $uid);
            $stmtR->execute();
        }
    }
}

/* Profile photo upload (image) */
if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];

    if (in_array($ext, $allowed)) {
        $newName = "photo_{$uid}_" . time() . "." . $ext;
        $target  = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $stmtP = $conn->prepare("UPDATE users SET photo = ? WHERE uid = ?");
            $stmtP->bind_param("si", $newName, $uid);
            $stmtP->execute();
        }
    }
}


echo "<script>alert('Profile Updated!'); window.location.href='../Frontend/User/user_profile.html';</script>";
exit;
?>
