$(document).ready(function() {
    $('#register-form').hide();

    $('#toggle-link').click(function(e) {
        e.preventDefault();
        if ($('#login-form').is(':visible')) {
            $('#login-form').hide();
            $('#register-form').show();
            $('#alert').hide();
            $(this).text('Masz konto? Zaloguj się!');
            $('#form-type').text('Rejestracja');
        } else {
            $('#register-form').hide();
            $('#login-form').show();
            $('#alert').hide();
            $(this).text('Nie masz konta? Zarejestruj się!');
            $('#form-type').text('Logowanie');
        }
    });

    $('#login-form').submit(function(event) {
        event.preventDefault();
        var username = $("#username_login").val();
        var password = $("#password_login").val();

        var data = {
            action: "login",
            username: username,
            password: password
        };
        $.ajax({
            type: "POST",
            url: "ajax/login.php",
            data: data,
            success: function(response) {
                if (response === "success") {
                    $('#alert').hide();
                    window.location.href = 'index.php';
                } else {
                    $('#error_message').text('Dane logowania są niepoprawne!')
                    $('#alert').show();
                }
            }
        });
    });

    $('#register-form').submit(function(event) {
        event.preventDefault();
        var username = $("#username_register").val();
        var email = $("#email_register").val();
        var password = $("#password_register").val();

        var data = {
            action: "register",
            username: username,
            email: email,
            password: password
        };
        $.ajax({
            type: "POST",
            url: "ajax/login.php",
            data: data,
            success: function(response) {
                if (response === "success") {
                    $('#alert').hide();
                    window.location.href = 'index.php';
                } else if(response === "login_used") {
                    $('#error_message').text('Podany login jest zajęty!');
                    $('#alert').show();
                } else if(response === "email_used") {
                    $('#error_message').text('Podany adres email jest zajęty!');
                    $('#alert').show();
                } else if(response === "login") {
                    $('#error_message').text('Login nie spełnia wymagań!');
                    $('#alert').show();
                } else if(response === "email") {
                    $('#error_message').text('Podany email jest nieprawidłowy!');
                    $('#alert').show();
                } else if(response === "password") {
                    $('#error_message').text('Podane hasło nie spełnia wymagań!');
                    $('#alert').show();
                } else {
                    $('#error_message').text('Wystąpił błąd!');
                    $('#alert').show();
                }
            }
        });
    });
});
