<?php
// ----------------------------------------------------------
// INITIAL SETUP
// ----------------------------------------------------------
session_start();
include("database.php");

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

// ----------------------------------------------------------
// REQUIRE LOGIN
// ----------------------------------------------------------
if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];

// ----------------------------------------------------------
// HELPERS
// ----------------------------------------------------------
function safe($value) {
    return isset($value) && $value !== "" ? $value : null;
}

// ----------------------------------------------------------
// HANDLE SKILLS (NORMAL FORM POST, NOT JSON)
// ----------------------------------------------------------
if (isset($_POST['type']) && $_POST['type'] === "skills_form") {

    $skills    = safe($_POST["skills"] ?? null);
    $languages = safe($_POST["languages"] ?? null);
    $phone     = safe($_POST["phone"] ?? null);

    // Insert skills only if something was entered
    if ($skills !== null || $languages !== null) {
        $stmt = $conn->prepare("
            INSERT INTO skills (user_id, skill_name, languages)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $uid, $skills, $languages);
        $stmt->execute();
    }

    // Update phone number
    if ($phone !== null) {
        $stmt2 = $conn->prepare("
            UPDATE users SET phone_number = ? WHERE uid = ?
        ");
        $stmt2->bind_param("si", $phone, $uid);
        $stmt2->execute();
    }

    echo json_encode(["success" => true]);
    exit;
}

// ----------------------------------------------------------
// HANDLE JSON REQUESTS FOR EXPERIENCE & EDUCATION
// ----------------------------------------------------------
$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

// If no JSON → stop
if (!$data || !isset($data["type"])) {
    echo json_encode(["success" => false, "error" => "Invalid or missing JSON"]);
    exit;
}

$type = $data["type"];

// ----------------------------------------------------------
// 🔥 EXPERIENCE
// ----------------------------------------------------------
if ($type === "experience") {

    if (!isset($data["experience"]) || !is_array($data["experience"])) {
        echo json_encode(["success" => false, "error" => "Experience array missing"]);
        exit;
    }

    foreach ($data["experience"] as $exp) {

        // Skip empty entries
        if (empty($exp["title"]) || empty($exp["company"])) {
            continue;
        }

        $title      = $exp["title"];
        $company    = $exp["company"];  // DB uses column company_name
        $location   = safe($exp["location"]);
        $start      = safe($exp["start"]);
        $end        = $exp["current"] ? null : safe($exp["end"]);
        $desc       = safe($exp["desc"]);

        $stmt = $conn->prepare("
            INSERT INTO work_experience
            (user_id, job_title, company_name, location, start_date, end_date, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issssss",
            $uid,
            $title,
            $company,
            $location,
            $start,
            $end,
            $desc
        );

        $stmt->execute();
    }

    echo json_encode(["success" => true]);
    exit;
}

// ----------------------------------------------------------
// 🔥 EDUCATION
// ----------------------------------------------------------
if ($type === "education") {

    if (!isset($data["education"]) || !is_array($data["education"])) {
        echo json_encode(["success" => false, "error" => "Education array missing"]);
        exit;
    }

    foreach ($data["education"] as $edu) {

        if (empty($edu["school"]) || empty($edu["degree"])) {
            continue;
        }

        // Convert year to full date OR null
        $start = $edu["startYear"] ? $edu["startYear"] . "-01-01" : null;
        $end   = $edu["endYear"]   ? $edu["endYear"] . "-01-01"   : null;

        $stmt = $conn->prepare("
            INSERT INTO education
            (user_id, school_name, field_of_study, start_date, end_date, degree)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssss",
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

// ----------------------------------------------------------
// FALLBACK
// ----------------------------------------------------------
echo json_encode(["success" => false, "error" => "Unknown or unsupported type"]);
exit;

?>
