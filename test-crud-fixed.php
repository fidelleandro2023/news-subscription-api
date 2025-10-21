<?php

function testEndpoint($method, $url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "=== $method $url ===\n";
    echo "HTTP Code: $httpCode\n";
    
    $decoded = json_decode($response, true);
    if ($decoded && isset($decoded['success'])) {
        echo "Success: " . ($decoded['success'] ? 'true' : 'false') . "\n";
        echo "Message: " . $decoded['message'] . "\n";
        if (isset($decoded['data'])) {
            if (is_array($decoded['data'])) {
                echo "Data count: " . count($decoded['data']) . "\n";
            } else {
                echo "Data: " . json_encode($decoded['data']) . "\n";
            }
        }
    } else {
        echo "Raw response: " . substr($response, 0, 300) . "\n";
    }
    echo "\n";
    
    return ['code' => $httpCode, 'response' => $decoded];
}

echo "=== TESTING CRUD OPERATIONS (FIXED) ===\n\n";

// Test 1: Crear nueva suscripción (usando email único)
echo "1. Creando nueva suscripción...\n";
$uniqueEmail = 'test_' . time() . '@example.com';
$newSubscription = [
    'email' => $uniqueEmail,
    'name' => 'Usuario de Prueba',
    'categories' => ['tecnologia', 'deportes']
];
$result1 = testEndpoint('POST', 'http://127.0.0.1:8000/api/v1/subscriptions', $newSubscription);

// Test 2: Obtener suscripciones activas (usando ruta de prueba)
echo "2. Obteniendo suscripciones activas...\n";
$result2 = testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions/active');

// Test 3: Verificar suscripción por email (usando ruta de prueba)
echo "3. Verificando suscripción por email...\n";
$result3 = testEndpoint('GET', "http://127.0.0.1:8000/api/v1/test/subscriptions/check/$uniqueEmail");

// Test 4: Obtener lista de suscripciones (modo prueba)
echo "4. Obteniendo lista de suscripciones...\n";
$result4 = testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions');

// Test 5: Obtener suscripción específica (modo prueba)
echo "5. Obteniendo suscripción específica...\n";
$result5 = testEndpoint('GET', 'http://127.0.0.1:8000/api/v1/test/subscriptions/1');

echo "=== SUMMARY ===\n";
echo "Create subscription: " . ($result1['code'] == 201 ? "PASS" : "FAIL") . " (Code: " . $result1['code'] . ")\n";
echo "Get active subscriptions: " . ($result2['code'] == 200 ? "PASS" : "FAIL") . " (Code: " . $result2['code'] . ")\n";
echo "Check subscription by email: " . ($result3['code'] == 200 ? "PASS" : "FAIL") . " (Code: " . $result3['code'] . ")\n";
echo "Get all subscriptions: " . ($result4['code'] == 200 ? "PASS" : "FAIL") . " (Code: " . $result4['code'] . ")\n";
echo "Get specific subscription: " . ($result5['code'] == 200 ? "PASS" : "FAIL") . " (Code: " . $result5['code'] . ")\n";