<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}
$uid = $_SESSION['uid'];

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data['jaid']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

$jaid   = (int)$data['jaid'];
$status = $data['status'];

$allowed = ['accepted','waitlist','rejected','progress'];
if (!in_array($status, $allowed, true)) {
    echo json_encode(["success" => false, "error" => "Invalid status"]);
    exit;
}

try {
    // employer → company id
    $q = $conn->prepare("SELECT employee_cid FROM company_employees WHERE employee_uid = ?");
    $q->bind_param("i", $uid);
    $q->execute();
    $res = $q->get_result();
    if ($res->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Not a company employee"]);
        exit;
    }
    $cid = (int)$res->fetch_assoc()['employee_cid'];

    // update status only if application belongs to this company
    $sql = "
      UPDATE job_application ja
      JOIN jobpost j ON j.pid = ja.job_id
      JOIN company_employees ce ON ce.ceid = j.emp_id
      SET ja.status = ?
      WHERE ja.jaid = ? AND ce.employee_cid = ?
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("sii", $status, $jaid, $cid);
    $st->execute();

    if ($st->affected_rows === 0) {
        echo json_encode(["success" => false, "error" => "No record updated"]);
        exit;
    }

    echo json_encode(["success" => true]);
    exit;

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}
