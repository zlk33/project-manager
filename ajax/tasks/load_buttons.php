<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET["task_id"]) && !empty($_GET["task_id"])) {
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
    
            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $query="SELECT tasks.status FROM `tasks` WHERE id=".$_GET["task_id"].";";
            $result = $con->query($query);
            if($result) {
                $row=$result->fetch_assoc();
                $status=$row["status"];
                if($status!=0) {
                    echo ' <button type="submit" data-task-id="'.$_GET["task_id"].'" id="changeStatusInprogress" class="btn btn-yellow">W trakcie</button> ';
                }
                if($status!=1) {
                    echo ' <button type="submit" data-task-id="'.$_GET["task_id"].'" id="changeStatusFinished" class="btn">Ukończone</button> ';
                }
                if($status!=2) {
                    echo ' <button type="submit" data-task-id="'.$_GET["task_id"].'" id="changeStatusNotFinished" class="btn btn-red">Nieukończone</button> ';
                }
            } else {
                echo "Wystąpił błąd-2";
            }
        } else {
            echo "Wystąpił błąd-1";
        }
    } else {
        echo "Błąd zapytania";
    }
?>