# API de Suscripciones de Noticias

## Descripción General

Esta API permite gestionar suscripciones de usuarios a un servicio de noticias. Los usuarios pueden suscribirse, actualizar sus preferencias, consultar suscripciones activas y más.

## Base URL

```
http://localhost:8000/api/v1
```

## Autenticación

La API utiliza Laravel Sanctum para autenticación. Los endpoints protegidos requieren un token Bearer en el header:

```
Authorization: Bearer {token}
```

## Endpoints Disponibles

### 1. Autenticación

#### Registro de Usuario
- **POST** `/auth/register`
- **Descripción**: Registra un nuevo usuario
- **Cuerpo de la petición**:
```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```
- **Respuesta exitosa (201)**:
```json
{
    "message": "Usuario registrado exitosamente",
    "user": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan@example.com"
    },
    "token": "1|abc123..."
}
```

#### Inicio de Sesión
- **POST** `/auth/login`
- **Descripción**: Autentica un usuario existente
- **Cuerpo de la petición**:
```json
{
    "email": "juan@example.com",
    "password": "password123"
}
```
- **Respuesta exitosa (200)**:
```json
{
    "message": "Inicio de sesión exitoso",
    "user": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan@example.com"
    },
    "token": "2|def456..."
}
```

#### Cerrar Sesión
- **POST** `/auth/logout`
- **Descripción**: Cierra la sesión del usuario autenticado
- **Headers**: `Authorization: Bearer {token}`
- **Respuesta exitosa (200)**:
```json
{
    "message": "Sesión cerrada exitosamente"
}
```

### 2. Suscripciones Públicas

#### Crear Suscripción
- **POST** `/subscriptions`
- **Descripción**: Crea una nueva suscripción (público)
- **Cuerpo de la petición**:
```json
{
    "name": "María García",
    "email": "maria@example.com",
    "categories": ["tecnologia", "deportes", "salud"]
}
```
- **Respuesta exitosa (201)**:
```json
{
    "message": "Suscripción creada exitosamente",
    "subscription": {
        "id": 11,
        "name": "María García",
        "email": "maria@example.com",
        "categories": ["tecnologia", "deportes", "salud"],
        "is_active": true,
        "subscribed_at": "2025-10-20T22:18:40.000000Z",
        "created_at": "2025-10-20T22:18:40.000000Z",
        "updated_at": "2025-10-20T22:18:40.000000Z"
    }
}
```

### 3. Suscripciones Protegidas (Requieren Autenticación)

#### Listar Todas las Suscripciones
- **GET** `/subscriptions`
- **Descripción**: Obtiene todas las suscripciones con paginación
- **Headers**: `Authorization: Bearer {token}`
- **Parámetros de consulta**:
  - `page` (opcional): Número de página (default: 1)
  - `per_page` (opcional): Elementos por página (default: 15)
- **Respuesta exitosa (200)**:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan.perez@example.com",
            "categories": ["tecnologia", "deportes"],
            "is_active": true,
            "subscribed_at": "2025-10-10T22:18:40.000000Z",
            "created_at": "2025-10-20T22:18:40.000000Z",
            "updated_at": "2025-10-20T22:18:40.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/v1/subscriptions?page=1",
        "last": "http://localhost:8000/api/v1/subscriptions?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 15,
        "to": 10,
        "total": 10
    }
}
```

#### Obtener Suscripción por ID
- **GET** `/subscriptions/{id}`
- **Descripción**: Obtiene una suscripción específica por su ID
- **Headers**: `Authorization: Bearer {token}`
- **Respuesta exitosa (200)**:
```json
{
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan.perez@example.com",
    "categories": ["tecnologia", "deportes"],
    "is_active": true,
    "subscribed_at": "2025-10-10T22:18:40.000000Z",
    "created_at": "2025-10-20T22:18:40.000000Z",
    "updated_at": "2025-10-20T22:18:40.000000Z"
}
```

#### Actualizar Suscripción
- **PUT** `/subscriptions/{id}`
- **Descripción**: Actualiza una suscripción existente
- **Headers**: `Authorization: Bearer {token}`
- **Cuerpo de la petición**:
```json
{
    "name": "Juan Pérez Actualizado",
    "email": "juan.nuevo@example.com",
    "categories": ["tecnologia", "ciencia"],
    "is_active": false
}
```
- **Respuesta exitosa (200)**:
```json
{
    "message": "Suscripción actualizada exitosamente",
    "subscription": {
        "id": 1,
        "name": "Juan Pérez Actualizado",
        "email": "juan.nuevo@example.com",
        "categories": ["tecnologia", "ciencia"],
        "is_active": false,
        "subscribed_at": "2025-10-10T22:18:40.000000Z",
        "created_at": "2025-10-20T22:18:40.000000Z",
        "updated_at": "2025-10-20T22:20:15.000000Z"
    }
}
```

#### Eliminar Suscripción
- **DELETE** `/subscriptions/{id}`
- **Descripción**: Elimina una suscripción
- **Headers**: `Authorization: Bearer {token}`
- **Respuesta exitosa (200)**:
```json
{
    "message": "Suscripción eliminada exitosamente"
}
```

#### Obtener Suscripciones Activas
- **GET** `/subscriptions/active`
- **Descripción**: Obtiene todas las suscripciones activas con paginación
- **Headers**: `Authorization: Bearer {token}`
- **Parámetros de consulta**:
  - `page` (opcional): Número de página (default: 1)
  - `per_page` (opcional): Elementos por página (default: 15)
- **Respuesta exitosa (200)**:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan.perez@example.com",
            "categories": ["tecnologia", "deportes"],
            "is_active": true,
            "subscribed_at": "2025-10-10T22:18:40.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 7
    }
}
```

