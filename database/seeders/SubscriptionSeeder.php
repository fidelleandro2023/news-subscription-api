<?php
namespace Database\Seeders;
use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar suscripciones existentes para evitar duplicados
        Subscription::truncate();
        
        $subscriptions = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'categories' => json_encode(['tecnologia', 'deportes']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(10),
            ],
            [
                'name' => 'María García',
                'email' => 'maria.garcia@example.com',
                'categories' => json_encode(['salud', 'ciencia']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(5),
            ],
            [
                'name' => 'Carlos López',
                'email' => 'carlos.lopez@example.com',
                'categories' => json_encode(['economia', 'politica']),
                'is_active' => false,
                'subscribed_at' => now()->subDays(15),
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@example.com',
                'categories' => json_encode(['entretenimiento', 'cultura']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(3),
            ],
            [
                'name' => 'Luis Rodríguez',
                'email' => 'luis.rodriguez@example.com',
                'categories' => json_encode(['tecnologia', 'ciencia', 'salud']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(7),
            ],
            [
                'name' => 'Carmen Sánchez',
                'email' => 'carmen.sanchez@example.com',
                'categories' => json_encode(['deportes', 'entretenimiento']),
                'is_active' => false,
                'subscribed_at' => now()->subDays(20),
            ],
            [
                'name' => 'Pedro González',
                'email' => 'pedro.gonzalez@example.com',
                'categories' => json_encode(['economia', 'tecnologia']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(1),
            ],
            [
                'name' => 'Laura Fernández',
                'email' => 'laura.fernandez@example.com',
                'categories' => json_encode(['cultura', 'politica', 'salud']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(12),
            ],
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto.silva@example.com',
                'categories' => json_encode(['deportes', 'tecnologia']),
                'is_active' => true,
                'subscribed_at' => now()->subDays(8),
            ],
            [
                'name' => 'Elena Torres',
                'email' => 'elena.torres@example.com',
                'categories' => json_encode(['ciencia', 'cultura']),
                'is_active' => false,
                'subscribed_at' => now()->subDays(25),
            ]
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
