<?php
include("database.php");
session_start();

if (!isset($_SESSION["uid"])) {
    die(json_encode(["error" => "Not logged in"]));
}

$jobId = $_GET["job"];

$stmt = $conn->prepare("SELECT * FROM jobpost WHERE pid = ?");
$stmt->bind_param("i", $jobId);
$stmt->execute();

$res = $stmt->get_result();
$job = $res->fetch_assoc();

echo json_encode($job);
