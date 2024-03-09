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
                $query_1 = "SELECT COUNT(*) AS count FROM `projects_assignments` WHERE user_id=".$user_id." AND project_id=".$project_id.";";
                $result_1 = $con->query($query_1);
                if($result_1) {
                    $row = $result_1->fetch_assoc();
                    if($row['count'] != 0) {
                        $query_2 = "SELECT leader FROM `projects` WHERE project_id=".$project_id.";";
                        $result_2 = $con->query($query_2);
                        if($result_2) {
                            $row = $result_2->fetch_assoc();
                            if($user_id!=$row["leader"]) {
                                $query_3 = "DELETE FROM projects_assignments WHERE user_id = ".$user_id." AND project_id = ".$project_id."";
                                $result_3 = $con->query($query_3);
                                if($result_3) {
                                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$project_id."', '4', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$user_id."', 'NULL');";
                                    $con->query($query_event);
                                    $json_success = [
                                        'success' => "Pomyślnie opuściłeś projekt o id: ".$project_id."!"
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
                                    'error' => "Nie możesz opuścić projektu którego jesteś liderem"
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
                            'error' => "Nie jesteś członkiem tego projektu!"
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