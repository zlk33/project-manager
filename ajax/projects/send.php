<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["message"]) && !empty($_POST["message"]) && isset($_POST["project_id"]) && !empty($_POST["project_id"])) {
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $message = strip_tags($_POST["message"]);
            $message = mysqli_real_escape_string($con, $message);
            $project_id = mysqli_real_escape_string($con, $_POST["project_id"]);
            $query = "INSERT INTO `messages` (`id`, `sender_id`, `mproject_id`, `message`, `date`) VALUES (NULL, '".$_SESSION['id']."', '".$project_id."', '".$message."', '".date("Y-m-d H:i:s")."');";
            $result = $con->query($query);
            if($result) {
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
            $con->close();
        } else {
            $json_error = [
                'error' => "Wystąpił błąd"
            ];
            echo json_encode($json_error);
        }
    } else {
        echo "Błąd zapytania";
    }
?>