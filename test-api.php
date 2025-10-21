<?php

// Función para probar endpoints
function testEndpoint($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Testing: $url\n";
    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n";
    echo "---\n";
}

// Probar endpoints
testEndpoint('http://127.0.0.1:8000/api/test-simple');
testEndpoint('http://127.0.0.1:8000/api/v1/test/subscriptions');