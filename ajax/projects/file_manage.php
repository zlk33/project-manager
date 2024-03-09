<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["action"])) {
            $action=$_POST["action"];
            $projectId=$_POST["project_id"];
            $fileId=$_POST["file_id"];
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            if($action=="delete") {
                $query_file_name = "SELECT * FROM `projects_attachments` WHERE id=".$fileId.";";
                $result_file_name = $con->query($query_file_name);
                $query_delete="DELETE FROM projects_attachments WHERE id = ".$fileId." AND project_id=".$projectId.";";
                $result_delete= $con->query($query_delete);
                $row = $result_file_name->fetch_assoc();
                $file_name = $row["source"];
                if($result_delete) {
                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '9', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', 'NULL');";
                    $result_event=$con->query($query_event);
                    if($result_event) {
                        $path="../../uploads/projects/".$projectId."/".$file_name;
                        $delete_status=unlink($path);
                        if($delete_status) {
                            $json_success = [
                                'success' => "true",
                                'file_id' => $fileId,
                                'project_id' => $projectId
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