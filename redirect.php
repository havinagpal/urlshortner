<?php
require_once 'config.php';

// Get the short path from the URL
$shortPath = isset($_GET['path']) ? $_GET['path'] : '';

if (empty($shortPath)) {
    // Redirect to homepage if no path is provided
    header('Location: index.html');
    exit;
}

try {
    // Look up the short path in the database
    $stmt = $pdo->prepare("SELECT long_url FROM links WHERE short_path = ?");
    $stmt->execute([$shortPath]);
    $link = $stmt->fetch();
    
    if ($link) {
        // Increment the click count
        $updateStmt = $pdo->prepare("UPDATE links SET click_count = click_count + 1 WHERE short_path = ?");
        $updateStmt->execute([$shortPath]);
        
        // Redirect to the long URL
        header('Location: ' . $link['long_url']);
        exit;
    } else {
        // Short URL not found
        header('HTTP/1.0 404 Not Found');
        echo "404 - Short URL not found";
    }
} catch (PDOException $e) {
    // Database error
    header('HTTP/1.0 500 Internal Server Error');
    echo "500 - Server error";
}
?>

