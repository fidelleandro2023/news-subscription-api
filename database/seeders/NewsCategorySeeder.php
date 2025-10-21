<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NewsCategory;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen categorías para evitar duplicados
        if (NewsCategory::count() > 0) {
            return; // Si ya existen categorías, no crear duplicados
        }
        
        $categories = [
            [
                'name' => 'Tecnología',
                'slug' => 'tecnologia',
                'description' => 'Noticias sobre tecnología, innovación y desarrollo digital',
                'is_active' => true
            ],
            [
                'name' => 'Deportes',
                'slug' => 'deportes',
                'description' => 'Noticias deportivas, resultados y eventos',
                'is_active' => true
            ],
            [
                'name' => 'Política',
                'slug' => 'politica',
                'description' => 'Noticias políticas nacionales e internacionales',
                'is_active' => true
            ],
            [
                'name' => 'Economía',
                'slug' => 'economia',
                'description' => 'Noticias económicas, finanzas y mercados',
                'is_active' => true
            ],
            [
                'name' => 'Entretenimiento',
                'slug' => 'entretenimiento',
                'description' => 'Noticias de entretenimiento, celebridades y espectáculos',
                'is_active' => true
            ],
            [
                'name' => 'Salud',
                'slug' => 'salud',
                'description' => 'Noticias sobre salud, medicina y bienestar',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            NewsCategory::create($category);
        }
    }
}
