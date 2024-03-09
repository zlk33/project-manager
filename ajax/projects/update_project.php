<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["projectId"]) && !empty($_POST["projectId"])) {
            if (isset($_POST['projectVisibility']) && $_POST['projectVisibility'] == 'on') {
                $checkbox="1";
            } else {
                $checkbox="0";
            }
            if(isset($_POST["projectName"]) && !empty($_POST["projectName"]) && isset($_POST["end_date"]) && !empty($_POST["end_date"])) {
                if(isset($_POST["projectDescription"]) && !empty($_POST["projectDescription"])) {
                    $description = $_POST["projectDescription"];
                } else {
                    $description = "-";
                }
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                $description = mysqli_real_escape_string($con, $description);
                $project_id = mysqli_real_escape_string($con, $_POST["projectId"]);
                $project_name = mysqli_real_escape_string($con, $_POST["projectName"]);
                $project_end = mysqli_real_escape_string($con, $_POST["end_date"]);

                $query_1 = "SELECT COUNT(*) AS count FROM projects_assignments WHERE project_id=".$project_id." AND permissions=3 AND user_id=".$_SESSION['id']."";
                $result_1 = $con->query($query_1);
                if($result_1) {
                    $row = $result_1->fetch_assoc();
                    if ($row['count'] != 0) {
                        $query_2 = "UPDATE `projects` SET `name` = '".$project_name."', `description` = '".$description."', `end_date` = '".$project_end."', `private` = '".$checkbox."' WHERE `projects`.`project_id` = ".$project_id.";";
                        $result_2 = $con->query($query_2);
                        if($result_2) {
                            $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$project_id."', '10', '".date("Y-m-d H:i:s")."', '0', '0', '".$_SESSION['id']."', '0');";
                            $result_event = $con->query($query_event);
                            $json_success = [
                                'success' => "true",
                                'project_id' => $project_id,
                                'project_name' => $project_name,
                                'description' => $description,
                                'visibility' => $checkbox,
                                'end_date' => $project_end
                            ];
                            echo json_encode($json_success);
                        } else {
                            $json_error = [
                                'error' => "Wystąpił błąd!"
                            ];
                            echo json_encode($json_error);
                        }
                    } else {
                        $json_error = [
                            'error' => "Wystąpił błąd!"
                        ];
                        echo json_encode($json_error);
                    }
                } else {
                    $json_error = [
                        'error' => "Niepoprawne dane!"
                    ];
                    echo json_encode($json_error);
                }
            } else {
                $json_error = [
                    'error' => "Niepoprawne dane!"
                ];
                echo json_encode($json_error);
            }

        }
    } else {
        $json_error = [
            'error' => "Wystąpił błąd! Spróbuj ponownie"
        ];
        echo json_encode($json_error);
    }

?>