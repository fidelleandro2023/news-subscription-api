<?php

function testEndpoint($method, $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "=== $method $url ===\n";
    echo "HTTP Code: $httpCode\n";
    
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo "Success: " . ($decoded['success'] ? 'true' : 'false') . "\n";
        echo "Message: " . $decoded['message'] . "\n";
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            echo "Data count: " . count($decoded['data']) . "\n";
        }
    } else {
        echo "Raw response: " . substr($response, 0, 200) . "\n";
    }
    echo "\n";
    
    return $httpCode == 200;
}

echo "=== TESTING KEY ENDPOINTS ===\n\n";

// Test 1: Lista de suscripciones (modo prueba)
$test1 = testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions');

// Test 2: Suscripción específica (modo prueba)
$test2 = testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions/1');

// Test 3: API simple
$test3 = testEndpoint('GET', 'http://127.0.0.1:8000/api/test-simple');

echo "=== RESULTS ===\n";
echo "Test 1 (List subscriptions): " . ($test1 ? "PASS" : "FAIL") . "\n";
echo "Test 2 (Get subscription): " . ($test2 ? "PASS" : "FAIL") . "\n";
echo "Test 3 (Simple API): " . ($test3 ? "PASS" : "FAIL") . "\n";