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

try {
    // 1) Which company does this employer belong to?
    $q = $conn->prepare("SELECT employee_cid FROM company_employees WHERE employee_uid = ?");
    $q->bind_param("i", $uid);
    $q->execute();
    $res = $q->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Not a company employee"]);
        exit;
    }

    $row = $res->fetch_assoc();
    $cid = (int)$row['employee_cid'];

    // 2) Get all employees (ceid) in this company
    $q2 = $conn->prepare("SELECT ceid FROM company_employees WHERE employee_cid = ?");
    $q2->bind_param("i", $cid);
    $q2->execute();
    $res2 = $q2->get_result();

    $ceids = [];
    while ($r = $res2->fetch_assoc()) {
        $ceids[] = (int)$r['ceid'];
    }

    if (empty($ceids)) {
        echo json_encode(["success" => true, "jobs" => []]);
        exit;
    }

    $placeholders = implode(",", array_fill(0, count($ceids), "?"));
    $types = str_repeat("i", count($ceids));

    // 3) Get jobs for this company
    $sqlJobs = "
        SELECT pid, title, location, jobtype
        FROM jobpost
        WHERE emp_id IN ($placeholders)
        ORDER BY pid DESC
    ";
    $sj = $conn->prepare($sqlJobs);
    $sj->bind_param($types, ...$ceids);
    $sj->execute();
    $jobsRes = $sj->get_result();

    $jobs = [];

    while ($job = $jobsRes->fetch_assoc()) {
        $jobId = (int)$job['pid'];

        // 4) Applicants for each job
        $sa = $conn->prepare("
            SELECT ja.jaid, ja.job_uid, ja.status, ja.applied_at,
                   u.fname, u.lname, u.email
            FROM job_application ja
            JOIN users u ON u.uid = ja.job_uid
            WHERE ja.job_id = ?
            ORDER BY ja.applied_at DESC, ja.jaid DESC
        ");
        $sa->bind_param("i", $jobId);
        $sa->execute();
        $appsRes = $sa->get_result();

        $apps = [];
        while ($a = $appsRes->fetch_assoc()) {
            $apps[] = [
                "jaid"       => (int)$a['jaid'],
                "user_id"    => (int)$a['job_uid'],
                "full_name"  => $a['fname'] . " " . $a['lname'],
                "email"      => $a['email'],
                "status"     => $a['status'],
                "applied_at" => $a['applied_at'],
            ];
        }

        $jobs[] = [
            "job_id"     => $jobId,
            "title"      => $job['title'],
            "location"   => $job['location'],
            "jobtype"    => $job['jobtype'],
            "applicants" => $apps,
        ];
    }

    echo json_encode([
        "success" => true,
        "jobs"    => $jobs
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}
