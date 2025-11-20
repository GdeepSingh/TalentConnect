<?php
include("database.php");
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ---------- HANDLE SIGNUP ---------- */
if (isset($_POST['signup'])) {

    $fname  = trim($_POST['fname']);
    $lname  = trim($_POST['lname']);
    $email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass   = $_POST['password'];
    $repass = $_POST['repassword'];
    $role   = $_POST['role'];
    $companyCode = $_POST['companyCode'] ?? null;

    if (!$fname || !$lname || !$email || !$pass || !$role) {
        die("Missing required fields.");
    }

    if ($pass !== $repass) {
        die("Passwords do not match.");
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    /* ---------- IF EMPLOYER: VERIFY COMPANY CODE ---------- */
    $parentCid = NULL;

    if ($role === "CompanyEmployee") {

        if (!$companyCode) {
            die("Company code required.");
        }

        $codeStmt = $conn->prepare("
            SELECT comp_cid FROM company_codes WHERE code = ?
        ");
        $codeStmt->bind_param("s", $companyCode);
        $codeStmt->execute();
        $codeResult = $codeStmt->get_result();

        if ($codeResult->num_rows === 0) {
            die("Invalid company code.");
        }

        $parentCid = $codeResult->fetch_assoc()['comp_cid'];
    }

    /* ---------- INSERT USER INTO DATABASE ---------- */
    $stmt = $conn->prepare("
        INSERT INTO users (fname, lname, age, gender, email, password, phone_number, resume, photo, role)
        VALUES (?, ?, 0, 'Others', ?, ?, NULL, NULL, 'default.png', ?)
    ");

    $stmt->bind_param("sssss", $fname, $lname, $email, $hashed, $role);
    $stmt->execute();

    /* ---------- GET USER ID ---------- */
    $getUid = $conn->prepare("SELECT uid FROM users WHERE email = ?");
    $getUid->bind_param("s", $email);
    $getUid->execute();
    $uid = $getUid->get_result()->fetch_assoc()['uid'];

    $_SESSION['uid'] = $uid;


    /* ---------- STORE COMPANY EMPLOYEE (IF ROLE MATCHES) ---------- */
    if ($role === "CompanyEmployee") {

        $insertCE = $conn->prepare("
            INSERT INTO company_employees (employee_uid, employee_cid)
            VALUES (?, ?)
        ");
        $insertCE->bind_param("ii", $uid, $parentCid);
        $insertCE->execute();
    }

    /* ---------- REDIRECT ---------- */
    if ($role === "CompanyEmployee") {
        header("Location: ../Frontend/Company/company_dashboard.html");
        exit;
    } else {
        header("Location: ../Frontend/User/experience.html");
        exit;
    }
}


?>