### 4. Endpoints de Prueba (Sin Autenticación)

#### Listar Suscripciones (Test)
- **GET** `/test/subscriptions`
- **Descripción**: Endpoint de prueba para listar suscripciones sin autenticación

#### Obtener Suscripción por ID (Test)
- **GET** `/test/subscriptions/{id}`
- **Descripción**: Endpoint de prueba para obtener suscripción por ID sin autenticación

#### Obtener Suscripciones Activas (Test)
- **GET** `/test/subscriptions/active`
- **Descripción**: Endpoint de prueba para obtener suscripciones activas sin autenticación

#### Verificar Suscripción por Email (Test)
- **GET** `/test/subscriptions/check/{email}`
- **Descripción**: Endpoint de prueba para verificar si un email está suscrito
- **Respuesta exitosa (200)**:
```json
{
    "exists": true,
    "subscription": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan.perez@example.com",
        "categories": ["tecnologia", "deportes"],
        "is_active": true,
        "subscribed_at": "2025-10-10T22:18:40.000000Z"
    }
}
```

#### Endpoint Simple de Prueba
- **GET** `/simple`
- **Descripción**: Endpoint simple para verificar que la API está funcionando
- **Respuesta exitosa (200)**:
```json
{
    "message": "API funcionando correctamente",
    "timestamp": "2025-10-20T22:18:40.000000Z"
}
```

## Códigos de Estado HTTP

- **200 OK**: Operación exitosa
- **201 Created**: Recurso creado exitosamente
- **400 Bad Request**: Datos de entrada inválidos
- **401 Unauthorized**: Token de autenticación requerido o inválido
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Errores de validación
- **500 Internal Server Error**: Error interno del servidor

## Errores de Validación

Cuando hay errores de validación (422), la respuesta incluye detalles específicos:

```json
{
    "message": "Los datos proporcionados no son válidos.",
    "errors": {
        "email": [
            "El email ya está registrado."
        ],
        "name": [
            "El campo nombre es obligatorio."
        ]
    }
}
```

## Categorías Disponibles

Las siguientes categorías están disponibles para las suscripciones:

- `tecnologia`
- `deportes`
- `salud`
- `ciencia`
- `economia`
- `politica`
- `entretenimiento`
- `cultura`

## Gestión de Noticias

### Endpoints Públicos de Noticias

#### Listar Noticias Publicadas
- **GET** `/news`
- **Descripción**: Obtiene todas las noticias publicadas con paginación
- **Parámetros de consulta**:
  - `category` (string): Filtrar por slug de categoría
  - `search` (string): Buscar en título y resumen
  - `per_page` (integer): Elementos por página (default: 15)
  - `page` (integer): Número de página
- **Respuesta exitosa (200)**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Nueva tecnología revolucionaria",
            "slug": "nueva-tecnologia-revolucionaria",
            "summary": "Resumen de la noticia...",
            "content": "Contenido completo de la noticia...",
            "image_url": "https://example.com/image.jpg",
            "is_published": true,
            "published_at": "2024-01-15T10:30:00.000000Z",
            "created_at": "2024-01-15T09:00:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z",
            "category": {
                "id": 1,
                "name": "Tecnología",
                "slug": "tecnologia"
            },
            "author": {
                "id": 1,
                "name": "Editor Principal"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 67
    }
}
```

#### Obtener Noticia Específica
- **GET** `/news/{id}`
- **Descripción**: Obtiene una noticia específica por ID (solo si está publicada)
- **Respuesta exitosa (200)**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Nueva tecnología revolucionaria",
        "slug": "nueva-tecnologia-revolucionaria",
        "summary": "Resumen de la noticia...",
        "content": "Contenido completo de la noticia...",
        "image_url": "https://example.com/image.jpg",
        "is_published": true,
        "published_at": "2024-01-15T10:30:00.000000Z",
        "created_at": "2024-01-15T09:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "category": {
            "id": 1,
            "name": "Tecnología",
            "slug": "tecnologia"
        },
        "author": {
            "id": 1,
            "name": "Editor Principal"
        }
    }
}
```

