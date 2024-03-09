<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["task_id"]) && isset($_POST["status"])) {
            if(!empty($_POST["task_id"])) {
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
    
                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                $task_id = mysqli_real_escape_string($con, $_POST["task_id"]);
                $status = mysqli_real_escape_string($con, $_POST["status"]);
                $query = "UPDATE `tasks` SET `status` = '".$status."' WHERE `tasks`.`id` = ".$task_id.";";
                $result = $con->query($query);
                if($result) {
                    $json_success = [
                        'success' => true
                    ];
                    echo json_encode($json_success);
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd!-4"
                    ];
                    echo json_encode($json_error);
                }
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