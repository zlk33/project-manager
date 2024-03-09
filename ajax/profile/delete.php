<?php

    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["user_id"]) && $_POST["user_id"]==$_SESSION["id"]) {
            $id = $_SESSION["id"];
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
    
            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $query1 = "DELETE FROM messages WHERE sender_id = ".$id.";";
            $query2 = "DELETE FROM projects WHERE leader = ".$id.";";
            $query3 = "DELETE FROM projects_assignments WHERE user_id = ".$id.";";
            $query4 = "DELETE FROM projects_attachments WHERE owner = ".$id.";";
            $query5 = "DELETE FROM projects_events WHERE event_user_id = ".$id." OR affected_user_id =".$id.";";
            $query6 = "DELETE FROM project_bans WHERE b_user_id = ".$id.";";
            $query7 = "DELETE FROM tasks WHERE leader = ".$id.";";
            $query8 = "DELETE FROM tasks_assignment WHERE user_id = ".$id.";";
            $query9 = "DELETE FROM users WHERE user_id = ".$id.";";
            $result1 = $con->query($query1);
            $result2 = $con->query($query2);
            $result3 = $con->query($query3);
            $result4 = $con->query($query4);
            $result5 = $con->query($query5);
            $result6 = $con->query($query6);
            $result7 = $con->query($query7);
            $result8 = $con->query($query8);
            $result9 = $con->query($query9);
            if($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7 && $result8 && $result9) {
                $json_success = [
                    'success' => true
                ];
                echo json_encode($json_success);
            } else {
                $json_error = [
                    'error' => "Wystąpił błąd!-3"
                ];
                echo json_encode($json_error); 
            }
            $con->close();
        } else {
            $json_error = [
                'error' => "Wystąpił błąd!-2"
            ];
            echo json_encode($json_error);
        }
    } else {
        $json_error = [
            'error' => "Wystąpił błąd!-1"
        ];
        echo json_encode($json_error);
    }

?>