<?php

include("database.php");
session_start();

if (!isset($_SESSION['uid'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$uid = $_SESSION['uid'];


// ---------------------------------------------------------
// 1️⃣ GET EMPLOYEE ROW TO DETERMINE THEIR COMPANY (employee_cid)
// ---------------------------------------------------------
$q = $conn->prepare("SELECT ceid, employee_cid FROM company_employees WHERE employee_uid = ?");
$q->bind_param("i", $uid);
$q->execute();
$res = $q->get_result();

if ($res->num_rows == 0) {
    echo json_encode(["success" => false, "error" => "Not a company employee"]);
    exit;
}

$empRow = $res->fetch_assoc();
$companyId = $empRow['employee_cid'];   // the company this user belongs to


// ---------------------------------------------------------
// 2️⃣ FIND ALL ceid (employees) BELONGING TO THIS COMPANY
// ---------------------------------------------------------
$q2 = $conn->prepare("SELECT ceid FROM company_employees WHERE employee_cid = ?");
$q2->bind_param("i", $companyId);
$q2->execute();
$empResult = $q2->get_result();

$allCEID = [];
while ($r = $empResult->fetch_assoc()) {
    $allCEID[] = $r['ceid'];
}

if (empty($allCEID)) {
    echo json_encode(["success" => true, "jobs" => []]);
    exit;
}


// ---------------------------------------------------------
// 3️⃣ PREPARE SQL IN() PLACEHOLDERS SAFELY
// ---------------------------------------------------------
$placeholders = implode(",", array_fill(0, count($allCEID), "?"));
$types = str_repeat("i", count($allCEID));


// ---------------------------------------------------------
// 4️⃣ GET ALL JOBPOSTS FOR ALL EMPLOYEES OF THIS COMPANY
// ---------------------------------------------------------
$sql = "SELECT * FROM jobpost WHERE emp_id IN ($placeholders) ORDER BY pid DESC";
$stmt = $conn->prepare($sql);

// FIXED: Must use spread operator properly for bind_param
$stmt->bind_param($types, ...$allCEID);

$stmt->execute();
$jobRes = $stmt->get_result();


// ---------------------------------------------------------
// 5️⃣ JOB CLASS (UNCHANGED FROM YOUR VERSION)
// ---------------------------------------------------------
class Job
{
    public $job_id;
    public $job_title;
    public $job_location;
    public $job_type;
    public $job_min;
    public $job_max;
    public $job_qualification;
    public $job_description;
    public $job_visibility;

    public function setInfo($id, $t, $l, $jt, $jmin, $jmax, $jq, $jd, $jv)
    {
        $this->job_id = $id;
        $this->job_title = $t;
        $this->job_location = $l;
        $this->job_type = $jt;
        $this->job_min = $jmin;
        $this->job_max = $jmax;
        $this->job_qualification = $jq;
        $this->job_description = $jd;
        $this->job_visibility = $jv;
    }
}


// ---------------------------------------------------------
// 6️⃣ BUILD RESPONSE ARRAY
// ---------------------------------------------------------
$jobInfo = [];

while ($row = $jobRes->fetch_object()) {

    $job = new Job();
    $job->setInfo(
        $row->pid,
        $row->title,
        $row->location,
        $row->jobtype,
        $row->min_salary,
        $row->max_salary,
        $row->qualification,
        $row->description,
        $row->visibility
    );

    $jobInfo[] = $job;
}


// ---------------------------------------------------------
// 7️⃣ RETURN FINAL JSON OUTPUT
// ---------------------------------------------------------
echo json_encode([
    "success" => true,
    "jobs" => $jobInfo
]);
exit;

?>
