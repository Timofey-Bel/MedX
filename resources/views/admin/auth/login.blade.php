<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.frandroid.com/wp-content/uploads/2019/12/windows-10-wallpaper.jpg') center center no-repeat;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        
        /* Затемнение фона */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 0;
        }
        
        .login-container {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Аватар пользователя */
        .user-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            cursor: pointer;
            transition: transform 0.3s ease;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
        }
        
        .user-avatar .material-icons {
            font-size: 80px;
            color: white;
        }
        
        /* Имя пользователя */
        .user-name {
            font-size: 32px;
            color: white;
            margin-bottom: 30px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        
        /* Форма входа */
        .login-form {
            display: none;
            flex-direction: column;
            align-items: center;
            animation: slideUp 0.3s ease;
        }
        
        .login-form.active {
            display: flex;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Поля ввода */
        .input-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .text-input {
            width: 300px;
            padding: 15px 50px 15px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .text-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 0 0 2px #0078d7;
        }
        
        /* Маскировка пароля для поля типа text */
        .password-field {
            -webkit-text-security: disc;
            text-security: disc;
            font-family: 'Courier New', monospace;
        }
        
        /* Кнопка входа */
        .submit-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 4px;
            background: #0078d7;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }
        
        .submit-btn:hover {
            background: #005a9e;
        }
        
        .submit-btn .material-icons {
            font-size: 24px;
        }
        
        /* Сообщение об ошибке */
        .error-message {
            color: #ff6b6b;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 14px;
            animation: shake 0.5s ease;
            display: none;
        }
        
        .error-message.active {
            display: block;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
        
        /* Кнопка "Назад" */
        .back-btn {
            margin-top: 20px;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
            display: none;
        }
        
        .back-btn.active {
            display: block;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Дата и время внизу */
        .datetime {
            position: absolute;
            bottom: 40px;
            left: 40px;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        
        .time {
            font-size: 72px;
            font-weight: 300;
            line-height: 1;
            margin-bottom: 10px;
        }
        
        .date {
            font-size: 24px;
            font-weight: 300;
        }
        
        /* Ссылка на сайт */
        .site-link {
            position: absolute;
            bottom: 40px;
            right: 40px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            transition: all 0.2s ease;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        
        .site-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="user-avatar" id="avatar" onclick="showLoginForm()">
            <span class="material-icons">person</span>
        </div>
        
        <div class="user-name" id="userName">Вход в систему</div>
        
        <form class="login-form" id="loginForm" method="POST" action="/admin/login" autocomplete="off" novalidate>
@csrf
            <!-- Поле логина с динамическим именем и ID -->
            <div class="input-container">
                <input 
                    type="text" 
                    name="{{ $loginFieldName }}" 
                    class="text-input" 
                    id="{{ $loginFieldId }}"
                    autocomplete="off"
                    autocapitalize="off"
                    spellcheck="false"
                    data-lpignore="true"
                    data-form-type=""
                    value="{{ old('login') }}"
                    required
                >
            </div>
            
            <!-- Поле пароля с динамическим именем и ID (замаскированное как text) -->
            <div class="input-container">
                <input 
                    type="text" 
                    name="{{ $passwordFieldName }}" 
                    class="text-input password-field" 
                    id="{{ $passwordFieldId }}"
                    autocomplete="off"
                    spellcheck="false"
                    data-lpignore="true"
                    data-form-type=""
                    style="-webkit-text-security: disc; text-security: disc;"
                    required
                >
                <button type="submit" class="submit-btn">
                    <span class="material-icons">arrow_forward</span>
                </button>
            </div>
        </form>
        </form>
        
@if(session('error'))
        <div class="error-message active">
            {{ session('error') }}
        </div>
@endif
        
        <button class="back-btn" id="backBtn" onclick="hideLoginForm()">Назад</button>
    </div>
    
    <!-- Дата и время -->
    <div class="datetime">
        <div class="time" id="time">00:00</div>
        <div class="date" id="date">Понедельник, 1 января</div>
    </div>
    
    <!-- Ссылка на сайт -->
    <a href="/" class="site-link">← На сайт</a>
    
    <script>
        // Получаем динамические ID полей
        var loginFieldId = '{{ $loginFieldId }}';
        var passwordFieldId = '{{ $passwordFieldId }}';
        
        // Показать форму входа
        function showLoginForm() {
            document.getElementById('loginForm').classList.add('active');
            document.getElementById('backBtn').classList.add('active');
            document.getElementById(loginFieldId).focus();
        }
        
        // Скрыть форму входа
        function hideLoginForm() {
            document.getElementById('loginForm').classList.remove('active');
            document.getElementById('backBtn').classList.remove('active');
            document.getElementById(loginFieldId).value = '';
            document.getElementById(passwordFieldId).value = '';
        }
        
        // Обновление времени
        function updateTime() {
            const now = new Date();
            
            // Время
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('time').textContent = hours + ':' + minutes;
            
            // Дата
            const days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
            const months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
            
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const month = months[now.getMonth()];
            
            document.getElementById('date').textContent = dayName + ', ' + day + ' ' + month;
        }
        
        // Обновляем время каждую секунду
        updateTime();
        setInterval(updateTime, 1000);
        
        // Автоматически показать форму если есть ошибка
@if(session('error'))
        showLoginForm();
@endif
    </script>
</body>
</html>
