<?php

/**
 * Script de Pruebas Automatizadas Completas para la API de Suscripciones
 * 
 * Este script valida todos los endpoints de la API y verifica que funcionen correctamente.
 * Incluye pruebas de autenticación, CRUD de suscripciones y endpoints de prueba.
 */

class ApiTester
{
    private $baseUrl;
    private $token;
    private $testResults = [];
    private $createdSubscriptionId;
    private $testUserId;

    public function __construct($baseUrl = 'http://localhost:8000/api/v1')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Ejecuta una petición HTTP
     */
    private function makeRequest($method, $endpoint, $data = null, $headers = [])
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array_merge([
                'Content-Type: application/json',
                'Accept: application/json'
            ], $headers),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("cURL Error: $error");
        }

        return [
            'status_code' => $httpCode,
            'body' => json_decode($response, true),
            'raw_body' => $response
        ];
    }

    /**
     * Registra el resultado de una prueba
     */
    private function logTest($testName, $passed, $message = '', $details = null)
    {
        $this->testResults[] = [
            'test' => $testName,
            'passed' => $passed,
            'message' => $message,
            'details' => $details
        ];

        $status = $passed ? '✅ PASS' : '❌ FAIL';
        echo sprintf("%-50s %s", $testName, $status);
        if ($message) {
            echo " - $message";
        }
        echo "\n";

        if (!$passed && $details) {
            echo "   Detalles: " . json_encode($details, JSON_PRETTY_PRINT) . "\n";
        }
    }

    /**
     * Prueba el endpoint simple
     */
    public function testSimpleEndpoint()
    {
        try {
            $response = $this->makeRequest('GET', '/simple');
            $passed = $response['status_code'] === 200 && 
                     isset($response['body']['message']);
            
            $this->logTest('Simple Endpoint', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('Simple Endpoint', false, $e->getMessage());
        }
    }

    /**
     * Prueba el registro de usuario
     */
    public function testUserRegistration()
    {
        try {
            // Generar email único con timestamp
            $uniqueEmail = 'testuser' . time() . '@example.com';
            
            $userData = [
                'name' => 'Usuario Test ' . time(),
                'email' => $uniqueEmail,
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ];

            $response = $this->makeRequest('POST', '/auth/register', $userData);
            $passed = $response['status_code'] === 201 && 
                     isset($response['body']['token']);

            if ($passed) {
                $this->token = $response['body']['token'];
                $this->testUserId = $response['body']['user']['id'];
            }

            $this->logTest('User Registration', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('User Registration', false, $e->getMessage());
        }
    }

    /**
     * Prueba la creación de suscripción pública
     */
    public function testCreateSubscription()
    {
        try {
            $subscriptionData = [
                'name' => 'Test Subscription ' . time(),
                'email' => 'subscription' . time() . '@example.com',
                'categories' => ['tecnologia', 'deportes']
            ];

            $response = $this->makeRequest('POST', '/subscriptions', $subscriptionData);
            $passed = $response['status_code'] === 201 && 
                     isset($response['body']['data']['id']);

            if ($passed) {
                $this->createdSubscriptionId = $response['body']['data']['id'];
            }

            $this->logTest('Create Subscription (Public)', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('Create Subscription (Public)', false, $e->getMessage());
        }
    }

    /**
     * Prueba listar todas las suscripciones (protegido)
     */
    public function testListSubscriptions()
    {
        if (!$this->token) {
            $this->logTest('List Subscriptions (Protected)', false, 'No token available');
            return;
        }

        try {
            $headers = ['Authorization: Bearer ' . $this->token];
            $response = $this->makeRequest('GET', '/subscriptions', null, $headers);
            $passed = $response['status_code'] === 200 && 
                     isset($response['body']['data']);

            $this->logTest('List Subscriptions (Protected)', $passed, 
                "HTTP {$response['status_code']}", 
                ['total_items' => count($response['body']['data'] ?? [])]);
        } catch (Exception $e) {
            $this->logTest('List Subscriptions (Protected)', false, $e->getMessage());
        }
    }

    /**
     * Prueba obtener suscripción por ID (protegido)
     */
    public function testGetSubscriptionById()
    {
        if (!$this->token || !$this->createdSubscriptionId) {
            $this->logTest('Get Subscription by ID (Protected)', false, 
                'No token or subscription ID available');
            return;
        }

        try {
            $headers = ['Authorization: Bearer ' . $this->token];
            $response = $this->makeRequest('GET', "/subscriptions/{$this->createdSubscriptionId}", 
                null, $headers);
            $passed = $response['status_code'] === 200 && 
                     isset($response['body']['data']['id']);

            $this->logTest('Get Subscription by ID (Protected)', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('Get Subscription by ID (Protected)', false, $e->getMessage());
        }
    }

    /**
     * Prueba actualizar suscripción (protegido)
     */
    public function testUpdateSubscription()
    {
        if (!$this->token || !$this->createdSubscriptionId) {
            $this->logTest('Update Subscription (Protected)', false, 
                'No token or subscription ID available');
            return;
        }

        try {
            $updateData = [
                'name' => 'Updated Test Subscription',
                'categories' => ['ciencia', 'salud'],
                'is_active' => false
            ];

            $headers = ['Authorization: Bearer ' . $this->token];
            $response = $this->makeRequest('PUT', "/subscriptions/{$this->createdSubscriptionId}", 
                $updateData, $headers);
            $passed = $response['status_code'] === 200 && 
                     isset($response['body']['data']);

            $this->logTest('Update Subscription (Protected)', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('Update Subscription (Protected)', false, $e->getMessage());
        }
    }

    /**
     * Prueba obtener suscripciones activas (protegido)
     */
    public function testGetActiveSubscriptions()
    {
        if (!$this->token) {
            $this->logTest('Get Active Subscriptions (Protected)', false, 'No token available');
            return;
        }

        try {
            $headers = ['Authorization: Bearer ' . $this->token];
            $response = $this->makeRequest('GET', '/subscriptions/active', null, $headers);
            $passed = $response['status_code'] === 200;

            $this->logTest('Get Active Subscriptions (Protected)', $passed, 
                "HTTP {$response['status_code']}", 
                $response['body']);
        } catch (Exception $e) {
            $this->logTest('Get Active Subscriptions (Protected)', false, $e->getMessage());
        }
    }

    /**
     * Prueba endpoints de test (sin autenticación)
     */
    public function testPublicEndpoints()
    {
        // Test listar suscripciones
        try {
            $response = $this->makeRequest('GET', '/test/subscriptions');
            $passed = $response['status_code'] === 200;
            $this->logTest('Test List Subscriptions (Public)', $passed, 
                "HTTP {$response['status_code']}");
        } catch (Exception $e) {
            $this->logTest('Test List Subscriptions (Public)', false, $e->getMessage());
        }

        // Test obtener suscripciones activas
        try {
            $response = $this->makeRequest('GET', '/test/subscriptions/active');
            $passed = $response['status_code'] === 200;
            $this->logTest('Test Active Subscriptions (Public)', $passed, 
                "HTTP {$response['status_code']}");
        } catch (Exception $e) {
            $this->logTest('Test Active Subscriptions (Public)', false, $e->getMessage());
        }

        // Test verificar por email
        if ($this->createdSubscriptionId) {
            try {
                $response = $this->makeRequest('GET', '/test/subscriptions/1');
                $passed = $response['status_code'] === 200;
                $this->logTest('Test Get Subscription by ID (Public)', $passed, 
                    "HTTP {$response['status_code']}");
            } catch (Exception $e) {
                $this->logTest('Test Get Subscription by ID (Public)', false, $e->getMessage());
            }
        }
    }

    /**
     * Prueba eliminar suscripción (protegido)
     */
    public function testDeleteSubscription()
    {
        if (!$this->token || !$this->createdSubscriptionId) {
            $this->logTest('Delete Subscription (Protected)', false, 
                'No token or subscription ID available');
            return;
        }

        try {
            $headers = ['Authorization: Bearer ' . $this->token];
            $response = $this->makeRequest('DELETE', "/subscriptions/{$this->createdSubscriptionId}", 
                null, $headers);
            $passed = $response['status_code'] === 200;

            $this->logTest('Delete Subscription (Protected)', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('Delete Subscription (Protected)', false, $e->getMessage());
        }
    }

    /**
     * Prueba cerrar sesión
     */
    public function testLogout()
    {
        if (!$this->token) {
            $this->logTest('User Logout', false, 'No token available');
            return;
        }

        try {
            $headers = ['Authorization: Bearer ' . $this->token];
            $response = $this->makeRequest('POST', '/auth/logout', null, $headers);
            $passed = $response['status_code'] === 200;

            $this->logTest('User Logout', $passed, 
                "HTTP {$response['status_code']}", $response['body']);
        } catch (Exception $e) {
            $this->logTest('User Logout', false, $e->getMessage());
        }
    }

    /**
     * Ejecuta todas las pruebas
     */
    public function runAllTests()
    {
        echo "🚀 Iniciando Pruebas Automatizadas de la API\n";
        echo str_repeat("=", 80) . "\n\n";

        // Pruebas básicas
        $this->testSimpleEndpoint();
        
        // Pruebas de autenticación
        $this->testUserRegistration();
        
        // Pruebas de suscripciones
        $this->testCreateSubscription();
        $this->testListSubscriptions();
        $this->testGetSubscriptionById();
        $this->testUpdateSubscription();
        $this->testGetActiveSubscriptions();
        
        // Pruebas de endpoints públicos
        $this->testPublicEndpoints();
        
        // Limpieza
        $this->testDeleteSubscription();
        $this->testLogout();

        // Resumen
        $this->printSummary();
    }

    /**
     * Imprime el resumen de las pruebas
     */
    private function printSummary()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 RESUMEN DE PRUEBAS\n";
        echo str_repeat("=", 80) . "\n";

        $totalTests = count($this->testResults);
        $passedTests = count(array_filter($this->testResults, function($test) {
            return $test['passed'];
        }));
        $failedTests = $totalTests - $passedTests;

        echo "Total de pruebas: $totalTests\n";
        echo "✅ Exitosas: $passedTests\n";
        echo "❌ Fallidas: $failedTests\n";
        echo "📈 Porcentaje de éxito: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

        if ($failedTests > 0) {
            echo "❌ PRUEBAS FALLIDAS:\n";
            echo str_repeat("-", 40) . "\n";
            foreach ($this->testResults as $test) {
                if (!$test['passed']) {
                    echo "• {$test['test']}: {$test['message']}\n";
                }
            }
            echo "\n";
        }

        if ($passedTests === $totalTests) {
            echo "🎉 ¡Todas las pruebas pasaron exitosamente!\n";
            echo "✨ La API está funcionando correctamente.\n";
        } else {
            echo "⚠️  Algunas pruebas fallaron. Revisa los detalles arriba.\n";
        }

        echo str_repeat("=", 80) . "\n";
    }
}

// Ejecutar las pruebas
try {
    $tester = new ApiTester();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "❌ Error fatal durante las pruebas: " . $e->getMessage() . "\n";
    exit(1);
}