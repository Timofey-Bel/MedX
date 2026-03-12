<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        h1 {
            color: #1a1a1a;
            font-size: 24px;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .password-box {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .password {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .steps {
            background: #e7f3ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .steps h3 {
            color: #0066cc;
            margin-top: 0;
        }
        .steps ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .steps li {
            margin-bottom: 8px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <h1>Восстановление пароля</h1>
        </div>

        <div class="content">
            <p>Здравствуйте, {{ $user->name }}!</p>
            
            <p>Вы запросили восстановление пароля для вашего аккаунта. Мы создали для вас временный пароль:</p>

            <div class="password-box">
                <div style="margin-bottom: 10px; color: #6c757d; font-size: 14px;">Ваш временный пароль:</div>
                <div class="password">{{ $temporaryPassword }}</div>
            </div>

            <div class="warning">
                <div class="warning-title">⚠️ Важно!</div>
                <p style="margin: 0;">Этот пароль временный и должен быть изменен при первом входе в систему. Не передавайте его третьим лицам.</p>
            </div>

            <div class="steps">
                <h3>Что делать дальше:</h3>
                <ol>
                    <li>Перейдите на страницу входа</li>
                    <li>Введите ваш email: <strong>{{ $user->email }}</strong></li>
                    <li>Введите временный пароль: <strong>{{ $temporaryPassword }}</strong></li>
                    <li>После входа обязательно смените пароль в личном кабинете</li>
                </ol>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('login') }}" class="button">Войти в систему</a>
            </div>

            <p><strong>Безопасность:</strong></p>
            <ul>
                <li>Временный пароль действует до первой смены</li>
                <li>Если вы не запрашивали восстановление, проигнорируйте это письмо</li>
                <li>При подозрении на взлом обратитесь в службу поддержки</li>
            </ul>
        </div>

        <div class="footer">
            <p>Это автоматическое сообщение, не отвечайте на него.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Все права защищены.</p>
        </div>
    </div>
</body>
</html>