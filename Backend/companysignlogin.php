<?php

include("database.php");


// Enable exceptions for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();


//generate code so emplyer can join
function generateCompanyCode()
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // removed O, I, 0, 1
    $code = '';

    for ($i = 0; $i < 8; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}


if (isset($_POST['signup'])) {

    $owner_name = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $personal_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $company_email = filter_input(INPUT_POST, 'Compmail', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if ($owner_name && $personal_email && $password && $company_email) {

        // HASH THE PASSWORD
        $hashed = password_hash($password, PASSWORD_DEFAULT);

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

    } else {
        echo "<script>alert('Please fill in all fields correctly.');</script>";
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