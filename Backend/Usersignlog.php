<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ------------------------------------------------------
   HANDLE SIGNUP
------------------------------------------------------ */
if (isset($_POST['signup'])) {

    $fname  = trim($_POST['fname']);
    $lname  = trim($_POST['lname']);
    $email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass   = $_POST['password'];
    $repass = $_POST['repassword'];
    $role   = $_POST['role'];
    $companyCode = $_POST['companyCode'] ?? null;

    // Required fields
    if (!$fname || !$lname || !$email || !$pass || !$repass || !$role) {
        die("<script>alert('Missing required fields'); window.location='../Frontend/User/user_signup.html';</script>");
    }

    // Password check
    if ($pass !== $repass) {
        die("<script>alert('Passwords do not match'); window.location='../Frontend/User/user_signup.html';</script>");
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT uid FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $dup = $check->get_result();

    if ($dup->num_rows > 0) {
        die("<script>alert('Email already exists'); window.location='../Frontend/User/user_signup.html';</script>");
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    /* ------------------------------------------------------
       VERIFY COMPANY CODE IF EMPLOYER
    ------------------------------------------------------ */
    $parentCid = NULL;

    if ($role === "CompanyEmployee") {

        if (!$companyCode) {
            die("<script>alert('Company Code required'); window.location='../Frontend/User/user_signup.html';</script>");
        }

        $codeStmt = $conn->prepare("SELECT comp_cid FROM company_codes WHERE code = ?");
        $codeStmt->bind_param("s", $companyCode);
        $codeStmt->execute();

        $found = $codeStmt->get_result();
        if ($found->num_rows === 0) {
            die("<script>alert('Invalid company code'); window.location='../Frontend/User/user_signup.html';</script>");
        }

        $parentCid = $found->fetch_assoc()['comp_cid'];
    }

    /* ------------------------------------------------------
       INSERT USER
    ------------------------------------------------------ */
    $stmt = $conn->prepare("
        INSERT INTO users (fname, lname, age, gender, email, password, phone_number, resume, photo, role)
        VALUES (?, ?, 0, 'Others', ?, ?, NULL, NULL, 'default.png', ?)
    ");

    // 3 string fields + hashed password + role → 5 params
    $stmt->bind_param("sssss", $fname, $lname, $email, $hashed, $role);
    $stmt->execute();

    /* ------------------------------------------------------
       GET USER ID
    ------------------------------------------------------ */
    $getUid = $conn->prepare("SELECT uid FROM users WHERE email = ?");
    $getUid->bind_param("s", $email);
    $getUid->execute();
    $uid = $getUid->get_result()->fetch_assoc()['uid'];

    $_SESSION['uid'] = $uid;

    /* ------------------------------------------------------
       EMPLOYER → INSERT INTO company_employees
    ------------------------------------------------------ */
    if ($role === "CompanyEmployee") {

        $insertCE = $conn->prepare("
            INSERT INTO company_employees (employee_uid, employee_cid)
            VALUES (?, ?)
        ");

        $insertCE->bind_param("ii", $uid, $parentCid);
        $insertCE->execute();

        header("Location: ../Frontend/Company/company_dashboard.html");
        exit;
    }

    /* ------------------------------------------------------
       JOB SEEKER → LOGIN PAGE
    ------------------------------------------------------ */
    header("Location: ../Frontend/User/login.html");
    exit;
}

/* ------------------------------------------------------
   If directly accessed
------------------------------------------------------ */
echo "Invalid request.";
exit;
?>
