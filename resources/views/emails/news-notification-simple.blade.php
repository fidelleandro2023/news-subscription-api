<!DOCTYPE html>
<html>
<head>
    <title>Nueva Noticia</title>
</head>
<body>
    <h1>Nueva Noticia Disponible</h1>
    
    <h2>{{ $news->title }}</h2>
    
    <p><strong>Categoría:</strong> {{ $news->category->name ?? 'Sin categoría' }}</p>
    
    <p><strong>Resumen:</strong></p>
    <p>{{ $news->summary }}</p>
    
    <p><strong>Contenido:</strong></p>
    <div>{!! $news->content !!}</div>
    
    <p><strong>Fecha de publicación:</strong> {{ $news->published_at ? $news->published_at->format('d/m/Y H:i') : 'No publicada' }}</p>
    
    <hr>
    
    <p>Si no deseas recibir más notificaciones, puedes <a href="{{ $unsubscribeUrl }}">darte de baja aquí</a>.</p>
    
    <p>Saludos,<br>El equipo de Noticias</p>
</body>
</html>