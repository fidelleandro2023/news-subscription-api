<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsCategoryController extends Controller
{
    public function __construct()
    {
        // No middleware needed for public routes (index, show)
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = NewsCategory::where('is_active', true)
            ->withCount('news')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news_categories,slug',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $category = NewsCategory::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente.',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = NewsCategory::with(['news' => function ($query) {
            $query->where('is_published', true)
                  ->orderBy('published_at', 'desc')
                  ->limit(10);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = NewsCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:news_categories,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente.',
            'data' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = NewsCategory::findOrFail($id);
        
        // Verificar si tiene noticias asociadas
        if ($category->news()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene noticias asociadas.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente.'
        ]);
    }
}
