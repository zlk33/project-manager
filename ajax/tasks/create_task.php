<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["task_name"]) && isset($_POST["start_date"]) && isset($_POST["end_date"]) && isset($_POST["project_id"])) {
            if(!empty($_POST["task_name"]) && !empty($_POST["start_date"]) && !empty($_POST["end_date"]) && !empty($_POST["project_id"])) {
                if($_POST["project_id"]!=0) {
                    include '../../db_config.php';
                    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

                    if ($con->connect_error) {
                        die("Blad polaczenia: " . $con->connect_error);
                    }
                    if(isset($_POST["description"])) {
                        if(!empty($_POST["description"])) {
                            $opis = mysqli_real_escape_string($con, $_POST["description"]);
                        } else {
                            $opis = "-";
                        }
                    } else {
                        $opis = "-";
                    }
                    $nazwa = mysqli_real_escape_string($con, $_POST["task_name"]);
                    $start_date = mysqli_real_escape_string($con, $_POST["start_date"]);
                    $end_date = mysqli_real_escape_string($con, $_POST["end_date"]);
                    $project_id = mysqli_real_escape_string($con, $_POST["project_id"]);
                    $query_project_leader = "SELECT leader FROM `projects` WHERE project_id=".$project_id.";";
                    $result_project_leader = $con->query($query_project_leader);
                    $row = $result_project_leader->fetch_assoc();
                    $project_leader = $row["leader"];
                    $query_create = "INSERT INTO `tasks` (`id`, `project_id`, `name`, `description`, `status`, `leader`, `start_date`, `end_date`) VALUES (NULL, '".$project_id."', '".$nazwa."', '".$opis."', '0', '".$_SESSION['id']."', '".$start_date."', '".$end_date."');";
                    $result_create = $con -> query($query_create);
                    
                    if($result_create) {
                        $task_id = $con->insert_id;
                        $query_assigment = "INSERT INTO `tasks_assignment` (`id`, `task_id`, `user_id`, `permissions`) VALUES (NULL, '".$task_id."', '".$_SESSION['id']."', '2');";
                        $con -> query($query_assigment);
                        if($project_leader!=$_SESSION["id"]) {
                            $query_leader = "INSERT INTO `tasks_assignment` (`id`, `task_id`, `user_id`, `permissions`) VALUES (NULL, '".$task_id."', '".$project_leader."', '1');";
                            $con->query($query_leader);
                        }
                        $json_success = [
                            'success' => $task_id
                        ];
                        echo json_encode($json_success);
                    } else {
                        $json_error = [
                            'error' => "Wystąpił błąd! Spróbuj ponownie"
                        ];
                        echo json_encode($json_error);
                    }
                } else {
                    $json_error = [
                        'error' => "Wybierz projekt w którym chcesz utworzyć zadanie!"
                    ];
                    echo json_encode($json_error);
                }
            } else {
                $json_error = [
                    'error' => "Wszystkie pola oznaczone * są wymagane!"
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





?>