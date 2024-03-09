<?php
    session_start();
    if(isset($_SESSION['login']) && isset($_SESSION['token'])) {
        header("Location: index.php");
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie i Rejestracja</title>
    <link rel="stylesheet" href="style/login.css">
    <link rel="icon" type="image/png" href="imgs/logo256.png">
</head>
<body>
        <div class="panel">
            <div class="logo">
                JZ
            </div>
            <div class="panel-header">
                <span id="form-type">Logowanie</span>
            </div>
            <div class="content">
                <form id="login-form" class="login-form">

                    <label for="username_login">Nazwa użytkownika:</label>
                    <input type="text" id="username_login" name="username_login" placeholder="Login" required>

                    <label for="password_login">Hasło:</label>
                    <input type="password" id="password_login" name="password_login" placeholder="Hasło" required>


                    <button type="submit">Zaloguj się</button> 
            </form>

            <form id="register-form" class="form">
                <label for="username_register">Nazwa użytkownika:</label>
                <input type="text" id="username_register" name="username_register" placeholder="Login" required>
                <span class="requirements">4-12 znaków, tylko litery i cyfry</span>
                <label for="email_register">E-mail:</label>
                <input type="text" id="email_register" name="email_register" placeholder="Email" required>

                <label for="password_register">Hasło:</label>
                <input type="password" id="password_register" name="password_register" placeholder="Hasło" required>
                <span class="requirements">8-16 znaków</span>

                <button type="submit">Zarejestruj się</button>
            </form>
            <div id="alert" class="alert alert-red">
            <span class="title">Błąd</span> <span id="error_message"></span>
            </div> 
            <a href="#" id="toggle-link">Nie masz konta? Zarejestruj się!</a>
            </div>
            <footer>
                <div class="footer">
                    <a href="https://github.com/zlk33" target="_blank">zlk33</a><img alt="github_avatar" src="https://avatars.githubusercontent.com/u/101130006?v=4"> 2024
                </div>
                <footer>       
            </div>

<script src="scripts/jquery-3.6.4.min.js"></script>
<script src="scripts/login.js"></script>
</body>
</html>
