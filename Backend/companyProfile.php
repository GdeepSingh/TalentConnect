<?php

include("database.php");


// Enable exceptions for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();



/* ---------------------------------------
   CHECK LOGIN
--------------------------------------- */
if (!isset($_SESSION['compID'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not Authorized"]);
    exit;
}

$cid = $_SESSION['compID'];


/* ---------------------------------------
   GET REQUEST → RETURN COMPANY DATA AS JSON
--------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    try {
        // Fetch company info
        $stmt = $conn->prepare("
            SELECT company_name, industry, hq_city, description, banner, logo 
            FROM companies 
            WHERE cid = ?
        ");
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();

        if (!$company) {
            echo json_encode(["error" => "Company not found"]);
            exit;
        }

        // Fetch company code
        $stmt2 = $conn->prepare("SELECT code FROM company_codes WHERE comp_cid = ?");
        $stmt2->bind_param("i", $cid);
        $stmt2->execute();
        $codeRow = $stmt2->get_result()->fetch_assoc();

        $company_code = $codeRow ? $codeRow["code"] : null;

        echo json_encode([
            "company_name" => $company["company_name"],
            "industry"     => $company["industry"],
            "hq_city"      => $company["hq_city"],
            "description"  => $company["description"],
            "banner"       => $company["banner"], // relative URL
            "logo"         => $company["logo"],
            "company_code" => $company_code
        ]);

        exit;

    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
        exit;
    }
}


/* ---------------------------------------
   LOAD CURRENT COMPANY FOR UPDATE
--------------------------------------- */
$stmt = $conn->prepare("SELECT * FROM companies WHERE cid = ?");
$stmt->bind_param("i", $cid);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();

if (!$company) {
    die("Company not found");
}

/* ---------------------------------------
   POST REQUEST → UPDATE COMPANY PROFILE
--------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cName     = $_POST['cName'] ?? "";
    $cIndustry = $_POST['cIndustry'] ?? "";
    $cLocation = $_POST['cLocation'] ?? "";
    $cAbout    = $_POST['cAbout'] ?? "";

    // Keep old images if not replaced
    $bannerPath = $company["banner"];
    $logoPath   = $company["logo"];

    /* --- Upload directory --- */
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    /* --- Banner upload --- */
    if (!empty($_FILES["banner"]["name"])) {
        $fileName = time() . "_banner_" . basename($_FILES["banner"]["name"]);
        $target = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["banner"]["tmp_name"], $target)) {
            $bannerPath = "uploads/" . $fileName; // relative
        }
    }

    /* --- Logo upload --- */
    if (!empty($_FILES["logo"]["name"])) {
        $fileName = time() . "_logo_" . basename($_FILES["logo"]["name"]);
        $target = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target)) {
            $logoPath = "uploads/" . $fileName;
        }
    }

    /* --- Update DB --- */
    $update = $conn->prepare("
        UPDATE companies SET
            company_name = ?,
            industry     = ?,
            hq_city      = ?,
            description  = ?,
            banner       = ?,
            logo         = ?
        WHERE cid = ?
    ");

    $update->bind_param(
        "ssssssi",
        $cName,
        $cIndustry,
        $cLocation,
        $cAbout,
        $bannerPath,
        $logoPath,
        $cid
    );

    if ($update->execute()) {
        echo "<script>alert('Profile updated!'); window.location='../Frontend/Company/company_profile.html';</script>";
        exit;
    }

    echo "<script>alert('Error updating profile');</script>";
    exit;
}

?>