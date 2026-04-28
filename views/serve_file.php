<?php
// Proxy script to serve files securely without revealing true paths
// Example usage: serve_file.php?id=123

// 1. Get the material ID
$material_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($material_id <= 0) {
    header("HTTP/1.0 404 Not Found");
    echo "Invalid material requested.";
    exit;
}

// 2. Database Connection
$host = 'localhost';
$db   = 'eduready_lis_db';
$user = 'root'; // Adjust as necessary
$pass = '';     // Adjust as necessary
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo "Database connection failed.";
    exit;
}

// 3. Query the database for the file path
$stmt = $pdo->prepare("SELECT file_url, material_type FROM learning_materials WHERE id = ?");
$stmt->execute([$material_id]);
$material = $stmt->fetch();

if (!$material || $material['material_type'] !== 'file' || empty($material['file_url'])) {
    header("HTTP/1.0 404 Not Found");
    echo "Material not found or is not a file.";
    exit;
}

// The file_url from DB (e.g., 'uploads/materials/physics.pdf')
$file_path = $material['file_url'];

// Resolve the absolute path
// Adjust the `__DIR__ . '/../'` based on where your uploads folder actually is.
$absolute_path = realpath(__DIR__ . '/../' . $file_path);

// 4. Security Checks
// Ensure the file exists
if (!$absolute_path || !file_exists($absolute_path)) {
    header("HTTP/1.0 404 Not Found");
    echo "File does not exist on the server.";
    exit;
}

// Prevent Directory Traversal by ensuring the file is inside the expected 'uploads' folder
$allowed_dir = realpath(__DIR__ . '/../uploads/');
if (strpos($absolute_path, $allowed_dir) !== 0) {
    header("HTTP/1.0 403 Forbidden");
    echo "Access denied.";
    exit;
}

// 5. Serve the file
$mime_type = mime_content_type($absolute_path);
if (!$mime_type) {
    $mime_type = 'application/octet-stream';
}

$file_size = filesize($absolute_path);
$file_name = basename($absolute_path);

header("Content-Type: " . $mime_type);
header("Content-Length: " . $file_size);
header('Content-Disposition: inline; filename="' . $file_name . '"');
header('Cache-Control: public, max-age=3600');

// Clear the output buffer to avoid corrupting binary files
if (ob_get_level()) {
    ob_end_clean();
}

readfile($absolute_path);
exit;
?>
