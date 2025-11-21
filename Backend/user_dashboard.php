<?php
include("database.php");
session_start();

if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];

/* ---------------------------------------------------------
   1️⃣ GET ALL JOBS THE USER HAS ALREADY APPLIED TO
--------------------------------------------------------- */
$q1 = $conn->prepare("SELECT job_id FROM job_application WHERE job_uid = ?");
$q1->bind_param("i", $uid);
$q1->execute();
$appliedRes = $q1->get_result();

$appliedJobs = [];
while ($row = $appliedRes->fetch_assoc()) {
    $appliedJobs[] = $row['job_id'];
}

/* Build the SQL exclusion list */
$excludeSql = "";
if (!empty($appliedJobs)) {
    $ids = implode(",", array_map('intval', $appliedJobs));
    $excludeSql = "WHERE pid NOT IN ($ids)";
}

/* ---------------------------------------------------------
   2️⃣ FETCH ALL JOBS THE USER HAS NOT APPLIED TO
--------------------------------------------------------- */
$query = "SELECT pid, title, location, jobtype FROM jobpost $excludeSql ORDER BY pid DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$jobsRes = $stmt->get_result();

$jobs = [];
while ($row = $jobsRes->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode([
    "success" => true,
    "jobs" => $jobs
]);
exit;
?>
