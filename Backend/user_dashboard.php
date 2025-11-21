<?php
include("database.php");
session_start();

if(!isset($_SESSION['uid'])){
    echo json_encode(["success"=>false,"error"=>"Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];

// check resume
$q = $conn->prepare("SELECT resume FROM users WHERE uid=?");
$q->bind_param("i",$uid);
$q->execute();
$resume = $q->get_result()->fetch_assoc()['resume'];

// check experience
$q2 = $conn->prepare("SELECT COUNT(*) AS total FROM work_experience WHERE user_id=?");
$q2->bind_param("i",$uid);
$q2->execute();
$exp = $q2->get_result()->fetch_assoc()['total'];

// check education
$q3 = $conn->prepare("SELECT COUNT(*) AS total FROM education WHERE user_id=?");
$q3->bind_param("i",$uid);
$q3->execute();
$edu = $q3->get_result()->fetch_assoc()['total'];

// check skills
$q4 = $conn->prepare("SELECT COUNT(*) AS total FROM skills WHERE user_id=?");
$q4->bind_param("i",$uid);
$q4->execute();
$skills = $q4->get_result()->fetch_assoc()['total'];

$ready = false;

// conditions:
if(!empty($resume)){
    $ready = true;  // resume alone is enough
}
else if($exp > 0 && $edu > 0 && $skills > 0){
    $ready = true;  // all three profile sections present
}

echo json_encode([
    "success"=>true,
    "ready"=>$ready
]);
exit;
?>
