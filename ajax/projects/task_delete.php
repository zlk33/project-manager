<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["task_id"])) {
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $task_id = mysqli_real_escape_string($con, $_POST["task_id"]);
            $project_id = mysqli_real_escape_string($con, $_POST["project_id"]);
            $query_delete1 = "DELETE FROM tasks WHERE id = ".$task_id.";";
            $query_delete2 = "DELETE FROM tasks_assignment WHERE tasks_assignment.task_id =".$task_id.";";
            $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$project_id."', '11', '".date("Y-m-d H:i:s")."', '0', '".$task_id."', '".$_SESSION['id']."', '0');";
            $result_event = $con->query($query_event);
            $result_delete1 = $con->query($query_delete1);
            $result_delete2 = $con->query($query_delete2);
            if($result_delete1 && $result_delete2) {
                $json_success = [
                    'success' => true
                ];
                echo json_encode($json_success);
            } else {
                $json_error = [
                    'error' => "Wystąpił błąd"
                ];
                echo json_encode($json_error);
            }
        } else {
            $json_error = [
                'error' => "Wystąpił błąd"
            ];
            echo json_encode($json_error);
        }
    } else {
        $json_error = [
            'error' => "Błąd zapytania"
        ];
        echo json_encode($json_error);
    }

?>