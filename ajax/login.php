<?php
    session_start();
    include '../db_config.php';

    function loginValidation($login) {
        $len = strlen($login);
        if ($len < 4 || $len > 12) {
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $login)) {
            return false;
        }
        return true;
    }
    function emailValidation($email) {
        $pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
        if (preg_match($pattern, $email)) {
            return true; 
        } else {
            return false;
        }
    }
    function passwordValidation($password) {
        $len = strlen($password);
        if ($len < 8 || $len > 16) {
            return false;
        }
        return true;
    }

    function Login($post_login,$post_password) {

        global $db_ip;
        global $db_login;
        global $db_pass;
        global $db_name;

        $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

        if ($con->connect_error) {
            die("Blad polaczenia: " . $con->connect_error);
        }

        $login_secure = mysqli_real_escape_string($con, $post_login);
        $salt_query = "SELECT user_id,salt FROM users WHERE login='$login_secure'";
        $salt_result = $con->query($salt_query);

        if($salt_result) {
            $result = mysqli_fetch_row($salt_result);
            if($result) {
                $salt=$result[1];
                $id=$result[0];
                mysqli_free_result($salt_result);
            } else {
                echo "Blad danych.";
                return;
            }
        } else {
            echo "Blad danych.";
            return;
        }
        if(isset($salt) && isset($id)) {
            $hashed_password = hash('sha256', $post_password);
            $pass_n_salt = hash('sha256', $hashed_password.$salt);
            $login_query = "SELECT * FROM users WHERE login='$login_secure' AND password='$pass_n_salt'";
            $login_result = $con->query($login_query);
            if($login_result) {
                $result = mysqli_fetch_row($login_result);
                if($result) {
                    $_SESSION['login'] = $login_secure;
                    $_SESSION['id'] = $id;
                    echo "success";
                    return;
                } else {
                    echo "Blad danych.";
                    return;
                }
            } else {
                echo "Blad danych.";
                return;
            }
            mysqli_free_result($login_result);
        }

        $con->close();
    }
 function Register($post_login,$post_email,$post_password) {
    global $db_ip;
    global $db_login;
    global $db_pass;
    global $db_name;

    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

    if ($con->connect_error) {
        die("Blad polaczenia: " . $con->connect_error);
    }

    if(!loginValidation($post_login)) {
        echo "login";
        return;
    }
    if(!emailValidation($post_email)) {
        echo "email";
        return;
    }
    if(!passwordValidation($post_password)) {
        echo "password";
        return;
    }
    $login_secure = mysqli_real_escape_string($con, $post_login);
    $email_secure = mysqli_real_escape_string($con, $post_email);
    $password_secure = mysqli_real_escape_string($con, $post_password);

    $check_login_query = "SELECT * FROM users WHERE login='$login_secure'";
    $check_email_query = "SELECT * FROM users WHERE email='$email_secure'";
    $check_login_result = $con->query($check_login_query);
    $check_email_result = $con->query($check_email_query);

    if($check_login_result) {
        $result_check = mysqli_fetch_row($check_login_result);
        if($result_check) {
            echo "login_used";
            return;
        }
    }
    if($check_email_result) {
        $result_check = mysqli_fetch_row($check_email_result);
        if($result_check) {
            echo "email_used";
            return;
        }
    }
    $salt = bin2hex(random_bytes(32));
    $hashed_password=hash('sha256', $password_secure);
    $pass_n_salt = hash('sha256',$hashed_password.$salt);
    $date=date("Y-m-d");
    $acc_create_query = "INSERT INTO `users` (`user_id`, `login`, `email`, `first_name`, `last_name`, `company_name`, `password`, `salt`, `dateoac`) VALUES (NULL, '".$login_secure."', '".$email_secure."', '', '', '', '".$pass_n_salt."', '".$salt."', '".$date."');";
    $acc_create_result = $con->query($acc_create_query);
    if($acc_create_result) {
        $user_id = $con->insert_id;
        $_SESSION['id'] = $user_id;
        $_SESSION['login'] = $login_secure;
        echo "success";
        return;
    } else {
        echo "Wystapil blad!";
        return;
    }
 }

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['action'])) {
        if($_POST['action']=="login") {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                echo Login($_POST['username'],$_POST['password']);
            } else {
                echo "Blad danych!";
                return;
            }
        } elseif ($_POST['action']=="register") {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                Register($username,$email,$password);
            } else {
                echo "Blad danych!";
                return;
            }
        } else {
            echo "Blad zapytania!";
            return;
        }
    } else {
        echo "Blad zapytania!";
        return;
    }
} else {
    echo "Blad zapytania!";
    return;
}

?>