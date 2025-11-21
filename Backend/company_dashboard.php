<?php
include("database.php");
session_start();

if (!isset($_SESSION['compID'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$cid = $_SESSION['compID'];

/* ---------------------------------------------------------
   1️⃣ Count employees in this company
--------------------------------------------------------- */
$q1 = $conn->prepare("SELECT COUNT(*) AS total FROM company_employees WHERE employee_cid = ?");
$q1->bind_param("i", $cid);
$q1->execute();
$employees = $q1->get_result()->fetch_assoc()['total'];

/* ---------------------------------------------------------
   2️⃣ Fetch all ceid under this company
--------------------------------------------------------- */
$q2 = $conn->prepare("SELECT ceid FROM company_employees WHERE employee_cid = ?");
$q2->bind_param("i", $cid);
$q2->execute();
$res2 = $q2->get_result();

$ceids = [];
while ($r = $res2->fetch_assoc()) {
    $ceids[] = $r['ceid'];
}

if (empty($ceids)) {
    echo json_encode([
        "success" => true,
        "employees" => $employees,
        "jobs" => [],
        "open_jobs" => 0,
        "applicants" => 0
    ]);
    exit;
}

$placeholders = implode(",", array_fill(0, count($ceids), "?"));
$types = str_repeat("i", count($ceids));


/* ---------------------------------------------------------
   3️⃣ Count open jobs (visibility = yes)
--------------------------------------------------------- */
$sql3 = "SELECT COUNT(*) AS total FROM jobpost WHERE visibility='yes' AND emp_id IN ($placeholders)";
$q3 = $conn->prepare($sql3);
$q3->bind_param($types, ...$ceids);
$q3->execute();
$open_jobs = $q3->get_result()->fetch_assoc()['total'];


/* ---------------------------------------------------------
   4️⃣ Fetch recent jobs
--------------------------------------------------------- */
$sql4 = "SELECT pid, title, location, jobtype FROM jobpost WHERE emp_id IN ($placeholders) ORDER BY pid DESC";
$q4 = $conn->prepare($sql4);
$q4->bind_param($types, ...$ceids);
$q4->execute();
$jobsRes = $q4->get_result();

$jobs = [];
while ($job = $jobsRes->fetch_assoc()) {
    $jobs[] = $job;
}


/* ---------------------------------------------------------
   5️⃣ Return JSON
--------------------------------------------------------- */
echo json_encode([
    "success" => true,
    "employees" => $employees,
    "open_jobs" => $open_jobs,
    "jobs" => $jobs,
    "applicants" => 0  // leave as you said
]);

exit;
?>
