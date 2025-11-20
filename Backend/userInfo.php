<?php
session_start();
include("database.php");

ini_set("display_errors", 1);
error_reporting(E_ALL);

/* ----------------------------------------------------------
   REQUIRE SESSION LOGIN
---------------------------------------------------------- */
if (!isset($_SESSION['uid'])) {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];

/* ----------------------------------------------------------
   1) SKILLS (NORMAL POST FORM REQUEST)
---------------------------------------------------------- */
if (isset($_POST['type']) && $_POST['type'] === "skills") {

    $skills    = trim($_POST['skills'] ?? "");
    $languages = trim($_POST['languages'] ?? "");
    $phone     = trim($_POST['phone'] ?? "");

    // Insert skills
    if (!empty($skills)) {
        $stmt = $conn->prepare("
            INSERT INTO skills (user_id, skill_name, languages)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $uid, $skills, $languages);
        $stmt->execute();
    }

    // Update user's phone number
    if (!empty($phone)) {
        $stmt2 = $conn->prepare("
            UPDATE users SET phone_number = ? WHERE uid = ?
        ");
        $stmt2->bind_param("si", $phone, $uid);
        $stmt2->execute();
    }

    // Redirect to user profile (NOT login)
    echo "<script>
        alert('Skills saved successfully!');
        window.location.href = '../Frontend/User/user_profile.html';
    </script>";
    exit;
}


/* ----------------------------------------------------------
   2) EXPERIENCE + EDUCATION (JSON REQUESTS)
---------------------------------------------------------- */
$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

// If JSON exists:
if ($data && isset($data["type"])) {

    header("Content-Type: application/json");

    /* ------------ EXPERIENCE ------------ */
    if ($data["type"] === "experience") {

        if (!isset($data["experience"]) || !is_array($data["experience"])) {
            echo json_encode(["success" => false, "error" => "Invalid experience array"]);
            exit;
        }

        foreach ($data["experience"] as $exp) {

            if (empty($exp["title"]) || empty($exp["company"])) continue;

            $stmt = $conn->prepare("
                INSERT INTO work_experience
                (user_id, job_title, company_name, location, start_date, end_date, description)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("issssss",
                $uid,
                $exp["title"],
                $exp["company"],
                $exp["location"],
                $exp["start"],
                $exp["current"] ? NULL : $exp["end"],
                $exp["desc"]
            );

            $stmt->execute();
        }

        echo json_encode(["success" => true]);
        exit;
    }

    /* ------------ EDUCATION ------------ */
    if ($data["type"] === "education") {

        if (!isset($data["education"]) || !is_array($data["education"])) {
            echo json_encode(["success" => false, "error" => "Invalid education array"]);
            exit;
        }

        foreach ($data["education"] as $edu) {

            if (empty($edu["school"]) || empty($edu["degree"])) continue;

            $start = $edu["startYear"] ? $edu["startYear"] . "-01-01" : NULL;
            $end   = $edu["endYear"]   ? $edu["endYear"] . "-01-01"   : NULL;

            $stmt = $conn->prepare("
                INSERT INTO education
                (user_id, school_name, field_of_study, start_date, end_date, degree)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param("isssss",
                $uid,
                $edu["school"],
                $edu["field"],
                $start,
                $end,
                $edu["degree"]
            );

            $stmt->execute();
        }

        echo json_encode(["success" => true]);
        exit;
    }

    /* -------- DEFAULT -------- */
    echo json_encode(["success" => false, "error" => "Unknown JSON type"]);
    exit;
}

/* ----------------------------------------------------------
   UNKNOWN REQUEST
---------------------------------------------------------- */
echo json_encode(["success" => false, "error" => "Unknown request type"]);
exit;
?>
