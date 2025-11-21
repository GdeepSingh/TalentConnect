<?php
include("database.php");
session_start();

if (!isset($_SESSION["uid"])) {
    echo json_encode(["success" => false]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$jobId = $data["job_id"];

$stmt = $conn->prepare("DELETE FROM jobpost WHERE pid = ?");
$stmt->bind_param("i", $jobId);
$stmt->execute();

echo json_encode(["success" => true]);
exit;
