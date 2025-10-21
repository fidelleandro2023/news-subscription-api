<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen noticias para evitar duplicados
        if (News::count() > 0) {
            return; // Si ya existen noticias, no crear duplicados
        }
        
        // Get categories and users
        $categories = NewsCategory::all();
        $users = User::all();

        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->info('No hay categorías o usuarios disponibles. Ejecuta primero los seeders correspondientes.');
            return;
        }

        $newsData = [
            [
                'title' => 'Nueva tecnología revoluciona la industria',
                'slug' => 'nueva-tecnologia-revoluciona-industria',
                'summary' => 'Una innovadora tecnología está cambiando la forma en que trabajamos.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'category_slug' => 'tecnologia',
                'is_published' => true,
                'published_at' => now()->subDays(1)
            ],
            [
                'title' => 'Resultados del campeonato nacional',
                'slug' => 'resultados-campeonato-nacional',
                'summary' => 'Los mejores equipos compitieron en una emocionante final.',
                'content' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'category_slug' => 'deportes',
                'is_published' => true,
                'published_at' => now()->subHours(12)
            ],
            [
                'title' => 'Nuevas políticas económicas anunciadas',
                'slug' => 'nuevas-politicas-economicas-anunciadas',
                'summary' => 'El gobierno presenta un paquete de medidas económicas.',
                'content' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.',
                'category_slug' => 'economia',
                'is_published' => true,
                'published_at' => now()->subHours(6)
            ],
            [
                'title' => 'Avances en medicina preventiva',
                'slug' => 'avances-medicina-preventiva',
                'summary' => 'Nuevos estudios revelan importantes avances en prevención.',
                'content' => 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.',
                'category_slug' => 'salud',
                'is_published' => true,
                'published_at' => now()->subHours(3)
            ],
            [
                'title' => 'Estreno de película esperada',
                'slug' => 'estreno-pelicula-esperada',
                'summary' => 'La película más esperada del año llega a los cines.',
                'content' => 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
                'category_slug' => 'entretenimiento',
                'is_published' => false,
                'published_at' => null
            ]
        ];

        foreach ($newsData as $data) {
            $category = $categories->where('slug', $data['category_slug'])->first();
            $author = $users->random();

            if ($category) {
                News::create([
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                    'news_category_id' => $category->id,
                    'author_id' => $author->id,
                    'is_published' => $data['is_published'],
                    'published_at' => $data['published_at']
                ]);
            }
        }
    }
}
