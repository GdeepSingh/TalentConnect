<?php
include("database.php");
session_start();
header("Content-Type: application/json");

if (!isset($_GET['job'])) {
    echo json_encode(["success" => false, "error" => "Missing job id"]);
    exit;
}

$jobId = intval($_GET['job']);

$q = $conn->prepare("SELECT * FROM jobpost WHERE pid = ?");
$q->bind_param("i", $jobId);
$q->execute();
$res = $q->get_result();

if ($res->num_rows == 0) {
    echo json_encode(["success" => false, "error" => "Job not found"]);
    exit;
}

echo json_encode([
    "success" => true,
    "job" => $res->fetch_assoc()
]);
exit;
?>
