<?php
include("database.php");
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];

if (!isset($_GET['job'])) {
    echo json_encode(["success" => false, "error" => "Missing job id"]);
    exit;
}

$jobId = intval($_GET['job']);

/* ---------------------------------------------------------
   1️⃣ CHECK IF JOB EXISTS
--------------------------------------------------------- */
$q = $conn->prepare("SELECT * FROM jobpost WHERE pid = ?");
$q->bind_param("i", $jobId);
$q->execute();
$res = $q->get_result();

if ($res->num_rows == 0) {
    echo json_encode(["success" => false, "error" => "Job not found"]);
    exit;
}

/* ---------------------------------------------------------
   2️⃣ CHECK IF USER PROFILE IS COMPLETE
   - Resume uploaded OR
   - Work + Education + Skills all exist
--------------------------------------------------------- */
$ready = false;

/* Resume check */
$r = $conn->prepare("SELECT resume FROM users WHERE uid = ?");
$r->bind_param("i", $uid);
$r->execute();
$resume = $r->get_result()->fetch_assoc();

$hasResume = (!empty($resume['resume_url']));

/* Work experience */
$w = $conn->prepare("SELECT COUNT(*) AS c FROM work_experience WHERE user_id = ?");
$w->bind_param("i", $uid);
$w->execute();
$work = $w->get_result()->fetch_assoc();

/* Education */
$e = $conn->prepare("SELECT COUNT(*) AS c FROM education WHERE user_id = ?");
$e->bind_param("i", $uid);
$e->execute();
$edu = $e->get_result()->fetch_assoc();

/* Skills */
$s = $conn->prepare("SELECT COUNT(*) AS c FROM skills WHERE user_id = ?");
$s->bind_param("i", $uid);
$s->execute();
$skills = $s->get_result()->fetch_assoc();

/* Must have resume OR all three filled */
if ($hasResume || ($work['c'] > 0 && $edu['c'] > 0 && $skills['c'] > 0)) {
    $ready = true;
}

if (!$ready) {
    echo json_encode([
        "success" => false,
        "error" => "Profile incomplete",
        "msg" => "Upload resume OR complete Work, Education, and Skills."
    ]);
    exit;
}

/* ---------------------------------------------------------
   3️⃣ PREVENT DUPLICATE APPLICATIONS
--------------------------------------------------------- */
$chk = $conn->prepare("SELECT jaid FROM job_application WHERE job_uid = ? AND job_id = ?");
$chk->bind_param("ii", $uid, $jobId);
$chk->execute();
$dupe = $chk->get_result();

if ($dupe->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Already applied"]);
    exit;
}

/* ---------------------------------------------------------
   4️⃣ INSERT APPLICATION
--------------------------------------------------------- */
$ins = $conn->prepare("
    INSERT INTO job_application (job_uid, job_id, status, applied_at)
    VALUES (?, ?, 'progress', NOW())
");
$ins->bind_param("ii", $uid, $jobId);
$ins->execute();

/* ---------------------------------------------------------
   5️⃣ SUCCESS
--------------------------------------------------------- */
echo json_encode(["success" => true, "redirect" => "../../Frontend/User/job_application.html"]);
exit;
?>
