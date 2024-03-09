<?php
    session_start();
    include 'db_config.php';
    include 'functions.php';
    if(!isset($_SESSION['login'])){
		header("Location: login.php");
	}
    $task = false;
    $create = false;
    $edit = false;
    if(isset($_GET["id"]) && !empty($_GET["id"]) && is_numeric($_GET["id"])) {
        $task = true;
    }
    if(isset($_GET["edit"]) && !empty($_GET["edit"]) && isset($_GET["task_id"]) && !empty($_GET["task_id"])) {
        if($_GET["edit"]=="true") {
            $edit = true;
        } 
    }
    if(isset($_GET["create"]) && !empty($_GET["create"])) {
        if($_GET["create"]=="true") {
            $create = true;
        } 
    }


    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

    if ($con->connect_error) {
        die("Blad polaczenia: " . $con->connect_error);
    }

    if($task || $edit) {
        if($task) {
            $id = mysqli_real_escape_string($con, $_GET['id']);
        }
        if($edit) {
            $id = mysqli_real_escape_string($con, $_GET['task_id']);
        }
        $task_query = "SELECT * FROM `tasks` WHERE id=".$id.";";
        $result_task = $con->query($task_query);
        if($result_task) {
            if($result_task->num_rows > 0) {
                while($row = $result_task->fetch_assoc()) {
                    $task_name = $row["name"];
                    $task_id = $row["id"];
                    $project_id = $row["project_id"];
                    $description = $row["description"];
                    $status = $row["status"];
                    $leader = $row["leader"];
                    $start_date = $row["start_date"];
                    $end_date = $row["end_date"];
                }
                $query_permission = "SELECT COUNT(*) AS count,permissions FROM `tasks_assignment` WHERE task_id=".$task_id." AND user_id=".$_SESSION['id'].";";
                $result_permission = $con->query($query_permission);
                if($result_permission) {
                    $row = $result_permission->fetch_assoc();
                    $permission_level = $row['permissions'];
                    if($row['count']==0) {
                        $permission=false;
                    } else {
                        $permission=true;
                    }
                } else {
                    $task = false;
                    echo "Wystąpił błąd!";
                }
            } else {
                header("Location: 404.php");
            }
        } else {
            $task = false;
            echo "Wystąpił błąd!";
        }
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/main.css">
    <link rel="icon" type="image/png" href="imgs/logo256.png">
    <title><?php if($task) {echo "Zadanie - ".$task_name;} elseif($create) {echo "Tworzenie zadania";} elseif($edit) { echo "Edycja zadania"; } else { echo "Lista zadań"; } ?></title>
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
                <li><a href="projects.php" class="sidebar-button"><i class="fa-solid fa-list-check"></i> <span>Projekty</span></a></li>
                <li><a href="<?php if($task || $create || $edit) {echo "tasks.php";} else { echo "#";} ?>" class="active sidebar-button"><i class="fa-solid fa-bars-progress"></i> <span>Zadania</span></a></li>
                <li><a href="schedule.php" class="sidebar-button"><i class="fa-solid fa-calendar-days"></i> <span>Harmonogram</span></a></li>
                <li class="spacer"></li>
                <li class="footer"><a href="https://github.com/zlk33" target="_blank">zlk <img alt="github_avatar" src="https://avatars.githubusercontent.com/u/101130006?v=4"> 2024</a></li>
            </ul>
        </aside>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <p><?php if($task) { echo "Zadanie - ".$task_name;}elseif($create) {echo "Tworzenie zadania";} elseif($edit) { echo "Edycja zadania - ".$task_name; } else { echo "Lista zadań"; } ?></p>
                </div>
                <div class="panel-content">
                    <?php
                        if($task) {
                            if($permission) {
                                echo '<div class="box-100"><a href="tasks.php">> Powrót do listy zadań</a><hr></div>';
                                if($permission_level>0) {
                                echo '<div class="box"><button data-task-id="'.$_GET["id"].'" id="edit_task" class="btn btn-red">Edycja zadania</button></div>';
                                echo '<div class="box-100"><hr></div>';
                                }
                                $query_project_name = "SELECT name FROM `projects` WHERE project_id=".$project_id.";";
                                $result_project_name = $con->query($query_project_name);
                                $row_pm = $result_project_name->fetch_assoc();
                                $project_name = $row_pm["name"];
                                echo '<div class="box"><h2>Dane zadania</h2>
                                <p><span>Projekt:</span> <a class="green" href="projects.php?id='.$project_id.'">'.$project_name.'</a></p>
                                <p><span>Data rozpoczęcia:</span> '.$start_date.'</p>
                                <p class="mtb"><span>Status:</span> ';
                                if($status==0) {
                                    echo '<span class="badge in-progress">w trakcie</span>';
                                } elseif($status==1) {
                                    echo '<span class="badge finished">ukończone</span>';
                                } else {
                                    echo '<span class="badge not-finished">nieukończone</span>';
                                }
                                echo '</p>
                                <p><span>Data zakończenia:</span> '.$end_date.'</p>
                                <p><span>Opis:</span> '.$description.'</p>
                            </div>';
                                echo '<div class="box"><h2>Członkowie zadania</h2>';
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
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo '<p>Nikt nie jest przypisany do tego zadania!</p>';
                                    }
                                }
                                echo '</div>';
                                echo '<div class="box-100"><hr></div>';
                            } else {
                                echo '<div class="box-100"><a href="tasks.php">> Powrót do listy zadań</a></div>';
                                echo '<div class="box-100"><h2>Nie masz dostępu do tego zadania!</h2></div>';
                            }
                        } elseif($create) {
                            echo '<div class="box-100">
                            <a href="tasks.php" style="cursor: pointer;">> Powrót do listy zadań</a><hr></div>
                            <div class="box-100">
                            <h1>Tworzenie zadania</h1>
                            <form id="createTask" class="form">';
                            $query_project_list = "SELECT * FROM `projects_assignments` JOIN projects ON projects.project_id=projects_assignments.project_id WHERE projects_assignments.user_id=".$_SESSION['id']." AND projects_assignments.permissions>1;";
                            $result_project_list = $con->query($query_project_list);
                            if($result_project_list) {
                                if($result_project_list->num_rows > 0) {
                                    $today = date('Y-m-d');
                                    $nextdaydate = date('Y-m-d', strtotime($today . ' + 1 day'));
                                    echo '<label for="task_name"><span style="color: red">*</span> Nazwa zadania: </label>
                                    <input id="task_name" name="task_name" type="text" placeholder="Nazwa zadania">
                                    <label for="task_description">Opis zadania:</label>
                                    <textarea id="task_description" name="task_description" placeholder="Opis zadania" maxlength="512"></textarea>
                                    <span class="charsleft">Max znaków: 512</span>
                                    <label><span style="color: red">*</span> Data rozpoczęcia:</label>
                                    <input type="date" id="start_date" value="'.$today.'" min="'.$today.'" name="start_date">
                                    <label><span style="color: red">*</span> Data zakończenia:</label>
                                    <input type="date" id="end_date" value="'.$nextdaydate.'" min="'.$nextdaydate.'" name="end_date">
                                    <label for="project_id"><span style="color: red">*</span> Wybierz projekt:</label>
                                    <select name="project_id" id="project_id">
                                    <option value="0">--Lista projektów--</option>';
                                    while($row = $result_project_list->fetch_assoc()) {
                                        echo '<option value="'.$row["project_id"].'">'.$row["name"].'</option>';
                                    }
                                    echo '</select>
                                    <button type="submit" class="btn_submit">Utwórz zadanie</button>';
                                } else {
                                    echo '<h2>Nie możesz utworzyć zadania, ponieważ nie zarządzasz żadnym projektem</h2><br>';
                                }
                            } else {
                                echo "<p>Wystąpił błąd!</p>";
                            }
                            echo '</form>';
                            echo '<div id="error_alert" class="alert alert-red">
                                <span class="title">Błąd!</span> <span id="error_alert_message"></span>
                            </div>
                        </div>';
                        } elseif($edit) {
                            if($description=="-") {
                                $description="";
                            }
                            $today = date('Y-m-d');
                            $nextdaydate = date('Y-m-d', strtotime($today . ' + 1 day'));
                            echo '<div class="box-100"><a href="tasks.php?id='.$id.'">> Powrót do widoku zadania</a><hr></div>';
                            echo '<div class="box-100"><h2>Edycja zadania</h2><hr></div>';
                            echo '<div class="box-100" id="statusButtons">';

                            echo '</div>';
                            echo '<div class="box-100"><hr></div>';
                            echo '<div class="box-100" id="addUsersL">';
                            echo '</div>';
                            echo '<div class="box"><h3>Dane zadania</h3><hr>';
                            echo '<form class="form" id="taskUpdateForm">';
                            echo '<input type="hidden" name="task_id" value="'.$id.'" id="task_id_i">';
                            echo '<label for="task_name_i"><span style="color: red">*</span> Nazwa zadania: </label>
                            <input id="task_name_i" name="task_name" value="'.$task_name.'" type="text" placeholder="Nazwa zadania">
                            <label for="task_description_i">Opis zadania:</label>
                            <textarea id="task_description_i" name="task_description" placeholder="Opis zadania" maxlength="512">'.$description.'</textarea>
                            <span class="charsleft">Max znaków: 512</span>
                            <label><span style="color: red">*</span> Data rozpoczęcia:</label>
                            <input type="date" id="start_date_i" value="'.$start_date.'" name="start_date">
                            <label><span style="color: red">*</span> Data zakończenia:</label>
                            <input type="date" id="end_date_i" value="'.$end_date.'" name="end_date">
                            <button type="submit" class="btn_submit">Aktualizuj informacje</button>';
                            echo '</form>';
                            echo '<div id="updateStatus"></div>';
                            echo '</div>';
                            echo '<div class="box" id="taskUsersList"><h3>Członkowie zadania</h3><hr>';
                            
                            echo '</div>';
                        } else {
                            echo '<div class="box-100"><button id="createTaskBTN" class="btn">Utwórz zadanie</button></div>';
                            $query_lista = "SELECT * FROM `tasks` JOIN tasks_assignment ON tasks.id=tasks_assignment.task_id WHERE user_id=".$_SESSION['id'].";";
                            $result_lista = $con->query($query_lista);
                            if($result_lista) {
                                if($result_lista->num_rows > 0) {
                                    echo '<div class="box-100"><h2>Lista zadań</h2><hr></div>';
                                    while($row = $result_lista->fetch_assoc()) {
                                        echo '<div class="box">';
                                        echo '<h2><a href="tasks.php?id='.$row["task_id"].'">'.$row["name"].'</a></h2>';
                                        $query_project_name = "SELECT name FROM `projects` WHERE project_id=".$row["project_id"].";";
                                        $result_project_name = $con->query($query_project_name);
                                        $row_pm = $result_project_name->fetch_assoc();
                                        $project_name = $row_pm["name"];
                                        echo '<p><span>Projekt:</span> <a class="green" href="projects.php?id='.$row["project_id"].'">'.$project_name.'</a></p>';
                                        echo '<p><span>Data rozpoczęcia:</span> '.$row["start_date"].'</p>';
                                        echo '<p><span>Data zakończenia:</span> '.$row["end_date"].'</p>';
                                        echo '<p class="mtb"><span>Status:</span> ';
                                        if($row["status"]==0) {
                                            echo '<span class="badge in-progress">w trakcie</span>';
                                        } elseif($row["status"]==1) {
                                            echo '<span class="badge finished">ukończone</span>';
                                        } elseif($row["status"]==2) {
                                            echo '<span class="badge not-finished">nieukończone</span>';
                                        }
                                        echo '</p>';
                                        echo '<p><span>Opis: </span> '.$row["description"].'</p>';
                                        echo '<hr>';
                                        echo '</div>';
                                    }
                            } else {
                                echo '<div class="box-100"><hr><h2>Nie jesteś przypisany do żadnego zadania!</h2></div>';
                            }
                        } else {
                            echo '<div class="box-100"><a href="tasks.php">> Powrót do listy zadań</a></div>';
                            echo '<div class="box-100"><h2>Wystąpił błąd!</h2></div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
    <script src="scripts/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/c9f993284f.js" crossorigin="anonymous"></script>
    <script src="scripts/tasks.js"></script>
    <script>
        <?php if($edit) {
            echo '$("#statusButtons").load("ajax/tasks/load_buttons.php?task_id='.$id.'");';
            echo '$("#addUsersL").load("ajax/tasks/load_users.php?task_id='.$id.'&project_id='.$project_id.'");';
            echo '$("#taskUsersList").load("ajax/tasks/users_list.php?task_id='.$id.'");';
        }?>
    </script>
</body>
<?php $con->close(); ?>
</html>