<?php

function testEndpoint($method, $url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "=== $method $url ===\n";
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "cURL Error: $error\n";
    }
    
    $decodedResponse = json_decode($response, true);
    if ($decodedResponse) {
        echo "Response: " . json_encode($decodedResponse, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Raw Response: " . substr($response, 0, 500) . "\n";
    }
    echo "\n";
    
    return ['code' => $httpCode, 'response' => $decodedResponse];
}

echo "=== TESTING SUBSCRIPTION API ENDPOINTS ===\n\n";

// 1. Probar endpoint de prueba para listar suscripciones
echo "1. Testing subscription list (test mode)\n";
testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions');

// 2. Probar endpoint de prueba para obtener suscripción específica
echo "2. Testing specific subscription (test mode)\n";
testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions/1');

// 3. Probar creación de suscripción (público)
echo "3. Testing subscription creation (public)\n";
$newSubscription = [
    'name' => 'Test User',
    'email' => 'test@example.com'
];
$result = testEndpoint('POST', 'http://127.0.0.1:8000/api/v1/subscriptions', $newSubscription);

// 4. Si la creación fue exitosa, probar obtener la nueva suscripción
if ($result['code'] == 201 && isset($result['response']['data']['id'])) {
    $newId = $result['response']['data']['id'];
    echo "4. Testing newly created subscription (ID: $newId)\n";
    testEndpoint('GET', "http://127.0.0.1:8000/api/v1/test/subscriptions/$newId");
}

// 5. Probar endpoint de suscripciones activas (requiere auth - debería fallar)
echo "5. Testing active subscriptions (should require auth)\n";
testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/subscriptions/active');

echo "=== TESTING COMPLETED ===\n";