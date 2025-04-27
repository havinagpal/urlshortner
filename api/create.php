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

if (!isset($data['longUrl']) || empty($data['longUrl'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Long URL is required']);
    exit;
}

$longUrl = filter_var($data['longUrl'], FILTER_VALIDATE_URL);
if (!$longUrl) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL format']);
    exit;
}

// Check if custom path is provided
if (isset($data['customPath']) && !empty($data['customPath'])) {
    $shortPath = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['customPath']);
    
    // Check if custom path already exists
    $stmt = $pdo->prepare("SELECT * FROM links WHERE short_path = ?");
    $stmt->execute([$shortPath]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Custom path already in use']);
        exit;
    }
} else {
    // Generate a random short path
    $shortPath = generateShortPath($pdo);
}

// Insert the new link into the database
try {
    $stmt = $pdo->prepare("INSERT INTO links (short_path, long_url) VALUES (?, ?)");
    $stmt->execute([$shortPath, $longUrl]);
    
    echo json_encode([
        'success' => true,
        'shortUrl' => $base_url . $shortPath,
        'shortPath' => $shortPath
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Function to generate a unique short path
function generateShortPath($pdo) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    
    do {
        $shortPath = '';
        for ($i = 0; $i < 5; $i++) {
            $shortPath .= $characters[rand(0, $charactersLength - 1)];
        }
        
        // Check if this short path already exists
        $stmt = $pdo->prepare("SELECT * FROM links WHERE short_path = ?");
        $stmt->execute([$shortPath]);
    } while ($stmt->rowCount() > 0);
    
    return $shortPath;
}
?>

