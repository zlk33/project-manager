<?php
    session_start();
    if(!isset($_SESSION['login'])){
		header("Location: ../../login.php");
	}
?>
<div class="box-100">
    <a id="go_back" style="cursor: pointer;">> Powrót do listy projektów</a>
</div>
<div class="box">
    <h1>Dołącz do projektu</h1>
    <p>Aby dołączyć do projektu musisz posiadać 6-cio znakowy kod projektu.</p>
    <form id="join_project_form" class="form">
        <input type="text" name="project_code" maxlength="6" minlength="6" id="project_code" placeholder="Podaj kod projektu" required>
        <button type="submit" class="btn_submit">Dołącz</button>
    </form>
    <div id="error_alert" class="alert alert-red">
        <span class="title">Błąd!</span> <span id="error_alert_message"></span>
    </div>
</div>
