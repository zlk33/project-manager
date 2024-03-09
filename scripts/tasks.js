$(document).ready(function() {
    $("#edit_task").on("click", function() {
        var task_id = $(this).data("task-id");
        window.location.href = 'tasks.php?edit=true&task_id='+task_id;
    });
    $("#createTaskBTN").on("click", function() {
        window.location.href = 'tasks.php?create=true';
    });
    $(document).on("submit", "#createTask",function(e){
        e.preventDefault();
        $("#error_alert").hide();
        $.ajax({
            url: 'ajax/tasks/create_task.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let data = JSON.parse(response);
                if(data.success) {
                    window.location.href = 'tasks.php?id='+data.success;
                } else {
                    $('#error_alert_message').text(data.error);
                    $("#error_alert").show();
                }
            }
        });
    });
    $(document).on("click", "#changeStatusInprogress",function(e){
        e.preventDefault();
        var task_id = $(this).data("task-id");
        var data = {
            task_id: task_id,
            status: "0"
        };
        $.ajax({
            url: 'ajax/tasks/change_status.php',
            type: 'POST',
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if(data.success) {
                    $("#statusButtons").load("ajax/tasks/load_buttons.php?task_id="+task_id);
                }
            }
        });
    });
    $(document).on("click", "#changeStatusFinished",function(e){
        e.preventDefault();    
        var task_id = $(this).data("task-id");
        var data = {
            task_id: task_id,
            status: "1"
        };
        $.ajax({
            url: 'ajax/tasks/change_status.php',
            type: 'POST',
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if(data.success) {
                    $("#statusButtons").load("ajax/tasks/load_buttons.php?task_id="+task_id);
                }
            }
        });
    });
    $(document).on("click", "#changeStatusNotFinished",function(e){
        e.preventDefault();  
        var task_id = $(this).data("task-id");
        var data = {
            task_id: task_id,
            status: "2"
        };
        $.ajax({
            url: 'ajax/tasks/change_status.php',
            type: 'POST',
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if(data.success) {
                    $("#statusButtons").load("ajax/tasks/load_buttons.php?task_id="+task_id);
                }
            }
        });
    });
    $(document).on("click", "#addUserBTN",function(e){
        e.preventDefault();  
        var user_id = $('#addUser').val();
        var task_id = $(this).data("task-id");
        var data = {
            user_id: user_id,
            task_id: task_id
        };
        $.ajax({
            url: 'ajax/tasks/add_user.php',
            type: 'POST',
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if(data.success) {
                    $("#addUsersL").load("ajax/tasks/load_users.php?task_id="+task_id+"&project_id="+data.project_id);
                    $("#taskUsersList").load("ajax/tasks/users_list.php?task_id="+task_id);
                }
            }
        });
    });
    $(document).on("submit", "#taskUpdateForm",function(e){
        e.preventDefault();
        var task_name = $('#task_name_i').val();
        var task_id = $('#task_id_i').val();
        var description = $('#task_description_i').val();
        var start_date = $('#start_date_i').val();
        var end_date = $('#end_date_i').val();
        var data = {
            task_id: task_id,
            task_name: task_name,
            description: description,
            start_date: start_date,
            end_date: end_date
        };
        $.ajax({
            url: 'ajax/tasks/task_update.php',
            type: 'POST',
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if(data.success) {
                    $('#updateStatus').html('<p style="color: #00DB84">Pomyślnie zaaktualizowano dane o projekcie</p>');
                    $('.panel-header').html('<p>Edycja zadania - '+task_name+'</p>');
                } else {
                    $('#updateStatus').html('<p style="color: #AD3939">'+data.error+'</p>');
                }
            }
        });
    });
    $(document).on("click", ".downgrade_user", function() {
        var userId = $(this).data("user-id");
        var taskId = $(this).data("task-id");
        var $button = $(this);
        var data = {
            action: "downgrade",
            user_id: userId,
            task_id: taskId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/tasks/user_update.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $("#taskUsersList").load("ajax/tasks/users_list.php?task_id="+taskId);
                    } else {
                        console.log("blad");
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $(document).on("click", ".delete_user", function() {
        var userId = $(this).data("user-id");
        var taskId = $(this).data("task-id");
        var project_id = $(this).data("project-id");
        var $button = $(this);
        var data = {
            action: "delete",
            user_id: userId,
            task_id: taskId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/tasks/user_update.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $("#taskUsersList").load("ajax/tasks/users_list.php?task_id="+taskId);
                        $("#addUsersL").load("ajax/tasks/load_users.php?task_id="+taskId+"&project_id="+project_id);
                    } else {
                        console.log("blad");
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $(document).on("click", ".upgrade_user", function() {
        var userId = $(this).data("user-id");
        var taskId = $(this).data("task-id");
        var $button = $(this);
        var data = {
            action: "upgrade",
            user_id: userId,
            task_id: taskId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/tasks/user_update.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $("#taskUsersList").load("ajax/tasks/users_list.php?task_id="+taskId);
                    } else {
                        console.log("blad");
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
});