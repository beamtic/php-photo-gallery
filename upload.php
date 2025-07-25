<?php
// === CONFIG ===
define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');
require BASE_PATH . 'includes/settings.php';

if (session_status() == PHP_SESSION_NONE) {
    session_cache_limiter("private_no_expire");
    session_start();
}

header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if ((!isset($_SESSION["password"])) || ($_SESSION["password"] != $password)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ((isset($_POST["password"])) && (md5($_POST["password"]) == $password)) {
            $_SESSION["password"] = md5($_POST["password"]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "error" => "Invalid password."]);
            exit();
        }
    } else {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Authentication required."]);
        exit();
    }
}

if (!isset($_POST['filename'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Malformed filename."]);
    exit();
}

if (!isset($_FILES['chunk']) || !is_array($_FILES['chunk'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Malformed chunk."]);
    exit();
}

$originalName = basename($_POST['filename']);
$fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

if (!is_string($fileExtension) || !in_array($fileExtension, $allowed_file_types_arr)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Unsupported file type."]);
    exit();
}

// -----------------------------
// Continiue with the upload ==>
// -----------------------------
$upload_category = $_POST['category'] ?? '';
$chunkIndex = $_POST['chunkIndex'];
$totalChunks = $_POST['totalChunks'];

$target_dir = BASE_PATH . 'gallery/' . ($upload_category ? $upload_category . '/' : '');
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

$tempDir = sys_get_temp_dir() . '/chunk_upload_' . md5($originalName);
$finalPath = $target_dir . $originalName;

if (!file_exists($tempDir)) mkdir($tempDir, 0777, true);
move_uploaded_file($_FILES['chunk']['tmp_name'], "$tempDir/chunk_$chunkIndex");

if ((int)$chunkIndex + 1 === (int)$totalChunks) {
    $out = fopen($finalPath, 'wb');
    for ($i = 0; $i < $totalChunks; $i++) {
        fwrite($out, file_get_contents("$tempDir/chunk_$i"));
        unlink("$tempDir/chunk_$i");
    }
    fclose($out);
    rmdir($tempDir);
    chmod($finalPath, 0775);
    $_SESSION["selected_category"] = $upload_category;
    echo json_encode(["success" => true, "message" => "Upload complete."]);
    exit();
}

echo json_encode(["success" => true, "message" => "Chunk $chunkIndex received."]);
exit();
