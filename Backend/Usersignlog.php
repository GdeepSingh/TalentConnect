<?php

include("database.php");


// Enable exceptions for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();




if (isset($_POST['signup'])) {

    /* ============================================
   GET FORM DATA
============================================ */
    $fullname = filter_input(INPUT_POST, "fullname", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $re_pass = $_POST["repassword"];
    $role = $_POST["role"];
    $companyCode = $_POST["companyCode"];

    // Basic validation
    if (!$fullname || !$email || !$password || !$role) {
        die("Invalid form input.");
    }


    // HASH THE PASSWORD
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    /* ============================================
            CHECK IF EMAIL ALREADY EXISTS
    ============================================ */
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        echo "<script>alert('Email already exists.'); window.location='../Frontend/User/user_signup.html';</script>";
        exit;
    }

    // default company_name placeholder
    $company_name = $owner_name . "'s Company";

    try {
        // PREPARED INSERT QUERY
        $sql = $conn->prepare("
                INSERT INTO companies (
                    company_name, email, password, description,
                    industry, hq_city, owner_name, owner_email,
                    banner, logo, parent_cid
                )
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");

        // EMPTY FIELDS
        $empty = "";
        $null = NULL;

        $sql->bind_param(
            "ssssssssssi",
            $company_name,   // company_name
            $company_email,          // email (login)
            $hashed,         // password
            $empty,          // description
            $empty,          // industry
            $empty,          // hq_city
            $owner_name,     // owner_name
            $personal_email,          // owner_email
            $empty,          // banner
            $empty,          // logo
            $null            // parent_cid
        );

        $sql->execute();


        /* 2️⃣ FETCH COMPANY USING EMAIL */
        $sqlCheck = $conn->prepare("SELECT cid FROM companies WHERE email = ?");
        $sqlCheck->bind_param("s", $company_email);
        $sqlCheck->execute();

        $result = $sqlCheck->get_result();

        if ($result->num_rows === 0) {
            echo "<script>alert('Error fetching company ID');</script>";
            exit;
        }

        $row = $result->fetch_assoc();
        $cid = $row['cid'];   // <-- THIS IS WHAT YOU WANT


        /*code generate for company*/
        $code = generateCompanyCode();

        $sql2 = $conn->prepare("
                INSERT INTO company_codes (code, comp_cid)
                VALUES (?, ?)
            ");
        $sql2->bind_param("si", $code, $cid);
        $sql2->execute();

        // SUCCESS → REDIRECT
        header("Location: ../Frontend/Company/company_login.html");
        exit;

    } catch (mysqli_sql_exception $e) {

        if (str_contains($e->getMessage(), "Duplicate entry")) {
            echo "<script>alert('Email already exists. Please use another.');</script>";
        } else {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }


}




if (isset($_POST['login'])) {

    $company_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass = $_POST['password'];

    if ($company_email && $pass) {

        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        try {

            //creqating the query and applying the query
            $sql = $conn->prepare("SELECT * FROM companies WHERE email = ?");
            $sql->bind_param("s", $company_email);
            $sql->execute();

            $result = $sql->get_result();

            if ($result->num_rows === 0) {
                echo "<script>alert('No company account found');</script>";
                exit;
            }

            $row = $result->fetch_assoc();

            // 🔥 CORRECT PASSWORD CHECK
            if (!password_verify($pass, $row['password'])) {
                echo "<script>alert('Wrong password');</script>";
                header("Location: ../Frontend/Company/company_login.html");
                exit;
            }

            // SUCCESS LOGIN
            $_SESSION['compID'] = $row['cid'];
            header("Location: ../Frontend/Company/company_dashboard.html");
            exit;



        } catch (mysqli_sql_exception $e) {
            //to check if there is user or not
            echo "<script defer>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}






?>