<?php
header('Content-Type: application/json');
require_once '../config.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['shortUrl']) || empty($data['shortUrl']) || 
    !isset($data['newLongUrl']) || empty($data['newLongUrl'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Short URL and new destination URL are required']);
    exit;
}

$newLongUrl = filter_var($data['newLongUrl'], FILTER_VALIDATE_URL);
if (!$newLongUrl) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL format']);
    exit;
}

// Extract the short path from the URL
$shortUrl = $data['shortUrl'];
$parsedUrl = parse_url($shortUrl);
$path = trim($parsedUrl['path'] ?? '', '/');
$shortPath = explode('/', $path);
$shortPath = end($shortPath);

// Update the link in the database
try {
    $stmt = $pdo->prepare("UPDATE links SET long_url = ? WHERE short_path = ?");
    $stmt->execute([$newLongUrl, $shortPath]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Link updated successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Short URL not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

