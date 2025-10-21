<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido a nuestro boletín!</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .welcome-title {
            color: #3498db;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .highlight {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📰 NewsLetter</div>
            <h1 class="welcome-title">¡Bienvenido {{ $subscription->name }}!</h1>
        </div>

        <div class="content">
            <p>¡Gracias por suscribirte a nuestro boletín de noticias!</p>
            
            <p>Nos complace tenerte como parte de nuestra comunidad. A partir de ahora, recibirás las últimas noticias, actualizaciones y contenido exclusivo directamente en tu bandeja de entrada.</p>

            <div class="highlight">
                <strong>Detalles de tu suscripción:</strong><br>
                <strong>Nombre:</strong> {{ $subscription->name }}<br>
                <strong>Email:</strong> {{ $subscription->email }}<br>
                <strong>Fecha de suscripción:</strong> {{ $subscription->subscribed_at->format('d/m/Y H:i') }}
            </div>

            <p>¿Qué puedes esperar de nosotros?</p>
            <ul>
                <li>📈 Noticias actualizadas y relevantes</li>
                <li>🎯 Contenido personalizado según tus intereses</li>
                <li>🔔 Notificaciones importantes en tiempo real</li>
                <li>📊 Análisis y reportes exclusivos</li>
            </ul>

            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos. Estamos aquí para ayudarte.</p>
        </div>

        <div class="footer">
            <p>Este correo fue enviado porque te suscribiste a nuestro boletín de noticias.</p>
            <p>Si no deseas recibir más correos, puedes darte de baja en cualquier momento.</p>
            <p>&copy; {{ date('Y') }} NewsLetter API. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>