<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Noticia - {{ $news->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .news-title {
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        .news-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .news-summary {
            font-size: 16px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border-left: 4px solid #007bff;
            border-radius: 0 5px 5px 0;
        }
        .news-image {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
        .read-more-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            transition: background-color 0.3s;
        }
        .read-more-btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .unsubscribe {
            margin-top: 15px;
        }
        .unsubscribe a {
            color: #dc3545;
            text-decoration: none;
        }
        .category-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📰 Noticias API</div>
            <p>¡Hola {{ $subscription->name }}! Tenemos una nueva noticia para ti</p>
        </div>

        <div class="news-content">
            <h1 class="news-title">{{ $news->title }}</h1>
            
            <div class="news-meta">
                <span class="category-badge">{{ $news->category->name }}</span>
                <strong>Autor:</strong> {{ $news->author->name }} | 
                <strong>Fecha:</strong> {{ $news->published_at->format('d/m/Y H:i') }}
            </div>

            @if($news->image_url)
                <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="news-image">
            @endif

            <div class="news-summary">
                {{ $news->summary }}
            </div>

            <a href="{{ config('app.url') }}/api/v1/news/{{ $news->id }}" class="read-more-btn">
                Leer noticia completa →
            </a>
        </div>

        <div class="footer">
            <p>
                Recibiste este email porque estás suscrito a noticias de la categoría 
                <strong>{{ $news->category->name }}</strong>.
            </p>
            
            <div class="unsubscribe">
                <p>
                    Si no deseas recibir más notificaciones, puedes 
                    <a href="{{ $unsubscribeUrl }}">gestionar tu suscripción aquí</a>.
                </p>
            </div>
            
            <p style="margin-top: 20px;">
                © {{ date('Y') }} Noticias API. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>