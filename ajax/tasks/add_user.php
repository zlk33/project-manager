<?php

//dodawanie usera, musi zwracac data.project_id
session_start();
if(!isset($_SESSION['login'])) {
    echo "nie jesteś zalogowany!";
    return;
}
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST["user_id"]) && isset($_POST["task_id"])) {
         if(!empty($_POST["user_id"]) && !empty($_POST["task_id"])) {
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $user_id = mysqli_real_escape_string($con, $_POST["user_id"]);
            $task_id = mysqli_real_escape_string($con, $_POST["task_id"]);
            $query="INSERT INTO `tasks_assignment` (`id`, `task_id`, `user_id`, `permissions`) VALUES (NULL, '".$task_id."', '".$user_id."', '0');";
            $result = $con->query($query);
            if($result) {
                $query2 = "SELECT project_id FROM `tasks` WHERE id=".$task_id.";";
                $result2 = $con->query($query2);
                if($result2) {
                    $row = $result2->fetch_assoc();
                    $project_id = $row["project_id"];
                    $json_success = [
                        'success' => true,
                        'project_id' => $project_id
                    ];
                    echo json_encode($json_success);
                } else {
                    $json_error = [
                        'error' => "Wystąpił błąd!-4"
                    ];
                    echo json_encode($json_error);
                }
            } else {
                $json_error = [
                    'error' => "Wystąpił błąd!-4"
                ];
                echo json_encode($json_error);
            }
         } else {
            $json_error = [
                'error' => "Wystąpił błąd!-3"
            ];
            echo json_encode($json_error);
         }
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