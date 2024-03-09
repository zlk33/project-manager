<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            include '../../db_config.php';
            include '../../functions.php';
            echo '<h2>Dołączone pliki</h2>';
            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
            if($con->connect_error) {
                die("Blad polaczenia z baza danych: " . $con->connect_error);
            }
            $query = "SELECT projects_attachments.id AS file_id,source,date,login,owner FROM `projects_attachments` JOIN users ON users.user_id=owner WHERE project_id='$id'";
            $result = $con->query($query);
            if($result->num_rows > 0) {
                echo '<ul class="file-list">';
                while($row = $result->fetch_assoc()) {
                    echo '<li class="file-item file_'.$row["file_id"].'">
                    '.rodzajPliku($row["source"]).'
                    <p><a target="_blank" href="uploads/projects/'.$id.'/'.$row["source"].'">'.$row["source"].'</a></p>
                    <p style="color: #737373;font-size: small">'.$row["date"].'</p>
                    <p><a href="profile.php?id='.$row["owner"].'">'.$row["login"].'</a></p>
                    </li>';
                }
                echo '</ul>';
            } else {
                echo "<p>Brak dołączonych plików.</p>";
            }
        } else {
            echo "Wystąpił błąd!";
        }
    } else {
        echo "Wystąpił błąd!";
    }
?>