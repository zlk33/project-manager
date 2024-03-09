<?php
    session_start();
    if(!isset($_SESSION['login'])){
		header("Location: ../../login.php");
	}
    $today = date('Y-m-d');
    $nextdaydate = date('Y-m-d', strtotime($today . ' + 1 day'));
?>
<div class="box-100">
    <a id="go_back" style="cursor: pointer;">> Powrót do listy projektów</a>
    <h1>Tworzenie projektu</h1>
    <form id="createProject" class="form">
        <label for="project_name"><span style="color: red">*</span> Nazwa projektu: </label>
        <input id="project_name" name="project_name" type="text" placeholder="Nazwa projektu" required>
        <label for="project_description">Opis projektu:</label>
        <textarea id="project_description" name="project_description" placeholder="Opis projektu" maxlength="512"></textarea>
        <span class="charsleft">Max znaków: 512</span>
        <label><span style="color: red">*</span> Data rozpoczęcia:</label>
        <input type="date" id="start_date" value="<?php echo $today; ?>" min="<?php echo $today; ?>" name="start_date">
        <label><span style="color: red">*</span> Data zakończenia:</label>
        <input type="date" id="end_date" value="<?php echo $nextdaydate; ?>" min="<?php echo $nextdaydate; ?>" name="end_date">
        <button type="submit" class="btn_submit">Utwórz projekt</button>
    </form>
    <div id="error_alert" class="alert alert-red">
        <span class="title">Błąd!</span> <span id="error_alert_message"></span>
    </div>
</div>
