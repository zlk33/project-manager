<?php
    session_start();

    include 'db_config.php';
    include 'functions.php';

    if(!isset($_SESSION['login'])){
		header("Location: login.php");
	}
    $con = new mysqli($db_ip, $db_login, $db_pass, $db_name);

    if($con->connect_error) {
        die("Blad polaczenia z baza danych: " . $con->connect_error);
    }

    $query_imie = "SELECT first_name FROM `users` WHERE user_id=".$_SESSION['id'].";";
    $result_imie = $con->query($query_imie);
    $row = $result_imie->fetch_assoc();
    if(!empty($row["first_name"])) {
        $imie = $row["first_name"];
    } else {
        $imie = $_SESSION["login"];
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/main.css">
    <link rel="icon" type="image/png" href="imgs/logo256.png">
    <title>Strona główna</title>
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
                <li><a href="#" class="active sidebar-button"><i class="fa-solid fa-house"></i></i> <span>Home</span></a></li>
                <li><a href="projects.php" class="sidebar-button"><i class="fa-solid fa-list-check"></i> <span>Projekty</span></a></li>
                <li><a href="tasks.php" class="sidebar-button"><i class="fa-solid fa-bars-progress"></i> <span>Zadania</span></a></li>
                <li><a href="schedule.php" class="sidebar-button"><i class="fa-solid fa-calendar-days"></i> <span>Harmonogram</span></a></li>
                <li class="spacer"></li>
                <li class="footer"><a href="https://github.com/zlk33" target="_blank">zlk <img alt="github_avatar" src="https://avatars.githubusercontent.com/u/101130006?v=4"> 2024</a></li>
            </ul>
        </aside>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <p>Strona główna</p>
                </div>
                <div class="panel-content">
                    <div class="box-100"><h1>Witaj, <?php echo $imie;?>!</h1><hr></div>
                    <div class="box">
                        <h2>Twoje najnowsze projekty</h2>
                        <?php
                            $query1 = "SELECT * FROM `projects` WHERE leader=".$_SESSION['id']." ORDER BY project_id DESC LIMIT 5;";
                            $result1 = $con->query($query1);
                            if($result1->num_rows > 0) {
                                echo '<ul class="list">';
                                while($row = $result1->fetch_assoc()) {
                                    echo '<li><a class="green" href="projects.php?id='.$row["project_id"].'">'.$row["name"].'</a></li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Nie masz jeszcze żadnego projektu! Utwórz go w <a class="green" href="projects.php">zakładce projekty</a>!</p>';
                            }
                        ?>
                    </div>
                
                    <div class="box">
                        <h2>Projekty w których uczestniczysz</h2>
                        <?php
                            $query2 = "SELECT * FROM `projects_assignments` JOIN projects ON projects.project_id=projects_assignments.project_id WHERE projects_assignments.user_id=".$_SESSION['id']." ORDER BY projects.project_id DESC LIMIT 5;";
                            $result2 = $con->query($query2);
                            if($result2->num_rows > 0) {
                                echo '<ul class="list">';
                                while($row = $result2->fetch_assoc()) {
                                    echo '<li><a class="green" href="projects.php?id='.$row["project_id"].'">'.$row["name"].'</a></li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Nie uczestniczysz w żadnym projekcie!</p> <p>Dołącz do projektu lub utwórz własny w <a class="green" href="projects.php">zakładce projekty</a>!</p>';
                            }
                        ?>
                    </div>

                    <div class="box">
                        <h2>Zbliżające się terminy</h2>
                        <?php
                            $query3 = "SELECT *,DATEDIFF(end_date, CURDATE()) AS days_left FROM `projects_assignments` JOIN projects ON projects.project_id=projects_assignments.project_id WHERE projects_assignments.user_id=".$_SESSION['id']." AND end_date >= CURDATE() ORDER BY days_left ASC LIMIT 5;";
                            $result3 = $con->query($query3);
                            if($result3->num_rows > 0) {
                                echo '<ul class="list">';
                                while($row = $result3->fetch_assoc()) {
                                    echo '<li><a class="green" href="projects.php?id='.$row["project_id"].'">'.$row["name"].'</a>, pozostało '.$row["days_left"].' dni</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Brak zbliżających się terminów!</p>';
                            }
                        ?>
                    </div>

                    <div class="box">
                        <h2>Zadania do ukończenia</h2>
                        <?php
                            $query4 = "SELECT * FROM `tasks` JOIN tasks_assignment ON tasks.id=tasks_assignment.task_id WHERE user_id=".$_SESSION['id']." AND status=0 AND leader!=".$_SESSION['id'].";";
                            $result4 = $con->query($query4);
                            if($result4->num_rows > 0) {
                                echo '<ul class="list">';
                                while($row = $result4->fetch_assoc()) {
                                    echo '<li><a class="green" href="tasks.php?id='.$row["task_id"].'">'.$row["name"].'</a></li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>Nie masz żadnych zadań do ukończenia!</p>';
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
