<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* HANDLE SIGNUP */
if (isset($_POST['signup'])) {

    $fullname = trim($_POST['fullname']);
    $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass     = $_POST['password'];
    $repass   = $_POST['repassword'];
    $role     = $_POST['role'];
    $companyCode = $_POST['companyCode'] ?? null;

    if (!$fullname || !$email || !$pass || !$repass || !$role) {
        die("Missing required fields.");
    }

    if ($pass !== $repass) {
        die("Passwords do not match.");
    }

    // Split fullname
    $nameParts = explode(" ", $fullname, 2);
    $fname = $nameParts[0];
    $lname = $nameParts[1] ?? "";

    // Check duplicate email
    $check = $conn->prepare("SELECT uid FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $dup = $check->get_result();

    if ($dup->num_rows > 0) {
        die("<script>alert('Email already exists'); window.location='../Frontend/User/signup.html';</script>");
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    /* ---------- VERIFY COMPANY CODE IF EMPLOYER ---------- */
    $parentCid = NULL;

    if ($role === "CompanyEmployee") {

        if (!$companyCode) {
            die("Company code required.");
        }

        $codeStmt = $conn->prepare("SELECT comp_cid FROM company_codes WHERE code = ?");
        $codeStmt->bind_param("s", $companyCode);
        $codeStmt->execute();

        $found = $codeStmt->get_result();
        if ($found->num_rows === 0) {
            die("Invalid company code.");
        }

        $parentCid = $found->fetch_assoc()['comp_cid'];
    }

    /* ---------- INSERT USER ---------- */
    $stmt = $conn->prepare("
        INSERT INTO users (fname, lname, age, gender, email, password, phone_number, resume, photo, role)
        VALUES (?, ?, 0, 'Others', ?, ?, NULL, NULL, 'default.png', ?)
    ");

    // exactly 4 variables are bound
    $stmt->bind_param("ssss", $fname, $lname, $email, $hashed, $role);
    // ❗ FIX: The above line is WRONG (you have 4 parameters but 5 values)
    // Need 5 parameters including role

    $stmt->bind_param("sssss", $fname, $lname, $email, $hashed, $role);

    $stmt->execute();

    /* GET USER ID */
    $getUid = $conn->prepare("SELECT uid FROM users WHERE email = ?");
    $getUid->bind_param("s", $email);
    $getUid->execute();
    $uid = $getUid->get_result()->fetch_assoc()['uid'];

    $_SESSION['uid'] = $uid;

    /* ---------- EMPLOYER: ADD EMPLOYEE ---------- */
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

    /* ---------- JOB SEEKER REDIRECT ---------- */
    header("Location: ../Frontend/User/login.html");
    exit;
}

/* Direct access */
echo "Invalid request.";
exit;
?>
