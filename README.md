# News Subscription API

Sistema de suscripción a noticias con API REST desarrollado en Laravel y frontend en AngularJS.

## Stack Tecnológico

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Base de datos
- **Laravel Queue** - Sistema de colas para envío de emails
- **Laravel Mail** - Sistema de envío de correos electrónicos
- **Mailtrap** - Servicio de testing de emails

### Herramientas de Desarrollo
- **Composer** - Gestor de dependencias PHP
- **Artisan** - CLI de Laravel
- **PHP Built-in Server** - Servidor de desarrollo

## Requisitos del Sistema

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Extensiones PHP: PDO, MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON

## Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone https://github.com/fidelleandro2023/news-subscription-api.git
cd news-subscription-api
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Configurar Variables de Entorno

```bash
# Copiar el archivo de configuración
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos

El proyecto usa MySQL. Asegúrate de tener MySQL ejecutándose y crea la base de datos:

**Opción 1: Usando comando Artisan personalizado (Recomendado)**
```bash
php artisan db:create
```

**Opción 2: Usando el script SQL incluido**
```bash
# Ejecutar el script de creación de base de datos
mysql -u root -p < database/create_database.sql
```

**Opción 3: Comando directo**
```bash
# Crear la base de datos MySQL manualmente
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS news_subscription_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Configura las credenciales de MySQL en el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_subscription_api
DB_USERNAME=root
DB_PASSWORD=tu_password_mysql
```

### 5. Ejecutar Migraciones

```bash
php artisan migrate
```

### 6. Ejecutar Seeders (Opcional)

```bash
php artisan db:seed
```

### 7. Configurar Email (Mailtrap)

Edita el archivo `.env` con tus credenciales de Mailtrap:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@newssubscription.com"
MAIL_FROM_NAME="News Subscription"
```

## Ejecución del Proyecto

### 1. Iniciar el Servidor Backend (Laravel)

```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

### 2. Iniciar el Worker de Colas

En una nueva terminal, ejecuta:

```bash
php artisan queue:work --queue=default --timeout=60 --verbose
```

## Estructura del Proyecto

```
news-subscription-api/
├── app/
│   ├── Http/Controllers/     # Controladores de la API
│   ├── Models/              # Modelos Eloquent
│   ├── Mail/                # Clases de email
│   ├── Jobs/                # Jobs para colas
│   └── Console/Commands/    # Comandos Artisan personalizados
├── database/
│   ├── migrations/          # Migraciones de base de datos
│   ├── seeders/            # Seeders
│   └── create_database.sql # Script de creación de base de datos
├── routes/
│   └── api.php             # Rutas de la API
└── resources/views/emails/ # Plantillas de email
```

## API Endpoints

### Suscripciones
- `GET /api/subscriptions` - Listar suscripciones
- `POST /api/subscriptions` - Crear suscripción
- `GET /api/subscriptions/{id}` - Obtener suscripción
- `PUT /api/subscriptions/{id}` - Actualizar suscripción
- `DELETE /api/subscriptions/{id}` - Eliminar suscripción

### Noticias
- `GET /api/news` - Listar noticias
- `POST /api/news` - Crear noticia
- `GET /api/news/{id}` - Obtener noticia
- `PUT /api/news/{id}` - Actualizar noticia
- `DELETE /api/news/{id}` - Eliminar noticia

### Estadísticas
- `GET /api/stats` - Obtener estadísticas generales

## Funcionalidades


### Backend
- **API REST**: Endpoints para gestión de noticias y suscripciones
- **Sistema de Colas**: Envío asíncrono de emails de bienvenida
- **Validaciones**: Validación de datos de entrada
- **Emails**: Envío automático de emails de confirmación

## Testing

### Probar Envío de Emails

```bash
php artisan test:email
```

### Ejecutar Tests Unitarios

```bash
php artisan test
```

## Comandos Útiles

### Base de Datos
```bash
# Crear base de datos
php artisan db:create

# Crear base de datos (forzar si ya existe)
php artisan db:create --force

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Refrescar base de datos (migrar y sembrar)
php artisan migrate:fresh --seed
```

### Sistema
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas disponibles
php artisan route:list

# Monitorear colas
php artisan queue:monitor

# Reiniciar workers de cola
php artisan queue:restart
```

## Solución de Problemas

### Los emails no se envían
1. Verifica que el worker de colas esté ejecutándose
2. Revisa la configuración de email en `.env`
3. Reinicia el worker: `php artisan queue:restart`

### Error de base de datos
1. Verifica que MySQL esté ejecutándose
2. Confirma que la base de datos `news_subscription_api` exista
3. Verifica las credenciales en el archivo `.env`
4. Ejecuta las migraciones: `php artisan migrate`

### Frontend no carga
1. Verifica que el servidor PHP esté ejecutándose en el puerto 8080
2. Revisa la consola del navegador para errores JavaScript

