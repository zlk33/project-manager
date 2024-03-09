<?php
    session_start();
    if(!isset($_SESSION['login'])){
		header("Location: ../../login.php");
	}
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET["project_id"]) && !empty($_GET["project_id"])) {
            include '../../db_config.php';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if ($con->connect_error) {
                die("Blad polaczenia: " . $con->connect_error);
            }
            $project_id = mysqli_real_escape_string($con, $_GET["project_id"]);
            $query = "SELECT messages.sender_id,messages.date,messages.message,users.login FROM `messages` JOIN users ON messages.sender_id=users.user_id WHERE messages.mproject_id=".$project_id.";";
            $result = $con->query($query);
            if($result->num_rows > 0) {
                echo '<ul>';
                while($row = $result->fetch_assoc()) {
                    $time = strtotime($row["date"]);
                    $data = date('H:i Y-m-d', $time);
                    echo '<li ';
                    if($row["sender_id"]==$_SESSION["id"]) {
                        echo 'class="my_message"';
                    }
                    echo '><p class="nick">'.$row["login"].'</p><div class="message">'.$row["message"].'</div><p class="data">'.$data.'</p></li>';
                }
                echo '</ul>';
            } else {
                echo '<h3 style="text-align: center;">Nie ma żadnych wiadomości!</h3>';
            }
        } else {
            echo '<h3 style="text-align: center;">Wystąpił błąd!</h3>';
        }
    } else {
        echo "Błąd zapytania!";
    }

?>