### Endpoints Protegidos de Noticias (Requiere Autenticación + Rol Editor/Admin)

#### Crear Nueva Noticia
- **POST** `/news`
- **Descripción**: Crea una nueva noticia
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Editor o Administrador
- **Cuerpo de la petición**:
```json
{
    "title": "Título de la noticia",
    "slug": "titulo-de-la-noticia",
    "summary": "Resumen breve de la noticia",
    "content": "Contenido completo de la noticia...",
    "news_category_id": 1,
    "image_url": "https://example.com/image.jpg",
    "is_published": false,
    "published_at": null
}
```
- **Respuesta exitosa (201)**:
```json
{
    "success": true,
    "message": "Noticia creada exitosamente.",
    "data": {
        "id": 1,
        "title": "Título de la noticia",
        "slug": "titulo-de-la-noticia",
        "summary": "Resumen breve de la noticia",
        "content": "Contenido completo de la noticia...",
        "news_category_id": 1,
        "image_url": "https://example.com/image.jpg",
        "is_published": false,
        "published_at": null,
        "created_at": "2024-01-15T09:00:00.000000Z",
        "updated_at": "2024-01-15T09:00:00.000000Z",
        "category": {
            "id": 1,
            "name": "Tecnología",
            "slug": "tecnologia"
        },
        "author": {
            "id": 1,
            "name": "Editor Principal"
        }
    }
}
```

#### Actualizar Noticia
- **PUT/PATCH** `/news/{id}`
- **Descripción**: Actualiza una noticia existente
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Editor o Administrador
- **Cuerpo de la petición**: Campos a actualizar (parcial o completo)
- **Respuesta exitosa (200)**: Similar a la creación

#### Eliminar Noticia
- **DELETE** `/news/{id}`
- **Descripción**: Elimina una noticia
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Editor o Administrador
- **Respuesta exitosa (200)**:
```json
{
    "success": true,
    "message": "Noticia eliminada exitosamente."
}
```

### Endpoints de Administrador de Noticias (Solo Administradores)

#### Obtener Todas las Noticias (Admin)
- **GET** `/admin/news`
- **Descripción**: Obtiene todas las noticias (publicadas y no publicadas)
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Solo Administrador
- **Parámetros de consulta**:
  - `status` (string): `published` o `unpublished`
  - `category` (string): Filtrar por slug de categoría
  - `search` (string): Buscar en título y resumen
  - `sort_by` (string): Campo para ordenar (default: created_at)
  - `sort_order` (string): `asc` o `desc` (default: desc)
  - `per_page` (integer): Elementos por página (default: 15)

#### Cambiar Estado de Publicación
- **PATCH** `/admin/news/{id}/toggle-publish`
- **Descripción**: Cambia el estado de publicación de una noticia
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Solo Administrador
- **Respuesta exitosa (200)**:
```json
{
    "success": true,
    "message": "Noticia publicada exitosamente.",
    "data": {
        "id": 1,
        "title": "Título de la noticia",
        "is_published": true,
        "published_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

#### Enviar Notificaciones Manualmente
- **POST** `/admin/news/{id}/send-notification`
- **Descripción**: Envía notificaciones por email a todos los suscriptores activos
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Solo Administrador
- **Respuesta exitosa (200)**:
```json
{
    "success": true,
    "message": "Notificaciones enviadas exitosamente.",
    "data": {
        "news_id": 1,
        "news_title": "Título de la noticia",
        "total_subscribers": 150,
        "queued_count": 148,
        "failed_count": 2,
        "errors": [
            {
                "email": "invalid@email.com",
                "error": "Invalid email address"
            }
        ]
    }
}
```

## Gestión de Categorías de Noticias

### Endpoints Públicos de Categorías

#### Listar Categorías
- **GET** `/categories`
- **Descripción**: Obtiene todas las categorías de noticias
- **Respuesta exitosa (200)**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Tecnología",
            "slug": "tecnologia",
            "description": "Noticias sobre tecnología e innovación",
            "created_at": "2024-01-15T09:00:00.000000Z",
            "updated_at": "2024-01-15T09:00:00.000000Z"
        }
    ]
}
```

