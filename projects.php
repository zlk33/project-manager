<?php
    session_start();
    include 'db_config.php';
    include 'functions.php';
    if(!isset($_SESSION['login'])){
		header("Location: login.php");
	}
    $project = false;
    $team_name="";

    if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET["id"])) {
        $project = true;
        $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

            if($con->connect_error) {
                die("Blad polaczenia z baza danych: " . $con->connect_error);
            }
            
            $id = mysqli_real_escape_string($con, $_GET['id']);
            $query = "SELECT * FROM `projects` WHERE project_id='$id'";
            $result = $con->query($query);
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $project_name = $row["name"];
                    $project_id = $row["project_id"];
                    $project_leader = $row["leader"];
                    $start_date = $row["start_date"];
                    $end_date = $row["end_date"];
                    $description = $row["description"];
                    $code = $row["code"];
                    $private = $row["private"];
                }
                $project=true;
                $query_permission = "SELECT permissions FROM `projects_assignments` WHERE user_id = '".$_SESSION['id']."' AND project_id ='".$id."';";
                $result_permission = $con->query($query_permission);
                if ($result_permission) {
                    if($result_permission->num_rows > 0) {
                        $row = $result_permission->fetch_assoc();
                        $permission = $row['permissions'];
                    } else {
                        $permission = 0;
                    }

                } 
            } else {
                header("Location: 404.php");
            }
    } else {
        $project = false;
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/main.css">
    <link rel="icon" type="image/png" href="imgs/logo256.png">
    <title><?php if($project) { echo "Projekt - ".$project_name.""; } else { echo "Projekty";} ?></title>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                JZ
            </div>
            <ul>
                <li><a href="profile.php"><i class="fa-regular fa-user"></i> <?php echo $_SESSION['login'] ?></a></li>
                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li>
            </ul>
        </div>
    </header>
    <div class="main">
        <aside class="sidebar">
            <ul>
                <li><a href="index.php" class="sidebar-button"><i class="fa-solid fa-house"></i></i> <span>Home</span></a></li>
                <li><a id="go_back" href="<?php if($project) { echo "projects.php"; } else { echo "#";} ?>" class="sidebar-button active"><i class="fa-solid fa-list-check"></i> <span>Projekty</span></a></li>
                <li><a href="tasks.php" class="sidebar-button"><i class="fa-solid fa-bars-progress"></i> <span>Zadania</span></a></li>
                <li><a href="schedule.php" class="sidebar-button"><i class="fa-solid fa-calendar-days"></i> <span>Harmonogram</span></a></li>
                <li class="spacer"></li>
                <li class="footer"><a href="https://github.com/zlk33" target="_blank">zlk <img alt="github_avatar" src="https://avatars.githubusercontent.com/u/101130006?v=4"> 2024</a></li>
            </ul>
        </aside>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <p id="panel_name"><?php if($project) { echo "Projekt - ".$project_name.""; } else { echo "Projekty";} ?></p>
                </div>
                <div class="panel-content">
                    <div id="project-list">
                    <?php
                        if($project) {
                            if($permission>1) {
                                echo '<div id="project-edit" style="display: none;">';
                                echo '<div class="box-100">';
                                echo '<a href="#" id="showProject" style="cursor: pointer;">> Powrót do widoku projektu</a>';
                                echo '<hr></div>';
                                echo '<div class="box-100">
                                <h3 style="margin-bottom: 8px;">Kod projektu: <span style="text-transform: uppercase;padding: 4px;border: 2px solid #00DB84;border-radius: 4px;">'.$code.'</span></h3>
                                <p style="color: gray">Wyślij kod innemu użytkownikowi aby zaprosić go do udziału w projekcie</p> 
                                <hr></div>';
                                if($permission==3) {
                                    echo '<div class="box-100">
                                    <button id="deleteProject" data-project_id="'.$project_id.'" class="btn btn-red">Usuń projekt</button>
                                    <hr></div>';
                                }
                                echo '<div class="box">';
                                echo '<h2>Informacje o projekcie</h2>';
                                echo '<form id="projectUpdateForm" class="form">
                                    <input type="hidden" name="projectId" id="projectId" value="'.$project_id.'"'; 
                                    if($permission!=3) {
                                        echo "disabled";
                                    }
                                    echo'>
                                    <label>Projekt prywatny:</label>
                                    <input type="checkbox" name="projectVisibility" id="projectVisibility" ';
                                    if($private) { echo 'checked ';}
                                    if($permission!=3) {
                                        echo "disabled";
                                    }
                                    echo '>';
                                    echo '<span class="formspan">Czy projekt ma być ukryty dla użytkowników którzy nie są jego członkami?</span>
                                    <label>Nazwa projektu:</label>
                                    <input type="text" placeholder="Nazwa projektu" name="projectName" value="'.$project_name.'" id="projectName" ';
                                    if($permission!=3) {
                                        echo "disabled";
                                    }
                                    echo ' required>
                                    <label>Opis projektu:</label>
                                    <textarea id="projectDescription" name="projectDescription" placeholder="Opis projektu" maxlength="512" ';
                                    if($permission!=3) {
                                        echo "disabled";
                                    }
                                    echo' >'.$description.'</textarea>
                                    <span class="charsleft">Max znaków: 512</span>
                                    <label>Data zakończenia:</label>
                                    <input type="date" id="end_date" name="end_date" value="'.$end_date.'" min="'.date('Y-m-d').'" ';
                                    if($permission!=3) {
                                        echo "disabled";
                                    }
                                    echo ' required>
                                    <button type="submit" class="btn" ';
                                    if($permission!=3) {
                                        echo "disabled";
                                    }   
                                    echo '>Zapisz</button>
                                </form>';
                                echo '<div id="projectUpdate"></div>';
                                echo '</div>';
                                echo '<div id="projectUsers" class="box box-v">';
                                echo '<h2>Członkowie projektu</h2>';
                                echo '<ul class="list">';
                                $query_users_list = "SELECT users.user_id,projects_assignments.permissions,users.login FROM `projects_assignments` JOIN users ON users.user_id = projects_assignments.user_id WHERE project_id=".$project_id.";";
                                $result_users_list = $con->query($query_users_list);
                                if($result_users_list->num_rows > 0) {
                                    while($row = $result_users_list->fetch_assoc()) {
                                        echo '<li class="user_'.$row["user_id"].'">';
                                        echo '<a href="profile.php?id='.$row["user_id"].'">'.$row["login"].'</a> ';
                                        if($row["permissions"]==3) {
                                            echo '<span class="badge leader">Lider</span>';
                                        } elseif($row["permissions"]==2) {
                                            echo '<span class="badge in-progress">Koordynator</span> ';
                                        }
                                        if($permission==3 || $row["permissions"]==1) {
                                            if($row["user_id"]!=$project_leader) {
                                                echo ' <button data-user-id="'.$row["user_id"].'" data-project-id="'.$project_id.'" class="btn btn-red btn-small ban_user">
                                                <i class="fa-solid fa-ban"></i>
                                                </button> ';
                                                echo ' <button data-user-id="'.$row["user_id"].'" data-project-id="'.$project_id.'" class="btn btn-red btn-small delete_user">
                                                <i class="fa-solid fa-trash-can"></i>
                                                </button>';
                                            }
                                        }
                                        if($permission==3) {
                                            if($row["permissions"]==2) {
                                                echo ' <button data-user-id="'.$row["user_id"].'" data-project-id="'.$project_id.'" class="btn btn-red btn-small downgrade_user">
                                                <i class="fa-solid fa-user-minus"></i>
                                                </button>';
                                            } elseif($row["permissions"]==1) {
                                                echo ' <button data-user-id="'.$row["user_id"].'" data-project-id="'.$project_id.'" class="btn btn-red btn-small upgrade_user">
                                                <i class="fa-solid fa-user-plus"></i>
                                                </button>';
                                            }

                                        }
                                        echo '</li>';
                                     }
                                } else {
                                    echo '<li>Wystąpił błąd!</li>';
                                }
                                echo '</ul>';
                                echo '</div>';
                                echo '<div class="box box-v">';
                                echo '<h2>Zadania</h2>';
                                echo '<button id="createNewTask" class="btn">Utwórz nowe zadanie</button>';
                                $query_tasks_list = "SELECT * FROM `tasks` WHERE project_id=".$project_id." ORDER BY status ASC";
                                $result_tasks_list = $con->query($query_tasks_list);
                                if($result_tasks_list->num_rows > 0) {
                                    echo '<ul class="list">';
                                    while($row = $result_tasks_list->fetch_assoc()) {
                                        echo '<li class="task_'.$row["id"].'"><a href="tasks.php?id='.$row["id"].'">'.$row["name"].'</a> ';
                                        if($row["status"]==0) {
                                            echo '<span class="badge in-progress">w trakcie</span>';
                                        } elseif($row["status"]==1) {
                                            echo '<span class="badge finished">ukończone</span>';
                                        } else {
                                            echo '<span class="badge not-finished">nieukończone</span>';
                                        }
                                        echo ' <button data-task-id="'.$row["id"].'" class="btn btn-red btn-small delete_task">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>';
                                    echo ' <button data-task-id="'.$row["id"].'" class="btn btn-small edit_task">
                                    <i class="fa-solid fa-pen"></i>
                                </button>';
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<h3>Brak zadań!</h3>';
                                }
                                echo '</div>';
                                echo '<div class="box box-v">';
                                echo '<h2>Pliki projektu</h2>';
                                $query_files_list = "SELECT projects_attachments.id AS file_id,source,date,login,owner FROM `projects_attachments` JOIN users ON users.user_id=projects_attachments.owner WHERE project_id=".$project_id."";
                                $result_files_list = $con->query($query_files_list);
                                if($result_files_list->num_rows > 0) {
                                    echo '<ul class="list">';
                                    while($row = $result_files_list->fetch_assoc()) {
                                        echo '<li class="file_'.$row["file_id"].'">';
                                        echo '<button data-file-id="'.$row["file_id"].'" data-project-id="'.$project_id.'" class="btn btn-red btn-small file_delete">
                                        <i class="fa-solid fa-trash-can"></i>
                                        </button>';
                                        echo ' <a target="_blank" href="uploads/projects/'.$project_id.'/'.$row["source"].'">'.skrocTekst($row["source"]).'</a> | <a class="badge badge-gray" href="profile.php?id='.$row["owner"].'">'.$row["login"].'</a> | '.$row["date"].'</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<h3 id="pm-nofiles">Brak plików!</h3>';
                                }
                                echo '</div>';
                                echo '<div class="box-100 box-v"><hr>
                                <h2>Lista zdarzeń</h2>';
                                echo '<ul id="event_list">
                                </ul>';
                                echo'</div>';
                                echo '</div>';
                            }
                            echo '<div id="project-view">';
                            if($private==0 || $permission>0) {
                                echo '<div class="box-100">
                                <a href="projects.php" style="cursor: pointer;">> Powrót do listy projektów</a>
                                </div>';
                                if($permission>0) {
                                    echo '<div class="box"><button id="projectChat" class="btn">Chat projektu</button>
                                     <button style="display: none" id="closeChat" class="btn btn-red">Zamknij chat</button>
                                    </div> ';
                                    echo '<div class="box">';
                                    if($_SESSION["id"]!=$project_leader) {
                                        echo ' <button id="leaveProject" data-project_id="'.$project_id.'" class="btn btn-red">Opuść projekt</button> ';
                                    }
                                    if($permission>1) {
                                        echo ' <button id="manageProject" class="btn btn-red">Zarządzaj projektem</button> ';
                                    }
                                    echo '</div>';
                                    echo '<div id="chatbox" style="display:none">';
                                    echo '<div class="box-100"><hr><h2>Chat</h2></div>';
                                    echo '<div class="box-100"><div class="chat-bg">
                                    <div class="chat">
                                        <div class="messages" id="loadMessages">

                                        </div>
                                    </div>
                                    <form id="message-form">
                                    <div class="inputs">
                                        <input type="hidden" id="mp_id" name="mp_id" value="'.$_GET["id"].'">
                                        <input placeholder="Napisz wiadomość" maxlength="512" name="message" id="message" type="text" required>
                                        <button type="submit" class="btn btn-send"><i class="fa-solid fa-paper-plane"></i></button>
                                    </div>
                                    </form>
                                </div></div>';
                                    echo '</div>';
                                }
                                
                                echo '<div class="box-100"><hr></div>';
                                
                                echo '<div class="box"><h2>Dane projektu</h2>
                                    <p><span>Data rozpoczęcia:</span> '.$start_date.'</p>
                                    <p id="dp_end_date"><span>Data zakończenia:</span> '.$end_date.'</p>
                                    <p><span>Opis:</span></p>
                                    <p id="dp_description"> '.$description.'</p>
                                </div>';
                                
                                
                                echo '<div class="box box-v"><h2>Członkowie projektu</h2>';
                                    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
                                    if($con->connect_error) {
                                        die("Blad polaczenia z baza danych: " . $con->connect_error);
                                    }
                                    $query_users = "SELECT login,users.user_id,permissions FROM `projects_assignments` JOIN users ON users.user_id=projects_assignments.user_id WHERE project_id='$id'";
                                    $result_users = $con->query($query_users);
                                    if($result_users->num_rows > 0) {
                                        echo '<ul class="list">';
                                        while($row = $result_users->fetch_assoc()) {
                                            echo '<li class="user_n_'.$row["user_id"].'"><a href="profile.php?id='.$row["user_id"].'">'.$row["login"].'</a> ';
                                            if($row["permissions"]==3) {
                                                echo '<span class="badge leader">Lider</span>';
                                            } elseif($row["permissions"]==2) {
                                                echo '<span class="badge in-progress">Koordynator</span> ';
                                            }
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo '-';
                                    }
                                    echo '</div>';
                                    
                                    
                                echo '<div class="box-100"><hr></div>';
                                
                                
                                echo '<div class="box"><h2>Postęp</h2>';
                                $query_postep = "SELECT count(id) AS ukonczone FROM `tasks` WHERE status=1 AND project_id='$id'";
                                $query_ilosc = "SELECT count(id) AS ilosc FROM `tasks` WHERE project_id='$id'";
                                $result_postep = $con->query($query_postep);
                                $result_ilosc = $con->query($query_ilosc);
                                if($result_ilosc) {
                                    $ilosc = $result_ilosc->fetch_assoc()["ilosc"];
                                }
                                if($result_postep) {
                                    $ukonczone = $result_postep->fetch_assoc()["ukonczone"];
                                }
                                if($ilosc>0) {
                                    $postep = round($ukonczone/$ilosc*100);
                                } else {
                                    $postep = 0;
                                }

                                echo '<div class="progress-bar" style="background-image: conic-gradient(#00DB84 '.$postep.'%, #464648 0);">';
                                echo '<div class="progress">'.$postep.'%</div>';
                                echo '</div>';
                                
                                echo '</div>';
                                
                                echo '<div class="box box-v"><h2>Zadania</h2>';
                                $query_zadania = "SELECT * FROM `tasks` WHERE project_id='$id' ORDER BY status";
                                $result_zadania = $con->query($query_zadania);
                                if($result_zadania->num_rows > 0) {
                                    echo '<ul class="list">';
                                    while($row = $result_zadania->fetch_assoc()) {
                                        echo '<li class="task_n_'.$row['id'].'"><a href="tasks.php?id='.$row["id"].'">'.$row["name"].'</a>';
                                        if($row["status"]==0) {
                                            echo ' <span class="badge in-progress">w trakcie</span> ';
                                        } elseif($row["status"]==1) {
                                            echo ' <span class="badge finished">ukończone</span> ';
                                        } else {
                                            echo ' <span class="badge not-finished">nieukończone</span>';
                                        }
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo "Brak zadań";
                                }
                                echo '</div>';
                                
                                echo '<div class="box-100">
                                <hr>';
                                echo '<div id="fileList"></div>';
                                if($permission>1) {
                                    echo '<form id="filesUpload">';
                                    echo '<input name="fileToUpload" id="fileToUpload" type="file" required>';
                                    echo '<input type="hidden" id="projectId" name="projectId" value="'.$_GET["id"].'" />';
                                    echo '<button class="btn btn-gray" type="submit">Dodaj plik</button>';
                                    echo '</form>';
                                    echo '<div id="error_alert" class="alert alert-red">
                                    <span class="title">Błąd!</span> <span id="error_alert_message"></span>
                                    </div>
                                    <div id="success_alert" class="alert alert-green">
                                    <span class="title">Sukces!</span> <span id="success_alert_message"></span>
                                    </div>  ';
                                }
                                echo '</div>';
                                
                            } else {
                                echo '<div class="box-100">
                                <a href="projects.php" style="cursor: pointer;">> Powrót do listy projektów</a>
                                <h1>Nie masz dostępu do tego projektu!</h1>
                                </div>';

                            }
                        echo '</div>';
                        } else {
                            echo '<div class="box-100">';
                            echo '<button id="create_project" class="btn mb-2">Utwórz nowy projekt</button>';
                            echo ' <button id="join_project" class="btn mb-2">Dołącz do projektu</button>';
                            echo '<h2>Lista Projektów</h2>';
                            echo '<hr>';
                            echo '</div>';
                            
                            $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);
                            $login = $_SESSION['login'];
                            if($con->connect_error) {
                                die("Blad polaczenia z baza danych: " . $con->connect_error);
                            }
                            $id = $_SESSION['id'];
                            $query = "SELECT * FROM `projects` JOIN projects_assignments ON projects.project_id=projects_assignments.project_id JOIN users ON users.user_id=projects.leader WHERE projects_assignments.user_id='$id'";
                            $result = $con->query($query);
                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<div class="box">';
                                    echo '<h2><a href="projects.php?id='.$row["project_id"].'">'.$row["name"].'</a></h2>';
                                    echo '<p><span>Data rozpoczęcia:</span> '.$row["start_date"].'</p>';
                                    echo '<p><span>Data zakończenia:</span> '.$row["end_date"].'</p>';
                                    echo '<p><span>Lider:</span> <a class="green" href="profile.php?id='.$row["leader"].'">'.$row["login"].'</a>';
                                    if($row["leader"]==$_SESSION["id"]) {
                                        echo '<span style="color: red">(Ty)</span>';
                                    }
                                    echo '</p>';
                                    echo '<p><span>Opis:</span> '.$row["description"].'</p>';
                                    $query_postep = "SELECT count(id) AS ukonczone FROM `tasks` WHERE status=1 AND project_id=".$row["project_id"]."";
                            $query_ilosc = "SELECT count(id) AS ilosc FROM `tasks` WHERE project_id=".$row["project_id"]."";
                            $result_postep = $con->query($query_postep);
                            $result_ilosc = $con->query($query_ilosc);
                            if($result_ilosc) {
                                $ilosc = $result_ilosc->fetch_assoc()["ilosc"];
                            }
                            if($result_postep) {
                                $ukonczone = $result_postep->fetch_assoc()["ukonczone"];
                            }
                            if($ilosc>0) {
                                $postep = round($ukonczone/$ilosc*100);
                            } else {
                                $postep = 0;
                            }
                            echo '<p><span>Postęp:</span></p>';
                            echo '<div class="progress-bar" style="background-image: conic-gradient(#00DB84 '.$postep.'%, #464648 0);">';
                            echo '<div class="progress">'.$postep.'%</div>';
                            echo '</div>';  
                            echo '<hr class="mt-2">';
                            echo '</div>';
                            }

                        } else {
                            echo "Nie jesteś członkiem żadnego projektu.";
                        }
                                }
                        ?>
                    </div>
                    <div id="ajax-result"></div>
                </div>
            </div>
        </section>
    </div>
    <script src="scripts/jquery-3.6.4.min.js"></script>
    <script src="scripts/projects.js"></script>
    <?php
        if($project && $permission>0) {
            echo '<script>
            $(document).ready(function() {
                $("#fileList").load("ajax/projects/file_list.php?id='.$_GET['id'].'");
                $("#event_list").load("ajax/projects/event_list.php?id='.$_GET['id'].'");
                $("#loadMessages").load("ajax/projects/load_chat.php?project_id='.$_GET['id'].'");
                setInterval(function() {
                    $("#loadMessages").load("ajax/projects/load_chat.php?project_id='.$_GET['id'].'");
                }, 1000);
            });
        </script>';
        }
    ?>
    <script src="https://kit.fontawesome.com/c9f993284f.js" crossorigin="anonymous"></script>
</body>
</html>