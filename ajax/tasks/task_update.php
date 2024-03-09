<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["task_id"]) && isset($_POST["task_name"]) && isset($_POST["start_date"]) && isset($_POST["end_date"])) {
            if(!empty($_POST["task_id"]) && !empty($_POST["task_name"]) && !empty($_POST["start_date"]) && !empty($_POST["end_date"])) {
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
    
                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                if(isset($_POST["description"]) && !empty($_POST["description"])) {
                    $description = mysqli_real_escape_string($con, $_POST["description"]);
                } else {
                    $description = "";
                }
                $task_id = mysqli_real_escape_string($con, $_POST["task_id"]);
                $task_name = mysqli_real_escape_string($con, $_POST["task_name"]);
                $start_date = mysqli_real_escape_string($con, $_POST["start_date"]);
                $end_date = mysqli_real_escape_string($con, $_POST["end_date"]);
                $query = "UPDATE `tasks` SET `name` = '".$task_name."', `description` = '".$description."', `start_date` = '".$start_date."', `end_date` = '".$end_date."' WHERE `tasks`.`id` = ".$task_id.";";
                $result = $con->query($query);
                if($result) {
                    $json_success = [
                        'success' => true,
                        'task_name' => $task_name,
                        'description' => $description,
                        'start_date' => $start_date,
                        'end_date' => $end_date
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
