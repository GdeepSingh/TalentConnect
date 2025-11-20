<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['uid'])) {
    die("Not logged in");
}

$uid = (int)$_SESSION['uid'];

/* ---------- TEXT FIELDS ---------- */
$fname    = trim($_POST["fname"] ?? "");
$lname    = trim($_POST["lname"] ?? "");
$age      = isset($_POST["age"]) && $_POST["age"] !== "" ? (int)$_POST["age"] : 0;
$gender   = trim($_POST["gender"] ?? "");
$email    = trim($_POST["email"] ?? "");
$phone    = trim($_POST["phone_number"] ?? "");
$role     = trim($_POST["role"] ?? "");
$password = $_POST["password"] ?? "";

/* ---------- UPDATE BASIC INFO ---------- */
if ($password === "") {
    // keep old password
    $stmt = $conn->prepare("
        UPDATE users
        SET fname = ?, lname = ?, age = ?, gender = ?, email = ?, phone_number = ?, role = ?
        WHERE uid = ?
    ");
    $stmt->bind_param("ssissssi",
        $fname,
        $lname,
        $age,
        $gender,
        $email,
        $phone,
        $role,
        $uid
    );
} else {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("
        UPDATE users
        SET fname = ?, lname = ?, age = ?, gender = ?, email = ?, phone_number = ?, role = ?, password = ?
        WHERE uid = ?
    ");
    $stmt->bind_param("ssisssssi",
        $fname,
        $lname,
        $age,
        $gender,
        $email,
        $phone,
        $role,
        $hashed,
        $uid
    );
}
$stmt->execute();

/* ---------- FILE UPLOAD HELPERS ---------- */
$uploadDir = __DIR__ . "/../Frontend/Uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* ---------- PHOTO UPLOAD ---------- */
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK) {

    $tmpName  = $_FILES["photo"]["tmp_name"];
    $origName = basename($_FILES["photo"]["name"]);
    $ext      = pathinfo($origName, PATHINFO_EXTENSION);

    // basic safety
    $ext = strtolower($ext);
    if (in_array($ext, ["jpg", "jpeg", "png", "gif", "webp"])) {
        $fileName = "IMG_" . $uid . "_" . time() . "." . $ext;
        $target   = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $target)) {
            // store only filename in DB
            $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE uid = ?");
            $stmt->bind_param("si", $fileName, $uid);
            $stmt->execute();
        }
    }
}

/* ---------- RESUME UPLOAD ---------- */
if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] === UPLOAD_ERR_OK) {

    $tmpName  = $_FILES["resume"]["tmp_name"];
    $origName = basename($_FILES["resume"]["name"]);
    $ext      = pathinfo($origName, PATHINFO_EXTENSION);

    $ext = strtolower($ext);
    if ($ext === "pdf") {
        $fileName = "CV_" . $uid . "_" . time() . "." . $ext;
        $target   = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $target)) {
            $stmt = $conn->prepare("UPDATE users SET resume = ? WHERE uid = ?");
            $stmt->bind_param("si", $fileName, $uid);
            $stmt->execute();
        }
    }
}

/* ---------- DONE ---------- */
echo "<script>alert('Profile Updated!'); window.location.href='../Frontend/User/user_profile.html';</script>";
exit;
