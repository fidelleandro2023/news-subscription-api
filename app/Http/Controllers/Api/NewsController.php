<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        // No middleware needed for public routes (index, show)
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = News::with(['category', 'author'])
            ->where('is_published', true);

        // Filtro por categoría
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtro por búsqueda en título y contenido
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filtro por autor
        if ($request->has('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->author}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Asignar el autor actual
        $validated['author_id'] = auth()->id();
        
        // Si no se proporciona slug, generarlo desde el título
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Si se marca como publicado pero no tiene fecha de publicación, usar la actual
        if ($validated['is_published'] && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $news = News::create($validated);
        $news->load(['category', 'author']);

        return response()->json([
            'success' => true,
            'message' => 'Noticia creada exitosamente.',
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $news = News::with(['category', 'author'])
            ->where('is_published', true)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, string $id): JsonResponse
    {
        $news = News::findOrFail($id);
        $validated = $request->validated();
        
        // Si se actualiza el slug, asegurarse de que sea único
        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }
        
        // Si se marca como publicado y no tenía fecha de publicación, usar la actual
        if (isset($validated['is_published']) && $validated['is_published'] && !$news->published_at) {
            $validated['published_at'] = now();
        }

        $news->update($validated);
        $news->load(['category', 'author']);

        return response()->json([
            'success' => true,
            'message' => 'Noticia actualizada exitosamente.',
            'data' => $news
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Noticia eliminada exitosamente.'
        ]);
    }

    /**
     * Get all news for admin (including unpublished)
     */
    public function getAllNews(Request $request): JsonResponse
    {
        $query = News::with(['category', 'author']);

        // Filtros administrativos
        if ($request->has('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
            ]
        ]);
    }

    /**
     * Toggle news publication status
     */
    public function togglePublishStatus(string $id): JsonResponse
    {
        $news = News::findOrFail($id);
        
        $news->is_published = !$news->is_published;
        
        if ($news->is_published && !$news->published_at) {
            $news->published_at = now();
        }
        
        $news->save();
        $news->load(['category', 'author']);

        return response()->json([
            'success' => true,
            'message' => $news->is_published ? 'Noticia publicada exitosamente.' : 'Noticia despublicada exitosamente.',
            'data' => $news
        ]);
    }

    /**
     * Send news notification manually to all subscribers
     */
    public function sendNotification(string $id): JsonResponse
    {
        $news = News::with(['category', 'author'])->findOrFail($id);
        
        if (!$news->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden enviar notificaciones de noticias no publicadas.'
            ], 400);
        }

        $newsNotificationService = app(\App\Services\NewsNotificationService::class);
        $result = $newsNotificationService->queueNewsNotification($news);

        return response()->json([
            'success' => true,
            'message' => 'Notificaciones enviadas exitosamente.',
            'data' => [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'total_subscribers' => $result['total_subscribers'],
                'queued_count' => $result['queued_count'],
                'failed_count' => $result['failed_count'],
                'errors' => $result['errors']
            ]
        ]);
    }
}
