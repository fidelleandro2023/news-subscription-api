<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        
        // Comentando middleware temporalmente para testing
        // $this->middleware('permission:view-subscriptions')->only(['index', 'show'])->except(['testIndex', 'testShow']);
        // $this->middleware('permission:create-subscriptions')->only(['store']);
        // $this->middleware('permission:edit-subscriptions')->only(['update']);
        // $this->middleware('permission:delete-subscriptions')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $subscriptions = $this->subscriptionService->getAllSubscriptions($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Suscripciones obtenidas exitosamente.',
                'data' => $subscriptions->items(),
                'meta' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las suscripciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->createSubscription($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Suscripción creada exitosamente. Se ha enviado un correo de bienvenida.',
                'data' => $subscription
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la suscripción.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->findSubscription($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suscripción no encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Suscripción obtenida exitosamente.',
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la suscripción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, string $id): JsonResponse
    {
        try {
            $updated = $this->subscriptionService->updateSubscription($id, $request->validated());

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suscripción no encontrada.'
                ], 404);
            }

            $subscription = $this->subscriptionService->findSubscription($id);

            return response()->json([
                'success' => true,
                'message' => 'Suscripción actualizada exitosamente.',
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la suscripción.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->subscriptionService->deleteSubscription($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suscripción no encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Suscripción eliminada exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la suscripción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de prueba para obtener suscripciones (sin autenticación)
     */
    public function testIndex(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $subscriptions = $this->subscriptionService->getAllSubscriptions($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Suscripciones obtenidas exitosamente (modo prueba).',
                'data' => $subscriptions->items(),
                'meta' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las suscripciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de prueba para obtener una suscripción específica (sin autenticación)
     */
    public function testShow(string $id): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->findSubscription($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suscripción no encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Suscripción obtenida exitosamente (modo prueba).',
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la suscripción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de prueba para obtener suscripciones activas (sin autenticación)
     */
    public function testGetActiveSubscriptions(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $subscriptions = $this->subscriptionService->getActiveSubscriptions($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Suscripciones activas obtenidas exitosamente (modo prueba).',
                'data' => $subscriptions->items(),
                'meta' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las suscripciones activas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de prueba para verificar suscripción por email (sin autenticación)
     */
    public function testCheckByEmail(string $email): JsonResponse
    {
        try {
            $subscription = $this->subscriptionService->findByEmail($email);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró suscripción para este email.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Suscripción encontrada exitosamente (modo prueba).',
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar la suscripción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
