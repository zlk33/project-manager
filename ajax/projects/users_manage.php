<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["action"]) && isset($_POST["user_id"]) && isset($_POST["project_id"]) && !empty($_POST["user_id"]) && !empty($_POST["project_id"])) {
            $action = $_POST["action"];
            $userId = $_POST["user_id"];
            $projectId = $_POST["project_id"];
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            if($action=="ban") {
                $query_remove = "DELETE FROM projects_assignments WHERE project_id=".$projectId." AND user_id=".$userId."";
                $result_remove = $con->query($query_remove);
                $query_ban = "INSERT INTO `project_bans` (`ban_id`, `b_project_id`, `b_user_id`) VALUES (NULL, '".$projectId."', '".$userId."');";
                $result_ban = $con->query($query_ban);
                if($result_remove) {
                    if($result_ban) {
                        $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '6', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', '".$userId."');";
                        $con->query($query_event);
                        $json_success = [
                            'success' => "zbanowany",
                            'project_id' => $projectId,
                            'user_id' => $userId
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
                        'error' => "Wystąpił błąd! Spróbuj ponownie"
                    ];
                    echo json_encode($json_error);
                }
            } elseif($action=="delete") {
                $query_remove = "DELETE FROM projects_assignments WHERE project_id=".$projectId." AND user_id=".$userId."";
                $result_remove = $con->query($query_remove);
                if($result_remove) {
                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '5', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', '".$userId."');";
                    $con->query($query_event);
                    $json_success = [
                        'success' => "usuniety",
                        'project_id' => $projectId,
                        'user_id' => $userId
                    ];
                    echo json_encode($json_success);
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd! Spróbuj ponownie"
                    ];
                    echo json_encode($json_error);
                }
            } elseif($action=="upgrade") {
                $query_upgrade = "UPDATE projects_assignments SET permissions = '2' WHERE user_id=".$userId." AND project_id=".$projectId.";";
                $result_upgrade = $con->query($query_upgrade);
                if($result_upgrade) {
                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '7', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', '".$userId."');";
                    $con->query($query_event);
                    $query_name = "SELECT login FROM `users` WHERE user_id=".$userId.";";
                    $result_name = $con->query($query_name);
                    if($result_name) {
                        $row = $result_name->fetch_assoc();
                        $userName = $row['login'];
                    } else {
                        $userName = "!!!";
                    }
                    $json_success = [
                        'success' => "podwyzszone_uprawnienia",
                        'project_id' => $projectId,
                        'user_id' => $userId,
                        'user_name' => $userName
                    ];
                    echo json_encode($json_success);
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd! Spróbuj ponownie"
                    ];
                    echo json_encode($json_error);
                }
            } elseif($action=="downgrade") {
                $query_downgrade = "UPDATE projects_assignments SET permissions = '1' WHERE user_id=".$userId." AND project_id=".$projectId.";";
                $result_downgra = $con->query($query_downgrade);
                if($result_downgra) {
                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '8', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', '".$userId."');";
                    $con->query($query_event);
                    $query_name = "SELECT login FROM `users` WHERE user_id=".$userId.";";
                    $result_name = $con->query($query_name);
                    if($result_name) {
                        $row = $result_name->fetch_assoc();
                        $userName = $row['login'];
                    } else {
                        $userName = "!!!";
                    }
                    $json_success = [
                        'success' => "obnizone_uprawnienia",
                        'project_id' => $projectId,
                        'user_id' => $userId,
                        'user_name' => $userName
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