#### Obtener Categoría Específica
- **GET** `/categories/{id}`
- **Descripción**: Obtiene una categoría específica por ID

### Endpoints Protegidos de Categorías (Requiere Autenticación + Rol Editor/Admin)

#### Crear Nueva Categoría
- **POST** `/categories`
- **Descripción**: Crea una nueva categoría
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Editor o Administrador
- **Cuerpo de la petición**:
```json
{
    "name": "Nueva Categoría",
    "slug": "nueva-categoria",
    "description": "Descripción de la categoría"
}
```

#### Actualizar Categoría
- **PUT/PATCH** `/categories/{id}`
- **Descripción**: Actualiza una categoría existente
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Editor o Administrador

#### Eliminar Categoría
- **DELETE** `/categories/{id}`
- **Descripción**: Elimina una categoría
- **Autenticación**: Requerida (Bearer token)
- **Permisos**: Editor o Administrador

## Sistema de Notificaciones por Email

### Funcionamiento Automático

El sistema envía automáticamente notificaciones por email cuando:
1. Se crea una nueva noticia con `is_published: true`
2. Se cambia el estado de una noticia de no publicada a publicada

### Características del Sistema de Notificaciones

- **Envío en Cola**: Las notificaciones se procesan en segundo plano para no bloquear las peticiones
- **Filtrado de Suscriptores**: Solo se envían a suscriptores activos (`is_active: true`)
- **Logging Completo**: Todos los envíos y errores se registran en los logs
- **Gestión de Errores**: Los fallos en el envío se capturan y reportan sin afectar otros envíos
- **URL de Desuscripción**: Cada email incluye un enlace único para desuscribirse

### Template del Email

El email de notificación incluye:
- Título de la noticia
- Categoría y autor
- Fecha de publicación
- Imagen (si está disponible)
- Resumen de la noticia
- Enlace para leer la noticia completa
- Enlace de desuscripción personalizado

## Ejemplos de Uso con cURL

### Crear una suscripción:
```bash
curl -X POST http://localhost:8000/api/v1/subscriptions \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ana López",
    "email": "ana@example.com",
    "categories": ["salud", "ciencia"]
  }'
```

### Obtener noticias publicadas:
```bash
curl -X GET "http://localhost:8000/api/v1/news?category=tecnologia&per_page=10"
```

### Crear una noticia (requiere autenticación):
```bash
curl -X POST http://localhost:8000/api/v1/news \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "title": "Nueva Innovación en IA",
    "slug": "nueva-innovacion-en-ia",
    "summary": "Descubrimiento revolucionario en inteligencia artificial",
    "content": "Contenido completo de la noticia...",
    "news_category_id": 1,
    "is_published": true,
    "published_at": "2024-01-15T10:30:00Z"
  }'
```

### Publicar una noticia (solo admin):
```bash
curl -X PATCH http://localhost:8000/api/v1/admin/news/1/toggle-publish \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN_HERE"
```

### Enviar notificaciones manualmente (solo admin):
```bash
curl -X POST http://localhost:8000/api/v1/admin/news/1/send-notification \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN_HERE"
```

### Obtener suscripciones activas (con autenticación):
```bash
curl -X GET http://localhost:8000/api/v1/subscriptions/active \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Verificar suscripción por email (test):
```bash
curl -X GET http://localhost:8000/api/v1/test/subscriptions/check/juan.perez@example.com
```

## Notas Importantes

1. Los endpoints de prueba (`/test/*`) no requieren autenticación y están destinados solo para desarrollo.
2. Todos los timestamps están en formato UTC ISO 8601.
3. Las categorías se almacenan como un array JSON en la base de datos.
4. Los emails deben ser únicos en el sistema.
5. La paginación utiliza el estándar de Laravel con enlaces de navegación incluidos.
6. **Sistema de Roles**: 
   - `Administrador`: Acceso completo a todas las funciones
   - `Editor`: Puede crear, editar y eliminar noticias y categorías
   - `Usuario`: Solo puede gestionar suscripciones
7. **Notificaciones Automáticas**: Se envían automáticamente cuando se publican noticias
8. **Validaciones**: Todos los endpoints incluyen validación completa de datos
9. **Middleware de Seguridad**: Protección por roles y autenticación en endpoints sensibles
10. **Gestión de Errores**: Respuestas consistentes con códigos HTTP apropiados