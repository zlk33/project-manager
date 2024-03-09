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

    if(isset($_GET["id"]) && !empty($_GET["id"])) {
        $id=$_GET["id"];
        if(is_numeric($id)) {
            $query1 = "SELECT COUNT(*) as count FROM `users` WHERE user_id=".$_GET["id"].";";
            $result1 = $con->query($query1);
            $row = $result1->fetch_assoc();
            if($row["count"]==0) {
                header("Location: 404.php");
            }
            if($_GET["id"]==$_SESSION["id"]) {
                $own_profile=true;
            } else {
                $own_profile=false;
            }
        } else {
            header("Location: 404.php");
        }
    } else {
        $own_profile=true;
    }
    if($own_profile) {
        $id=$_SESSION["id"];
    } else {
        $id=$_GET["id"];
    }
    $query = "SELECT * FROM users WHERE user_id='$id'";
    $result = $con->query($query);
    if($result) {
        while($row = $result->fetch_assoc()) {
            $uid = $row["user_id"];
            $login = $row["login"];
            $email = $row["email"];
            $first_name = $row["first_name"];
            $last_name = $row["last_name"];
            $company = $row["company_name"];
            $dateoac = $row["dateoac"];
        }
    }
    function emptyS($text) {
        if(empty($text)) {
            return "-";
        } else {
            return $text;
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
    <title>Profil - <?php if($own_profile) { echo $_SESSION['login']; } else { echo $login;} ?></title>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                JZ
            </div>
            <ul>
                <li><a href="<?php if(!$own_profile){ echo "profile.php"; } else { echo "#"; }?>"><i class="fa-regular fa-user"></i> <?php echo $_SESSION['login'] ?></a></li>
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
                <li><a href="schedule.php" class="sidebar-button"><i class="fa-solid fa-calendar-days"></i> <span>Harmonogram</span></a></li>
                <li class="spacer"></li>
                <li class="footer"><a href="https://github.com/zlk33" target="_blank">zlk <img alt="github_avatar" src="https://avatars.githubusercontent.com/u/101130006?v=4"> 2024</a></li>
            </ul>
        </aside>

        <section class="content">
            <div class="panel">
                <div class="panel-header">
                    <p>Profil użytkownika</p>
                </div>
                <div class="panel-content">
                    <?php
                        if($own_profile) {
                            echo '<div class="box-100">';
                            echo '<button id="edit" class="btn btn-red">Edytuj profil</button>';
                            echo ' <button id="delete_user" data-user-id="'.$_SESSION["id"].'" class="btn btn-red">Usuń profil</button>';
                            echo ' <button style="display: none" id="close_edit" class="btn btn-red">Zakończ edycje</button>';
                            echo '<hr></div>';
                        }
                    ?>
                    <div id="profile-view" class="box-100">
                        <h2>Informacje</h2>
                        <div class="p1">
                        <div class="avatar">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        </div>
                        <div class="p2">
                        <?php
                            echo '<p><span>Nazwa:</span> '.$login.'</p>';
                            echo '<p><span>Email:</span> <a class="green" href="mailto: '.$email.'">'.$email.'</a></p>';
                            echo '<p id="imie"><span>Imie:</span> '.emptyS($first_name).'</p>';
                            echo '<p id="nazwisko"><span>Nazwisko:</span> '.emptyS($last_name).'</p>';
                            echo '<p id="firma"><span>Firma:</span> '.emptyS($company).'</p>';
                            echo '<p><span>Data dołączenia:</span> '.$dateoac.'</p>';
                        ?>
                        </div>
                    </div>
                    <?php
                        if($own_profile) {
                            echo '<div id="profile_edit" style="display: none"><div class="box-100"><hr></div>';
                            echo '<div class="box"><h2>Edytuj profil</h2>';
                            echo '<form id="profile_edit_form" class="form">';
                            echo '<label for="first_name">Imie</label><input type="text" name="first_name" id="first_name" value="'.$first_name.'" placeholder="Imie">';
                            echo '<label for="last_name">Nazwisko</label><input type="text" name="last_name" id="last_name" value="'.$last_name.'" placeholder="Nazwisko">';
                            echo '<label for="company">Nazwa firmy</label><input type="text" name="company" id="company" value="'.$company.'" placeholder="Nazwa firmy">';
                            echo '<button type="submit" class="btn">Zapisz</button>';
                            echo '</form>';
                            echo '<div id="profileEditStatus"></div>';
                            echo '</div>';
                            echo '<div id="changepassword" class="box"><h2>Zmień hasło</h2>';
                            echo '<form id="password_change_form" class="form">';
                            echo '<label for="old_password"><span style="color: red">*</span> Stare hasło</label><input type="password" name="old_password" id="old_password" placeholder="Stare hasło" required>';
                            echo '<label for="new_password"><span style="color: red">*</span> Nowe hasło</label><input type="password" name="new_password" id="new_password" placeholder="Nowe hasło" required>';
                            echo '<span class="formspan">8-16 znaków</span>';
                            echo '<label for="confirm_new_password"><span style="color: red">*</span> Potwierdź nowe hasło</label><input type="password" name="confirm_new_password" id="confirm_new_password" placeholder="Powtórz nowe hasło" required>';
                            echo '<button type="submit" class="btn">Zmień hasło</button>';
                            echo '</form>';
                            echo '<div id="passwordUpdateStatus"></div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </section>
    </div>
    <script src="scripts/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/c9f993284f.js" crossorigin="anonymous"></script>
    <script src="scripts/profile.js"></script>
</body>
<?php
    $con->close(); 
?>
</html>
