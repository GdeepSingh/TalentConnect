<?php
include("database.php");
session_start();

if (!isset($_SESSION['compID'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$cid = $_SESSION['compID'];

/* ---------------------------------------------------------
   1️⃣ COUNT EMPLOYEES
--------------------------------------------------------- */
$q1 = $conn->prepare("SELECT COUNT(*) AS total 
                      FROM company_employees 
                      WHERE employee_cid = ?");
$q1->bind_param("i", $cid);
$q1->execute();
$employees = $q1->get_result()->fetch_assoc()['total'];

/* ---------------------------------------------------------
   2️⃣ GET ALL EMPLOYEES ceid FOR THIS COMPANY
--------------------------------------------------------- */
$q2 = $conn->prepare("SELECT ceid 
                      FROM company_employees 
                      WHERE employee_cid = ?");
$q2->bind_param("i", $cid);
$q2->execute();
$r2 = $q2->get_result();

$ceids = [];
while ($row = $r2->fetch_assoc()) {
    $ceids[] = $row['ceid'];
}

if (empty($ceids)) {
    echo json_encode([
        "success" => true,
        "employees" => $employees,
        "open_jobs" => 0,
        "applicants" => 0,
        "jobs" => [],
        "latest_applicants" => []
    ]);
    exit;
}

$placeholders = implode(",", array_fill(0, count($ceids), "?"));
$types = str_repeat("i", count($ceids));

/* ---------------------------------------------------------
   3️⃣ OPEN JOBS COUNT
--------------------------------------------------------- */
$sql3 = "SELECT COUNT(*) AS total FROM jobpost 
         WHERE visibility = 'yes' AND emp_id IN ($placeholders)";
$q3 = $conn->prepare($sql3);
$q3->bind_param($types, ...$ceids);
$q3->execute();
$open_jobs = $q3->get_result()->fetch_assoc()['total'];

/* ---------------------------------------------------------
   4️⃣ FETCH RECENT JOBS
--------------------------------------------------------- */
$sql4 = "SELECT pid, title, location, jobtype 
         FROM jobpost 
         WHERE emp_id IN ($placeholders)
         ORDER BY pid DESC LIMIT 10";

$q4 = $conn->prepare($sql4);
$q4->bind_param($types, ...$ceids);
$q4->execute();
$r4 = $q4->get_result();

$jobs = [];
while ($job = $r4->fetch_assoc()) {
    $jobs[] = $job;
}

/* ---------------------------------------------------------
   5️⃣ LATEST APPLICANTS (JOIN users + jobpost + job_application)
--------------------------------------------------------- */
$sql5 = "
SELECT 
    u.fname, u.lname,
    jp.title,
    ja.status,
    ja.applied_at
FROM job_application ja
JOIN users u ON u.uid = ja.job_uid
JOIN jobpost jp ON jp.pid = ja.job_id
WHERE jp.emp_id IN ($placeholders)
ORDER BY ja.jaid DESC
LIMIT 10;
";

$q5 = $conn->prepare($sql5);
$q5->bind_param($types, ...$ceids);
$q5->execute();
$r5 = $q5->get_result();

$latest_applicants = [];
while ($row = $r5->fetch_assoc()) {
    $latest_applicants[] = $row;
}

/* ---------------------------------------------------------
   6️⃣ TOTAL APPLICANT COUNT
--------------------------------------------------------- */
$q6 = $conn->prepare("
SELECT COUNT(*) AS total 
FROM job_application ja
JOIN jobpost jp ON jp.pid = ja.job_id
WHERE jp.emp_id IN ($placeholders)
");
$q6->bind_param($types, ...$ceids);
$q6->execute();
$applicants = $q6->get_result()->fetch_assoc()['total'];

/* ---------------------------------------------------------
   SEND JSON
--------------------------------------------------------- */
echo json_encode([
    "success" => true,
    "employees" => $employees,
    "open_jobs" => $open_jobs,
    "applicants" => $applicants,
    "jobs" => $jobs,
    "latest_applicants" => $latest_applicants
]);
exit;
?>
