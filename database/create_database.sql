-- Script para crear la base de datos MySQL
-- Ejecutar este script antes de las migraciones de Laravel

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `news_subscription_api` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE `news_subscription_api`;

-- Mostrar mensaje de confirmación
SELECT 'Base de datos news_subscription_api creada exitosamente' AS mensaje;