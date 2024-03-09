<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['project_id'])) {
            $project_id = $_POST['project_id'];
            $user_id = $_SESSION["id"];
            if(!empty($project_id)) {
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                $query_1 = "SELECT COUNT(*) AS count FROM `projects` WHERE project_id=".$project_id." AND leader=".$user_id.";";
                $result_1 = $con->query($query_1);
                if($result_1) {
                    $row = $result_1->fetch_assoc();
                    if($row['count'] == 1) {
                        $query_delete_projects = "DELETE FROM projects WHERE project_id = ".$project_id."";
                        $query_delete_files = "DELETE FROM projects_attachments WHERE project_id = ".$project_id."";
                        $query_delete_assignments = "DELETE FROM projects_assignments WHERE project_id = ".$project_id."";
                        $query_delete_events = "DELETE FROM projects_events WHERE project_id = ".$project_id."";
                        $query_delete_tasks = "DELETE FROM tasks WHERE project_id = ".$project_id."";
                        $con->query($query_delete_projects);
                        $con->query($query_delete_files);
                        $con->query($query_delete_assignments);
                        $con->query($query_delete_events);
                        $con->query($query_delete_tasks);
                        $path = "../../uploads/projects/".$project_id;
                        if (is_dir($path)) {
                            $files = glob($path . '/*');
                            foreach ($files as $file) {
                                unlink($file);
                            }
                        }
                        rmdir($path);
                        $json_success = [
                            'success' => "Pomyślnie usunąłeś projekt o id: ".$project_id."!"
                        ];
                        $con->close();
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

?>