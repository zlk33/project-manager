<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET["project_id"]) && isset($_GET["task_id"])) {
            if(!empty($_GET["project_id"]) && !empty($_GET["task_id"])) {
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }

                $id = mysqli_real_escape_string($con, $_GET["task_id"]);
                $project_id = mysqli_real_escape_string($con, $_GET["project_id"]);
                echo'<h3>Przypisz osobe do zadania</h3>';
                $query = "SELECT users.user_id,users.login FROM users JOIN projects_assignments ON projects_assignments.user_id=users.user_id WHERE projects_assignments.project_id=".$project_id." AND users.user_id NOT IN (SELECT tasks_assignment.user_id FROM tasks_assignment WHERE tasks_assignment.task_id=".$id.");";
                $result = $con->query($query);
                if($result->num_rows > 0) {
                    echo '<select id="addUser">';
                    while($row = $result->fetch_assoc()) {
                        echo '<option value="'.$row["user_id"].'">'.$row["login"].'</option>';
                    }
                    echo '</select>';
                    echo ' <button id="addUserBTN" data-task-id="'.$id.'" style="height: 49px;" class="btn">Dodaj</button>';
                } else {
                    echo '<h4>Zaproś kogoś do projektu aby przypisać go do zadania!</h4>';
                }
                echo '<hr>';
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