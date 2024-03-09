<?php
    session_start();
    include 'db_config.php';
    if(!isset($_SESSION['login'])){
		header("Location: login.php");
	}
    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

    if($con->connect_error) {
        die("Blad polaczenia z baza danych: " . $con->connect_error);
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/main.css">
    <link rel="icon" type="image/png" href="imgs/logo256.png">
    <title>Harmonogram</title>
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
                <li><a href="tasks.php" class="sidebar-button"><i class="fa-solid fa-bars-progress"></i> <span>Zadania</span></a></li>
                <li><a href="#" class="active sidebar-button"><i class="fa-solid fa-calendar-days"></i> <span>Harmonogram</span></a></li>
                <li class="spacer"></li>
                <li class="footer"><a href="https://github.com/zlk33" target="_blank">zlk <img alt="github_avatar" src="https://avatars.githubusercontent.com/u/101130006?v=4"> 2024</a></li>
            </ul>
        </aside>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <p>Harmonogram</p>
                </div>
                <div class="panel-content">
                    <div class="box-100"><h2>Harmognogram</h2><hr></div>
                    <div class="box box-v"><h3>Projekty</h3><hr>
                        <?php
                            $query_projekty = "SELECT *,DATEDIFF(end_date, CURDATE()) AS days_left FROM `projects_assignments` JOIN projects ON projects.project_id=projects_assignments.project_id WHERE projects_assignments.user_id=".$_SESSION['id']." AND end_date >= CURDATE() ORDER BY days_left";
                            $result_projekty = $con->query($query_projekty);
                            if($result_projekty->num_rows > 0) {
                                echo '<ul class="list">';
                                while($row = $result_projekty->fetch_assoc()) {
                                    echo '<li><a class="green" href="projects.php?id='.$row["project_id"].'">'.$row["name"].'</a>, zostało '.$row["days_left"].' dni</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Nie należysz do żadnego projektu!</p>';
                            }
                        ?>
                    </div>
                    <div class="box box-v"><h3>Zadania</h3><hr>
                        <?php
                            $query_zadania = "SELECT *,DATEDIFF(end_date, CURDATE()) AS days_left FROM `tasks_assignment` JOIN tasks ON tasks.id=tasks_assignment.task_id WHERE tasks_assignment.user_id=".$_SESSION['id']." AND end_date >= CURDATE() ORDER BY days_left;";
                            $result_zadania = $con->query($query_zadania);
                            if($result_zadania->num_rows > 0) {
                                echo '<ul class="list">';
                                while($row = $result_zadania->fetch_assoc()) {
                                    echo '<li><a class="green" href="tasks.php?id='.$row["task_id"].'">'.$row["name"].'</a>, zostało '.$row["days_left"].' dni</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Nie jesteś przypisany do żadnego zadania!</p>';
                            }
                        ?>
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
    <script src="scripts/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/c9f993284f.js" crossorigin="anonymous"></script>
</body>
</html>
