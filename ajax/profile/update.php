<?php
    function passwordValidation($password) {
        $len = strlen($password);
        if ($len < 8 || $len > 16) {
            return false;
        }
        return true;
    }
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        include '../../db_config.php';
        $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

        if ($con->connect_error) {
            die("Blad polaczenia: " . $con->connect_error);
        }
        if(isset($_POST["action"])) {
            if($_POST["action"]=="info_update") {
                $first_name = mysqli_real_escape_string($con, $_POST["first_name"]);
                $last_name = mysqli_real_escape_string($con, $_POST["last_name"]);
                $company = mysqli_real_escape_string($con, $_POST["company"]);

                $query_update = "UPDATE `users` SET `first_name` = '".$first_name."', `last_name` = '".$last_name."', `company_name` = '".$company."' WHERE `users`.`user_id` = ".$_SESSION['id'].";";
                $result_update = $con->query($query_update);
                if($result_update) {
                    $json_success = [
                        'success' => "Zaaktualizowano informacje na profilu!"
                    ];
                    echo json_encode($json_success);
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd! Spróbuj ponownie"
                    ];
                    echo json_encode($json_error);
                }
            } elseif($_POST["action"]=="password_update") {
                if(isset($_POST["old_password"]) && isset($_POST["new_password"]) && isset($_POST["confirm_new_password"])) {
                    $old_password = $_POST["old_password"];
                    $new_password = $_POST["new_password"];
                    $confirm_new_password = $_POST["confirm_new_password"];
                    if(!empty($old_password) && !empty($new_password) && !empty($confirm_new_password)) {
                        $query_old_password = "SELECT password,salt FROM `users` WHERE user_id=".$_SESSION['id'].";";
                        $result_old_password = $con->query($query_old_password);
                        $row = $result_old_password->fetch_assoc();
                        $db_old_password = $row["password"];
                        $db_salt = $row["salt"];
                        $hashed_post_old_password = hash('sha256', $old_password);
                        $post_old_password = hash('sha256', $hashed_post_old_password.$db_salt);
                        if($post_old_password==$db_old_password) {
                            if($new_password==$confirm_new_password) {
                                if(passwordValidation($new_password)) {
                                    $new_password = hash('sha256', $new_password);
                                    $new_password = hash('sha256', $new_password.$db_salt);
                                    if($new_password!=$db_old_password) {
                                        $query_password_update="UPDATE `users` SET `password` = '".$new_password."' WHERE `users`.`user_id` = ".$_SESSION['id'].";";
                                        $result_password_update = $con->query($query_password_update);
                                        if($result_password_update) {
                                            $json_success = [
                                                'success' => "Pomyślnie zmieniono hasło!"
                                            ];
                                            echo json_encode($json_success);
                                        } else {
                                            $json_error = [
                                                'error' => "Wystąpił błąd! Spróbuj ponownie"
                                            ]; 
                                        }
                                    } else {
                                        $json_error = [
                                            'error' => "Nowe musi być inne niż stare!"
                                        ];
                                        echo json_encode($json_error);
                                    }
                                } else {
                                    $json_error = [
                                        'error' => "Nowe hasło nie spełnia wymagań!"
                                    ];
                                    echo json_encode($json_error);
                                }
                            } else {
                                $json_error = [
                                    'error' => "Nowe hasło i jego potwierdzenie się różnią!"
                                ];
                                echo json_encode($json_error);
                            }
                        } else {
                            $json_error = [
                                'error' => "Stare hasło się nie zgadza!"
                            ];
                            echo json_encode($json_error);
                        }
                    } else {
                        $json_error = [
                            'error' => "Uzupełnij wszystkie pola!"
                        ];
                        echo json_encode($json_error); 
                    }
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd! Spróbuj ponownie"
                    ];
                    echo json_encode($json_error);
                }
            } else {
                $json_error = [
                    'error' => "Wystąpił błąd! Spróbuj ponownie"
                ];
                echo json_encode($json_error);
            }
        } else {
            $json_error = [
                'error' => "Wystąpił błąd! Spróbuj ponownie"
            ];
            echo json_encode($json_error);
        }
        $con->close();
    } else {
        $json_error = [
            'error' => "Wystąpił błąd! Spróbuj ponownie"
        ];
        echo json_encode($json_error);
    }

?>