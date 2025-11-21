<?php


include("database.php");
session_start();
$id = $_SESSION['uid'];

// Enable exceptions for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST['post'])) {

    $title = $_POST['title'];
    $location = $_POST['location'];
    $jobtype = $_POST['job_type'];
    $minSalary = $_POST['min_salary'];
    $maxSalary = $_POST['max_salary'];
    $qualification = $_POST['qualification'];
    $description = $_POST['description'];



    if ($title && $location && $jobtype && $minSalary && $maxSalary && $qualification && $description) {

        /**
         * collecting the company id of the user who is posting job
         * then adding those info into the jobpost database
         */

        $s = $conn->prepare("SELECT ceid FROM company_employees WHERE employee_uid = ?");
        $s->bind_param("i", $id);
        $s->execute();
        $res = $s->get_result();

        if ($res->num_rows == 0) {
            die("<script>alert('Email not found'); window.location='../Frontend/User/login.html';</script>");
        }

        $emp = $res->fetch_assoc();
        $empID = $emp['ceid'];



        $sql = $conn->prepare("INSERT INTO jobpost 
        (emp_id, title, location, jobtype, min_salary, max_salary, qualification, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $sql->bind_param("isssssss", $empID, $title, $location, $jobtype, $minSalary, $maxSalary, $qualification, $description);
        $sql->execute();

        header("Location: ../Frontend/Company/manage_jobs.html");
        exit;

    }
}
?>