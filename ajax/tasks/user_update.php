<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["action"]) && isset($_POST["user_id"]) && isset($_POST["task_id"])) {
            if(!empty($_POST["action"]) && !empty($_POST["user_id"]) && !empty($_POST["task_id"])) {
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
    
                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                $user_id = mysqli_real_escape_string($con,$_POST["user_id"]);
                $task_id = mysqli_real_escape_string($con,$_POST["task_id"]);
                $action = $_POST["action"];
                if($action=="delete") {
                    $query="DELETE FROM tasks_assignment WHERE task_id = ".$task_id." AND user_id=".$user_id.";";
                    $result=$con->query($query);
                    if($result) {
                        $json_success = [
                            'success' => true
                        ];
                        echo json_encode($json_success);
                    } else {
                        $json_error = [
                            'error' => "Wystąpił błąd!-5"
                        ];
                        echo json_encode($json_error);
                    }
                } elseif($action=="upgrade") {
                    $query="UPDATE `tasks_assignment` SET `permissions` = '1' WHERE task_id = ".$task_id." AND user_id = ".$user_id.";";
                    $result=$con->query($query);
                    if($result) {
                        $json_success = [
                            'success' => true
                        ];
                        echo json_encode($json_success);
                    } else {
                        $json_error = [
                            'error' => "Wystąpił błąd!-5"
                        ];
                        echo json_encode($json_error);
                    }
                } elseif($action=="downgrade") {
                    $query="UPDATE `tasks_assignment` SET `permissions` = '0' WHERE task_id = ".$task_id." AND user_id = ".$user_id.";";
                    $result=$con->query($query);
                    if($result) {
                        $json_success = [
                            'success' => true
                        ];
                        echo json_encode($json_success);
                    } else {
                        $json_error = [
                            'error' => "Wystąpił błąd!-5"
                        ];
                        echo json_encode($json_error);
                    }
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd!-4"
                    ];
                    echo json_encode($json_error);
                }
                $con->close();
            } else {
                $json_error = [
                    'error' => "Wystąpił błąd!-3"
                ];
                echo json_encode($json_error);
            }
        } else {
            $json_error = [
                'error' => "Wystąpił błąd!-2"
            ];
            echo json_encode($json_error);
        }
    } else {
        $json_error = [
            'error' => "Wystąpił błąd!-1"
        ];
        echo json_encode($json_error);
    }


?>