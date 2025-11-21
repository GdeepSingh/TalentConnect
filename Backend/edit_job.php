<?php
include("database.php");
session_start();

if (!isset($_SESSION['uid'])) {
    die("Not Logged In");
}

$job_id       = $_POST["job_id"];
$title        = $_POST["title"];
$location     = $_POST["location"];
$jobtype      = $_POST["job_type"];
$min_salary   = $_POST["min_salary"];
$max_salary   = $_POST["max_salary"];
$qualification = $_POST["qualification"];
$description   = $_POST["description"];
$visibility    = $_POST["visibility"];

$stmt = $conn->prepare("
    UPDATE jobpost SET 
        title=?, location=?, jobtype=?, 
        min_salary=?, max_salary=?, 
        qualification=?, description=?, 
        visibility=?
    WHERE pid=?
");

$stmt->bind_param(
    "ssssssssi",
    $title, $location, $jobtype,
    $min_salary, $max_salary,
    $qualification, $description,
    $visibility, $job_id
);

$stmt->execute();

echo "<script>alert('Job updated!'); window.location='../Frontend/Company/manage_jobs.html';</script>";
exit;
