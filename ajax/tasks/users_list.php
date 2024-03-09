<?php
    session_start();
    if(!isset($_SESSION['login'])) {
        echo "nie jesteś zalogowany!";
        return;
	}
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET["task_id"])) {
            if(!empty($_GET["task_id"])) {
                include '../../db_config.php';
                $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
    
                if ($con->connect_error) {
                    die("Blad polaczenia: " . $con->connect_error);
                }
                $task_id = mysqli_real_escape_string($con,$_GET["task_id"]);
                $query_permissions = "SELECT permissions FROM `tasks_assignment` WHERE user_id=".$_SESSION['id'].";";
                $result_permissions = $con->query($query_permissions);
                if($result_permissions) {
                    $row = $result_permissions->fetch_assoc();
                    $permissions = $row["permissions"];
                } else {
                    $permissions = 0;
                }
                $query_task_leader = "SELECT leader FROM `tasks` WHERE id=".$task_id.";";
                $result_task_leader = $con->query($query_task_leader);
                if($result_task_leader) {
                    $row = $result_task_leader->fetch_assoc();
                    $leader = $row["leader"];
                }
                $query_project_id = "SELECT project_id FROM `tasks` WHERE id=".$task_id.";";
                $result_project_id = $con->query($query_project_id);
                if($result_project_id) {
                    $row = $result_project_id->fetch_assoc();
                    $project_id = $row["project_id"];
                }
                echo '<h3>Członkowie zadania</h3><hr>';
                $query_lista_czlonkow = "SELECT * FROM `tasks_assignment` JOIN users ON tasks_assignment.user_id=users.user_id WHERE task_id=".$task_id.";";
                $result_lista_czlonkow = $con->query($query_lista_czlonkow);
                if($result_lista_czlonkow) {
                    if($result_lista_czlonkow->num_rows > 0) {
                        echo '<ul>';
                        while($rowl = $result_lista_czlonkow->fetch_assoc()) {
                            echo '<li class="mtb"><a href="profile.php?id='.$rowl["user_id"].'">'.$rowl["login"].'</a> ';
                            if($rowl["permissions"]==1) {
                                echo '<span class="badge in-progress">Koordynator</span>';
                            } elseif($rowl["permissions"]==2) {
                                echo '<span class="badge leader">Lider</span>';
                            }
                            if($rowl["user_id"]!=$_SESSION["id"] && $rowl["user_id"]!=$leader) {
                                echo ' <button data-user-id="'.$rowl["user_id"].'" data-project-id="'.$project_id.'" data-task-id="'.$task_id.'" class="btn btn-red btn-small delete_user">
                                <i class="fa-solid fa-trash-can"></i>
                                </button>';
                            }
                            if($rowl["permissions"]==0 && $rowl["user_id"]!=$_SESSION["id"]) {
                                echo ' <button data-user-id="'.$rowl["user_id"].'" data-task-id="'.$task_id.'" class="btn btn-red btn-small upgrade_user">
                                <i class="fa-solid fa-user-plus"></i>
                                </button>';
                            }
                            if($rowl["permissions"]==1 && $rowl["user_id"]!=$_SESSION["id"]) {
                                echo ' <button data-user-id="'.$rowl["user_id"].'" data-task-id="'.$task_id.'" class="btn btn-red btn-small downgrade_user">
                                <i class="fa-solid fa-user-minus"></i>
                                </button>';
                                echo '</li>';
                            }
                        }
                        echo '</ul>';
                     } else {
                        echo '<p>Nikt nie jest przypisany do tego zadania!</p>';
                        }
                } 
            } else {
                echo "Wystąpił błąd -2";
            }
        } else {
            echo "Wystąpił błąd -1";
        }
    } else {
        echo "Błąd zapytania";
    }
?>