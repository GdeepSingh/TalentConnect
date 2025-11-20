<?php

header("Content-Type: application/json");



session_start();
include("database.php");


// Must be logged in
if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];
// Detect JSON request
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);



if (!$data || !isset($data['type'])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

/* ==========================================================
   SAVE EXPERIENCE
========================================================== */
// If JSON received → experience or education
if ($data && isset($data['type'])) {

    /* EXPERIENCE */
    if ($data['type'] === "experience") {

        foreach ($data['experience'] as $exp) {
            if (empty($exp['title']) || empty($exp['company'])) continue;

            $stmt = $conn->prepare("
                INSERT INTO work_experience
                (user_id, job_title, company_name, location, start_date, end_date, description)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("issssss",
                $uid,
                $exp['title'],
                $exp['company'],
                $exp['location'],
                $exp['start'],
                $exp['current'] ? NULL : $exp['end'],
                $exp['desc']
            );

            $stmt->execute();
        }

        echo json_encode(["success" => true]);
        exit;
    }

    /* EDUCATION */
    if ($data['type'] === "education") {

        foreach ($data['education'] as $edu) {
            if (empty($edu['school']) || empty($edu['degree'])) continue;

            $stmt = $conn->prepare("
                INSERT INTO education
                (user_id, school_name, field_of_study, start_date, end_date, degree)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("isssss",
                $uid,
                $edu['school'],
                $edu['field'],
                $edu['startYear'] . "-01-01",
                $edu['endYear'] . "-01-01",
                $edu['degree']
            );

            $stmt->execute();
        }

        echo json_encode(["success" => true]);
        exit;
    }

    echo json_encode(["success" => false, "error" => "Unknown JSON type"]);
    exit;
}


/* ==========================================================
   SAVE SKILLS (simple POST)
========================================================== */
if (isset($_POST['type']) && $_POST['type'] === "skills") {

    $skills    = trim($_POST['skills'] ?? "");
    $languages = trim($_POST['languages'] ?? "");
    $phone     = trim($_POST['phone'] ?? "");
    $city      = trim($_POST['city'] ?? "");  // can be ignored if not stored

    // Insert skills only if entered
    if (!empty($skills)) {
        $stmt = $conn->prepare("
            INSERT INTO skills (user_id, skill_name, languages)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $uid, $skills, $languages);
        $stmt->execute();
    }

    // Update only phone_number in users table (NO city)
    if (!empty($phone)) {
        $stmt2 = $conn->prepare("
            UPDATE users SET phone_number = ? WHERE uid = ?
        ");
        $stmt2->bind_param("si", $phone, $uid);
        $stmt2->execute();
    }

    // 🟦 Destroy session so user can log in fresh
    session_unset();
    session_destroy();

    echo "<script>
    alert('Profile Completed!');
    window.location.href = '../Frontend/User/done.html';
</script>";
exit;

}


/* ==========================================================
   UNKNOWN TYPE
========================================================== */
echo json_encode(["success" => false, "error" => "Unknown request type"]);
exit;
