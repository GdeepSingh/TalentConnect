<?php
session_start();
include("database.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = (int)$_SESSION['uid'];

try {
    /* ---------- USER BASIC INFO ---------- */
    $stmt = $conn->prepare("
        SELECT uid, fname, lname, age, gender, email, phone_number, resume, photo, role
        FROM users
        WHERE uid = ?
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $userRes = $stmt->get_result();
    $user = $userRes->fetch_assoc();

    if (!$user) {
        echo json_encode(["success" => false, "error" => "User not found"]);
        exit;
    }

    // Build URLs for frontend (user_profile.html is in Frontend/User)
    $photoFile = $user['photo'] ?: 'user_dummy.png';
    $user['photo_url'] = "../Uploads/" . $photoFile;

    if (!empty($user['resume'])) {
        $user['resume_url'] = "../Uploads/" . $user['resume'];
    } else {
        $user['resume_url'] = null;
    }

    /* ---------- EXPERIENCE ---------- */
    $exp = [];
    $stmt2 = $conn->prepare("
        SELECT job_title, company_name, location, start_date, end_date, description
        FROM work_experience
        WHERE user_id = ?
        ORDER BY start_date DESC
    ");
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($row = $res2->fetch_assoc()) {
        $exp[] = $row;
    }

    /* ---------- EDUCATION ---------- */
    $edu = [];
    $stmt3 = $conn->prepare("
        SELECT school_name, degree, field_of_study, start_date, end_date
        FROM education
        WHERE user_id = ?
        ORDER BY start_date DESC
    ");
    $stmt3->bind_param("i", $uid);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    while ($row = $res3->fetch_assoc()) {
        $edu[] = $row;
    }

    /* ---------- SKILLS (LATEST ROW) ---------- */
    $skills = null;
    $stmt4 = $conn->prepare("
        SELECT skill_name, languages
        FROM skills
        WHERE user_id = ?
        ORDER BY sid DESC
        LIMIT 1
    ");
    $stmt4->bind_param("i", $uid);
    $stmt4->execute();
    $res4 = $stmt4->get_result();
    if ($row = $res4->fetch_assoc()) {
        $skills = $row;
    }

    echo json_encode([
        "success"    => true,
        "user"       => $user,
        "experience" => $exp,
        "education"  => $edu,
        "skills"     => $skills
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
    exit;
}
