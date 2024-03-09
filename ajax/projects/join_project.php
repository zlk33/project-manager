<?php
    session_start();
    if(!isset($_SESSION['id'])){
		header("Location: ../../login.php");
	}
    include '../../db_config.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['project_code']) && !empty($_POST['project_code'])) {

            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $code_secure = mysqli_real_escape_string($con, $_POST['project_code']);
            $query = "SELECT * FROM `projects` WHERE code ='$code_secure'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $id_projektu = $row['project_id'];
                $nazwa_projektu = $row['name'];
                $query = "SELECT * FROM `projects_assignments` WHERE project_id=".$id_projektu." AND user_id=".$_SESSION['id']."";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo json_encode(['error'=>"Jesteś już członkiem tego projektu!"]);
                } else {
                    $query_ban = "SELECT COUNT(*) AS count FROM `project_bans` WHERE b_user_id=".$_SESSION['id']." AND b_project_id=".$id_projektu.";";
                    $result_ban = $con->query($query_ban);
                    if($result_ban) {
                        $row = $result_ban->fetch_assoc();
                        if($row['count']==0) {
                            $query = "INSERT INTO `projects_assignments` (`id`, `project_id`, `user_id`, `permissions`) VALUES (NULL, '".$id_projektu."', '".$_SESSION['id']."', '1');";
                            $join_project_result = $con->query($query);
                            if($join_project_result) {
                                $data = [
                                    'success' => $id_projektu,
                                    'name' => $nazwa_projektu
                                ];
                                $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$id_projektu."', '2', '".date("Y-m-d H:i:s")."', 'NULL', 'NULL', '".$_SESSION['id']."', 'NULL');";
                                $con->query($query_event);
                                echo json_encode($data);
                            } else {
                                echo json_encode(['error'=>"Wystąpił błąd! Spróbuj ponownie!"]);
                            }
                        } else {
                            echo json_encode(['error'=>"Zostałeś zbanowany i nie możesz dołączyć do projektu!"]);
                        }
                    } else {
                        echo json_encode(['error'=>"Wystąpił błąd!"]);
                    }
                }
            } else {
                echo json_encode(['error'=>"Podany przez ciebie kod nie istnieje!"]);
            }
        } else {
                echo "błąd zapytania";
            }
    } else {
        echo "błąd zapytania";
    }
    
?>