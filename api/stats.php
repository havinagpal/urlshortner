<?php
header('Content-Type: application/json');
require_once '../config.php';

// Check if short path is provided
if (!isset($_GET['path']) || empty($_GET['path'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Short path is required']);
    exit;
}

$shortPath = $_GET['path'];

try {
    // Get link statistics
    $stmt = $pdo->prepare("SELECT short_path, long_url, created_at, updated_at, click_count FROM links WHERE short_path = ?");
    $stmt->execute([$shortPath]);
    $link = $stmt->fetch();
    
    if ($link) {
        echo json_encode([
            'success' => true,
            'stats' => [
                'shortPath' => $link['short_path'],
                'longUrl' => $link['long_url'],
                'createdAt' => $link['created_at'],
                'updatedAt' => $link['updated_at'],
                'clickCount' => $link['click_count']
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Short URL not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

