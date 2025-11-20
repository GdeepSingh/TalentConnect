<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    // Look up user
    $stmt = $conn->prepare("SELECT uid, password, role, fname, lname FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("<script>alert('Email not found'); window.location='../Frontend/User/login.html';</script>");
    }

    $user = $result->fetch_assoc();

    // Check password
    if (!password_verify($pass, $user['password'])) {
        die("<script>alert('Incorrect password'); window.location='../Frontend/User/login.html';</script>");
    }

    // Store session
    $_SESSION['uid']  = $user['uid'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['fname'] . " " . $user['lname'];

    // Redirect based on role
    if ($user['role'] === "CompanyAdmin") {
        // ADMIN → dashboard
        header("Location: ../Frontend/Company/company_dashboard.html");
        exit;
    } elseif ($user['role'] === "CompanyEmployee") {
        // EMPLOYER → manage jobs directly
        header("Location: ../Frontend/Company/manage_jobs.html");
        exit;
    } else {
        // JobSeeker
        header("Location: ../Frontend/User/user_dashboard.html");
        exit;
    }
}

echo "Invalid request";
exit;
?>
