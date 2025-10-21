<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        
        // Middleware para verificar que el usuario sea administrador
        $this->middleware(['auth:sanctum', 'role:Administrador']);
    }

    /**
     * Obtener todas las suscripciones con filtros avanzados para admin
     */
    public function getAllSubscriptions(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status'); // 'active', 'inactive', 'all'
            $search = $request->get('search'); // buscar por nombre o email
            $category = $request->get('category'); // filtrar por categoría
            
            $query = Subscription::query();
            
            // Filtro por estado
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
            
            // Búsqueda por nombre o email
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Filtro por categoría
            if ($category) {
                $query->whereJsonContains('categories', $category);
            }
            
            $subscriptions = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Suscripciones obtenidas exitosamente.',
                'data' => $subscriptions->items(),
                'meta' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                ],
                'filters' => [
                    'status' => $status,
                    'search' => $search,
                    'category' => $category
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
     * Obtener estadísticas de suscripciones para el dashboard de admin
     */
    public function getSubscriptionStats(): JsonResponse
    {
        try {
            $totalSubscriptions = Subscription::count();
            $activeSubscriptions = Subscription::where('is_active', true)->count();
            $inactiveSubscriptions = Subscription::where('is_active', false)->count();
            
            // Suscripciones por categoría
            $categoryStats = [];
            $allSubscriptions = Subscription::where('is_active', true)->get();
            
            foreach ($allSubscriptions as $subscription) {
                $categories = $subscription->categories ?? [];
                foreach ($categories as $category) {
                    if (!isset($categoryStats[$category])) {
                        $categoryStats[$category] = 0;
                    }
                    $categoryStats[$category]++;
                }
            }
            
            // Suscripciones recientes (últimos 30 días)
            $recentSubscriptions = Subscription::where('created_at', '>=', now()->subDays(30))->count();
            
            // Suscripciones por mes (últimos 6 meses)
            $monthlyStats = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $count = Subscription::whereYear('created_at', $date->year)
                                   ->whereMonth('created_at', $date->month)
                                   ->count();
                $monthlyStats[] = [
                    'month' => $date->format('Y-m'),
                    'count' => $count
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente.',
                'data' => [
                    'total_subscriptions' => $totalSubscriptions,
                    'active_subscriptions' => $activeSubscriptions,
                    'inactive_subscriptions' => $inactiveSubscriptions,
                    'recent_subscriptions' => $recentSubscriptions,
                    'category_stats' => $categoryStats,
                    'monthly_stats' => $monthlyStats,
                    'activity_rate' => $totalSubscriptions > 0 ? round(($activeSubscriptions / $totalSubscriptions) * 100, 2) : 0
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las estadísticas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activar o desactivar una suscripción
     */
    public function toggleSubscriptionStatus(string $id): JsonResponse
    {
        try {
            $subscription = Subscription::find($id);
            
            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suscripción no encontrada.'
                ], 404);
            }
            
            $subscription->is_active = !$subscription->is_active;
            $subscription->save();
            
            $status = $subscription->is_active ? 'activada' : 'desactivada';

            return response()->json([
                'success' => true,
                'message' => "Suscripción {$status} exitosamente.",
                'data' => $subscription
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado de la suscripción.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar múltiples suscripciones
     */
    public function bulkDeleteSubscriptions(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'subscription_ids' => 'required|array|min:1',
                'subscription_ids.*' => 'integer|exists:subscriptions,id'
            ]);
            
            $deletedCount = Subscription::whereIn('id', $request->subscription_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deletedCount} suscripciones exitosamente.",
                'deleted_count' => $deletedCount
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las suscripciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
