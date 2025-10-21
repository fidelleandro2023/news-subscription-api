<?php

function testRoute($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "URL: $url\n";
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "cURL Error: $error\n";
    }
    echo "Response: " . substr($response, 0, 200) . "\n";
    echo "---\n";
}

// Probar diferentes rutas
echo "=== TESTING ROUTES ===\n";
testRoute('http://127.0.0.1:8000/');
testRoute('http://127.0.0.1:8000/index.php');
testRoute('http://127.0.0.1:8000/test-web');
testRoute('http://127.0.0.1:8000/api/test-simple');
testRoute('http://127.0.0.1:8000/api/v1/test/subscriptions');
testRoute('http://127.0.0.1:8000/up');