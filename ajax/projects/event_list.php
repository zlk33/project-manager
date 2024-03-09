<?php

    include '../../db_config.php';
    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

    if ($con->connect_error) {
        die("Blad polaczenia: " . $con->connect_error);
    }

    function event($event_id,$event,$date,$file_id,$task_id,$event_user_id,$affected_user_id,$event_user_name,$affected_user_name) {
        if ($event == 1) {
            //Utworzenie projektu
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> utworzył projekt</li>';
        } elseif ($event == 2) {
            //Dołączenie do projektu
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> dołączył do projektu</li>';
        } elseif ($event == 3) {
            //Dodanie pliku
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> dodał plik do projektu</li>';
        } elseif ($event == 4) {
            //Opuszczenie projektu
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> opuśćił projekt</li>';
        } elseif ($event == 5) {
            //Usuniecie kogos z projektu
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> usunął <a class="green" href="profile.php?id='.$affected_user_id.'">'.$affected_user_name.'</a> z projektu</li>';
        } elseif ($event == 6) {
            //Zbanowanie kogos z projektu
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> zbanował <a class="green" href="profile.php?id='.$affected_user_id.'">'.$affected_user_name.'</a> z projektu</li>';
        } elseif ($event == 7) {
            //Zwiekszenie uprawnien
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> zwiększył uprawnienia <a class="green" href="profile.php?id='.$affected_user_id.'">'.$affected_user_name.'</a></li>';
        } elseif ($event == 8) {
            //Zmniejszenie uprawnien
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> zmniejszył uprawnienia <a class="green" href="profile.php?id='.$affected_user_id.'">'.$affected_user_name.'</a></li>';
        } elseif ($event == 9) {
            //Usuniecie pliku
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> usunął plik</li>';
        } elseif ($event == 10) {
            //Aktualizacja informacji o projekcie
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> zaaktualizowal informacje o projekcie</li>';
        } elseif ($event == 11) {
            //Usuniecie zadania
            echo '<li> '.$date.' | <a class="green" href="profile.php?id='.$event_user_id.'">'.$event_user_name.'</a> usunął zadanie</li>';
        }
    }
    if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["id"])) {
        $projectId=$_GET["id"];
        $query = "SELECT * FROM `projects_events` WHERE project_id=".$projectId." ORDER BY event_id DESC";
        $result = $con->query($query);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row["event_user_id"]!=0) {
                    $query1="SELECT login FROM `users` WHERE user_id=".$row['event_user_id']."";
                    $result1 = $con->query($query1);
                    $nick1 = $result1->fetch_assoc();
                    $event_user_name = $nick1["login"];
                } else {
                    $event_user_name = "-";
                }
                if($row["affected_user_id"]!=0) {
                    $query2="SELECT login FROM `users` WHERE user_id=".$row['affected_user_id']."";
                    $result2 = $con->query($query2);
                    $nick2 = $result2->fetch_assoc();
                    $affected_user_name = $nick2["login"];
                } else {
                    $affected_user_name = "-";
                }
                echo event($row["event_id"],$row["event"],$row["date"],$row["file_id"],$row["task_id"],$row["event_user_id"],$row["affected_user_id"],$event_user_name,$affected_user_name);
        }

    } else {
        echo "Nie jesteś członkiem żadnego projektu.";
    }
    } else {
        echo "Błąd zapytania";
    }
    $con->close();
?>
