<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);  // must NOT output errors (JSON only)

session_start();
header("Content-Type: application/json");

include("database.php");

// Must be logged in
if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "ready" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];

/*
|--------------------------------------------------------------------------
| 1. Check if resume exists
|--------------------------------------------------------------------------
*/
$res = $conn->prepare("SELECT resume FROM users WHERE uid = ?");
$res->bind_param("i", $uid);
$res->execute();
$userRow = $res->get_result()->fetch_assoc();

$resumeExists = (!empty($userRow['resume']));

/*
|--------------------------------------------------------------------------
| 2. Check Work Experience
|--------------------------------------------------------------------------
*/
$q1 = $conn->prepare("SELECT COUNT(*) AS total FROM work_experience WHERE user_id = ?");
$q1->bind_param("i", $uid);
$q1->execute();
$workCount = $q1->get_result()->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 3. Check Education
|--------------------------------------------------------------------------
*/
$q2 = $conn->prepare("SELECT COUNT(*) AS total FROM education WHERE user_id = ?");
$q2->bind_param("i", $uid);
$q2->execute();
$eduCount = $q2->get_result()->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 4. Check Skills
|--------------------------------------------------------------------------
*/
$q3 = $conn->prepare("SELECT COUNT(*) AS total FROM skills WHERE user_id = ?");
$q3->bind_param("i", $uid);
$q3->execute();
$skillsCount = $q3->get_result()->fetch_assoc()['total'];

/*
|--------------------------------------------------------------------------
| 5. Apply rules:
|    Resume OR (work + edu + skills)
|--------------------------------------------------------------------------
*/
$ready = (
    $resumeExists ||
    ($workCount > 0 && $eduCount > 0 && $skillsCount > 0)
);

echo json_encode([
    "success" => true,
    "ready" => $ready
]);
exit;

?>
