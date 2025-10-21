<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;
use PDOException;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {--force : Force creation even if database exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the MySQL database specified in the .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databaseName = Config::get('database.connections.mysql.database');
        $host = Config::get('database.connections.mysql.host');
        $port = Config::get('database.connections.mysql.port');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');

        if (!$databaseName) {
            $this->error('No database name specified in .env file');
            return 1;
        }

        try {
            // Connect to MySQL without specifying a database
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Check if database exists
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$databaseName]);
            $exists = $stmt->fetch();

            if ($exists && !$this->option('force')) {
                $this->info("Database '{$databaseName}' already exists.");
                
                if ($this->confirm('Do you want to continue anyway?')) {
                    $this->info("Using existing database '{$databaseName}'");
                    return 0;
                } else {
                    $this->info('Operation cancelled.');
                    return 0;
                }
            }

            // Create database
            $createQuery = "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $pdo->exec($createQuery);

            $this->info("✅ Database '{$databaseName}' created successfully!");
            $this->info("📍 Host: {$host}:{$port}");
            $this->info("👤 User: {$username}");
            
            // Test connection to the new database
            try {
                DB::connection()->getPdo();
                $this->info("✅ Database connection test successful!");
            } catch (\Exception $e) {
                $this->warn("⚠️  Database created but connection test failed. Please check your .env configuration.");
            }

            $this->newLine();
            $this->info("Next steps:");
            $this->info("1. Run migrations: php artisan migrate");
            $this->info("2. Run seeders: php artisan db:seed");

            return 0;

        } catch (PDOException $e) {
            $this->error("Failed to create database: " . $e->getMessage());
            $this->newLine();
            $this->info("Please check:");
            $this->info("- MySQL service is running");
            $this->info("- Database credentials in .env file are correct");
            $this->info("- User has permission to create databases");
            
            return 1;
        }
    }
}
