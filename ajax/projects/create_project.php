<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["project_name"]) && !empty($_POST["project_name"]) && isset($_POST["start_date"]) && !empty($_POST["start_date"]) && isset($_POST["end_date"]) && !empty($_POST["end_date"])) {
            include '../../db_config.php';
            include '../../functions.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            if(isset($_POST["project_description"]) && !empty($_POST["project_description"])) {
                $opis = mysqli_real_escape_string($con, $_POST["project_description"]);
            } else {
                $opis = "-";
            }
            $nazwa = mysqli_real_escape_string($con, $_POST["project_name"]);
            $end_date = mysqli_real_escape_string($con, $_POST["end_date"]);
            $start_date = mysqli_real_escape_string($con, $_POST["start_date"]);
            $unikalny_kod = false;
            while (!$unikalny_kod) {
                $kod = wygenerujKod();
                $query_kod = "SELECT COUNT(*) AS count FROM `projects` WHERE code = '".$kod."';";
                $result_kod = $con->query($query_kod);
                if ($result_kod) {
                    $row = $result_kod->fetch_assoc();
                    if ($row['count'] == 0) {
                        $unikalny_kod = true;
                    }
                } 
            }
            $query = "INSERT INTO `projects` (`project_id`, `name`, `description`, `leader`, `code`, `start_date`, `end_date`, `private`) VALUES (NULL, '".$nazwa."', '".$opis."', '".$_SESSION['id']."', '".$kod."', '".$start_date."', '".$end_date."', '0');";
            $result = $con->query($query);
            if($result) {
                $projectId = $con->insert_id;
                $query = "INSERT INTO `projects_assignments` (`id`, `project_id`, `user_id`, `permissions`) VALUES (NULL, '".$projectId."', '".$_SESSION['id']."', '3');";
                $result2 = $con->query($query);
                if($result2) {
                    $path = "../../uploads/projects/".$projectId;
                    mkdir($path, 0777, true);
                    $json_success = [
                        'success' => $projectId
                    ];
                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '1', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', 'NULL');";
                    $con->query($query_event);
                    echo json_encode($json_success);
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
                'error' => "Wszystkie pola oznaczone * są wymagane"
            ];
            echo json_encode($json_error);
        }
    } else {
        echo "Błąd zapytania!";
    }
?>