<?php
include("database.php");
session_start();

if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];


// Fetch all applications by current user
$sql = $conn->prepare("
    SELECT 
        ja.job_id,
        ja.status,
        ja.applied_at,
        jp.title AS job_title,
        c.company_name
    FROM job_application ja
    INNER JOIN jobpost jp ON ja.job_id = jp.pid
    INNER JOIN company_employees ce ON jp.emp_id = ce.ceid
    INNER JOIN companies c ON ce.employee_cid = c.cid
    WHERE ja.job_uid = ?
    ORDER BY ja.applied_at DESC
");
$sql->bind_param("i", $uid);
$sql->execute();
$result = $sql->get_result();

$applications = [];

while ($row = $result->fetch_assoc()) {
    $applications[] = [
        "company" => $row["company_name"],
        "title" => $row["job_title"],
        "status" => $row["status"],
        "applied" => $row["applied_at"]
    ];
}

echo json_encode([
    "success" => true,
    "applications" => $applications
]);
exit;
?>
