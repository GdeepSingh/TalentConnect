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

$jaid = isset($_GET['jaid']) ? (int)$_GET['jaid'] : 0;
if ($jaid <= 0) {
    echo json_encode(["success" => false, "error" => "Missing application ID"]);
    exit;
}

try {
    // 1) employer → company id
    $q = $conn->prepare("SELECT employee_cid FROM company_employees WHERE employee_uid = ?");
    $q->bind_param("i", $uid);
    $q->execute();
    $res = $q->get_result();
    if ($res->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Not a company employee"]);
        exit;
    }
    $cid = (int)$res->fetch_assoc()['employee_cid'];

    // 2) join job_application + jobpost + company_employees + users
    $sql = "
      SELECT ja.jaid, ja.job_uid, ja.job_id, ja.status, ja.applied_at,
             j.title AS job_title, j.location AS job_location, j.jobtype,
             u.uid, u.fname, u.lname, u.email, u.phone_number, u.age, u.gender, u.resume, u.photo
      FROM job_application ja
      JOIN jobpost j ON j.pid = ja.job_id
      JOIN company_employees ce ON ce.ceid = j.emp_id
      JOIN users u ON u.uid = ja.job_uid
      WHERE ja.jaid = ? AND ce.employee_cid = ?
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("ii", $jaid, $cid);
    $st->execute();
    $rowRes = $st->get_result();
    if ($rowRes->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Application not found"]);
        exit;
    }

    $row = $rowRes->fetch_assoc();
    $userId = (int)$row['uid'];

    $uploadBase = "../Uploads/";
    $photoUrl  = $row['photo']  ? $uploadBase . $row['photo']  : null;
    $resumeUrl = $row['resume'] ? $uploadBase . $row['resume'] : null;

    // 3) work experience
    $exp = [];
    $se = $conn->prepare("
        SELECT job_title, company_name, location, start_date, end_date, description
        FROM work_experience
        WHERE user_id = ?
        ORDER BY start_date DESC
    ");
    $se->bind_param("i", $userId);
    $se->execute();
    $resExp = $se->get_result();
    while ($e = $resExp->fetch_assoc()) {
        $exp[] = $e;
    }

    // 4) education
    $edu = [];
    $se2 = $conn->prepare("
        SELECT school_name, degree, field_of_study, start_date, end_date
        FROM education
        WHERE user_id = ?
        ORDER BY start_date DESC
    ");
    $se2->bind_param("i", $userId);
    $se2->execute();
    $resEdu = $se2->get_result();
    while ($e2 = $resEdu->fetch_assoc()) {
        $edu[] = $e2;
    }

    // 5) skills (latest)
    $skills = null;
    if ($conn->query("SHOW TABLES LIKE 'skills'")->num_rows) {
        $sk = $conn->prepare("
            SELECT skill_name, languages 
            FROM skills 
            WHERE user_id = ? 
            ORDER BY sid DESC LIMIT 1
        ");
        $sk->bind_param("i", $userId);
        $sk->execute();
        $rsk = $sk->get_result();
        if ($rsk->num_rows) {
            $skills = $rsk->fetch_assoc();
        }
    }

    echo json_encode([
        "success" => true,
        "user" => [
            "uid"          => $userId,
            "fname"        => $row['fname'],
            "lname"        => $row['lname'],
            "email"        => $row['email'],
            "phone_number" => $row['phone_number'],
            "age"          => $row['age'],
            "gender"       => $row['gender'],
            "photo_url"    => $photoUrl,
            "resume_url"   => $resumeUrl
        ],
        "job" => [
            "job_id"   => (int)$row['job_id'],
            "title"    => $row['job_title'],
            "location" => $row['job_location'],
            "jobtype"  => $row['jobtype']
        ],
        "application" => [
            "jaid"       => (int)$row['jaid'],
            "status"     => $row['status'],
            "applied_at" => $row['applied_at']
        ],
        "experience" => $exp,
        "education"  => $edu,
        "skills"     => $skills
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}
