<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_FILES['plik']) && isset($_POST['projectId'])) {
            $projectId = $_POST['projectId'];
            $path = "../../uploads/projects/".$projectId."/";
            $nazwa_pliku = $_FILES['plik']['name'];
            $lokalizacja = $path . $nazwa_pliku;
            if(!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if(file_exists($lokalizacja)) {
                $json_error = [
                    'error' => "Przesyłany plik jest już dołączony!"
                ];
                echo json_encode($json_error);
                return;
            }
            if(move_uploaded_file($_FILES['plik']['tmp_name'], $lokalizacja)){
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                $file_name = mysqli_real_escape_string($con, $nazwa_pliku);
                $query = "INSERT INTO `projects_attachments` (`id`, `project_id`, `date`, `source`, `owner`) VALUES (NULL, '".$projectId."', '".date('Y-m-d H:i:s')."', '".$file_name."', '".$_SESSION['id']."');";
                $result = $con->query($query);
                if($result) {
                    $file_id = $con->insert_id;
                    $json_success = [
                        'success' => $projectId
                    ];
                    $query_event = "INSERT INTO `projects_events` (`event_id`, `project_id`, `event`, `date`, `file_id`, `task_id`, `event_user_id`, `affected_user_id`) VALUES (NULL, '".$projectId."', '3', '".date("Y-m-d H:i:s")."', '".$file_id."', 'NULL', '".$_SESSION['id']."', 'NULL');";
                    $con->query($query_event);
                    echo json_encode($json_success);
                } else {
                    $json_error = [
                        'error' => "Błąd przesyłania pliku"
                    ];
                    echo json_encode($json_error);
                }
            } else {
                $json_error = [
                    'error' => "Błąd przesyłania pliku"
                ];
                echo json_encode($json_error);
            }
        } else {
            $json_error = [
                'error' => "Błąd wybranego pliku"
            ];
            echo json_encode($json_error);
        }
    } else {
        $json_error = [
            'error' => "Wystąpił błąd!"
        ];
        echo json_encode($json_error);
    }
?>
