$(document).ready(function() {
    $("#manageProject").on("click", function() {
        $('#project-view').hide();
        $('#project-edit').show();
    });
    $("#showProject").on("click", function(e) {
        e.preventDefault();
        $('#project-edit').hide();
        $('#project-view').show();
        $('#projectUpdate').hide();
    });
    $("#create_project").on("click", function() {
        $(document).prop('title', 'Utwórz projekt');
        $.ajax({
            url: 'ajax/projects/create.php',
            type: 'GET',
            success: function(data) {
                $('#project-list').hide();
                $('#ajax-result').html(data).show();
            }
        });
        $('#project-list').hide();
        $('#ajax-result').show();
      });
      $("#join_project").on("click", function() {
        $.ajax({
            url: 'ajax/projects/join.php',
            type: 'GET',
            success: function(data) {
                $('#project-list').hide();
                $('#ajax-result').html(data).show();
            }
        });
        $(document).prop('title', 'Dołącz do projektu');
        $('#project-list').hide();
        $('#ajax-result').show();
      });
    $(document).on("click", "#go_back", function(e) {
        e.preventDefault();
        $(document).prop('title', 'Projekty');
        $('#ajax-result').hide();
        $('#project-list').show();
    });
    $(document).on("submit", "#join_project_form",function(e){
        $('#error_alert').hide();
        $('#success_alert').hide();
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "ajax/projects/join_project.php",
            data: $(this).serialize()
        }).then(function(response) {
            let data = JSON.parse(response);
            if(data.error) {
                $('#error_alert_message').text(data.error);
                $('#error_alert').show();
                return;
            }
            window.location.href = 'projects.php?id='+data.success;
        }).fail(function() {
            $('#error_alert_message').text("Wystąpił błąd! Spróbuj ponownie!");
            $('#error_alert').show();
        });
    });
    $(document).on("submit", "#createProject",function(e){
        e.preventDefault();
        $("#error_alert").hide();
        $.ajax({
            url: 'ajax/projects/create_project.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response){
                console.log(response);
                let data = JSON.parse(response);
                if(data.success) {
                    window.location.href = 'projects.php?id='+data.success;
                } else {
                    $('#error_alert_message').text(data.error);
                    $("#error_alert").show();
                }
            }
        });
    });
    $(document).on("submit", "#projectUpdateForm",function(e){
        e.preventDefault();
        $("#projectUpdate").hide();
        $.ajax({
            url: 'ajax/projects/update_project.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response){
                let data = JSON.parse(response);
                if(data.success) {
                    $("#projectUpdate").fadeIn(1500);
                    $("#projectUpdate").html('<p style="color: #00DB84">Pomyślnie zaaktualizowano informacje</p>');
                    $("#event_list").load("ajax/projects/event_list.php?id="+data.project_id);
                    $("#panel_name").html('Projekt - '+data.project_name);
                    $(document).prop('title', 'Projekt - '+data.project_name);
                    $("#dp_description").text(data.description.replace(/\\r\\n|\\r/g, ''));
                    $("#dp_end_date").html('<span>Data zakończenia:</span> '+data.end_date);
                } else {
                    $("#projectUpdate").fadeIn(1500);
                    $("#projectUpdate").html('<p style="color: #AD3939">'+data.error+'</p>');
                }
            }
        });
    });
    $(document).on("submit", "#filesUpload",function(e){
        e.preventDefault();
        var file = $('#fileToUpload')[0].files[0];
        var id = $('#projectId').val();
        var formData = new FormData();
        formData.append('plik', file);
        formData.append('projectId', id);
        $.ajax({
            url: 'ajax/projects/upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                let data = JSON.parse(response);
                if(data.success) {
                    $("#fileList").load("ajax/projects/file_list.php?id="+data.success);
                    $("#event_list").load("ajax/projects/event_list.php?id="+data.success);
                    $('#fileToUpload').val('');
                    $('#success_alert_message').text("Pomyślnie przesłano plik!");
                    $('#error_alert').hide();
                    $('#pm-nofiles').hide();
                    $('#success_alert').show();

                } else if(data.error) {
                    $('#error_alert_message').text(data.error);
                    $('#error_alert').show();
                    $('#success_alert').hide();
                    //console.log("blad przesylania "+data.error);
                } else {
                    $('#error_alert_message').text(data.error);
                    $('#error_alert').show();
                    $('#success_alert').hide();
                    //console.log("blad przesylania "+response);
                }
            }
        });
    });
    $('#leaveProject').on('click', function() {
        var project_id = $(this).data('project_id');
        var $button = $(this);
        if ($button.data('clicked')) {
            $.ajax({
                url: 'ajax/projects/leave_project.php',
                type: 'POST',
                data: { project_id: project_id },
                success: function(response){
                    console.log(response);
                    let data = JSON.parse(response);
                    if(data.success) {
                        console.log(data.success);
                        window.location.href = 'projects.php';
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.text('Czy na pewno chcesz opuścić projekt?');
            $button.data('clicked', true);
        }
    });
    $('#deleteProject').on('click', function() {
        var project_id = $(this).data('project_id');
        var $button = $(this);
        if ($button.data('clicked')) {
            $.ajax({
                url: 'ajax/projects/delete_project.php',
                type: 'POST',
                data: { project_id: project_id },
                success: function(response){
                    console.log(response);
                    let data = JSON.parse(response);
                    if(data.success) {
                        window.location.href = 'projects.php';
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.text('Czy na pewno chcesz usunąć projekt?');
            $button.data('clicked', true);
        }
    });
    $(".ban_user").click(function() {
        var userId = $(this).data("user-id");
        var projectId = $(this).data("project-id");
        var $button = $(this);
        var data = {
            action: "ban",
            user_id: userId,
            project_id: projectId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/projects/users_manage.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $(".user_"+userId).remove();
                        $(".user_n_"+userId).remove();
                        $("#event_list").load("ajax/projects/event_list.php?id="+data.project_id);
                        console.log("Banujesz uzytkownika: "+data.user_id+" z projektu: "+data.project_id);
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $(".delete_user").click(function() {
        var userId = $(this).data("user-id");
        var projectId = $(this).data("project-id");
        var $button = $(this);
        var data = {
            action: "delete",
            user_id: userId,
            project_id: projectId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/projects/users_manage.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $(".user_"+userId).remove();
                        $(".user_n_"+userId).remove();
                        $("#event_list").load("ajax/projects/event_list.php?id="+data.project_id);
                        console.log("Usuwasz uzytkownika: "+data.user_id+" z projektu: "+data.project_id);
                    } else {
                        console.log(data.error);
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
        var projectId = $(this).data("project-id");
        var $button = $(this);
        var data = {
            action: "upgrade",
            user_id: userId,
            project_id: projectId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/projects/users_manage.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        var btn1 = '<button data-user-id="'+data.user_id+'" data-project-id="'+data.project_id+'" class="btn btn-red btn-small ban_user"><i class="fa-solid fa-ban"></i></button>';
                        var btn2 = '<button data-user-id="'+data.user_id+'" data-project-id="'+data.project_id+'" class="btn btn-red btn-small delete_user"><i class="fa-solid fa-trash-can"></i></button>';
                        var btn3 = '<button data-user-id="'+data.user_id+'" data-project-id="'+data.project_id+'" class="btn btn-red btn-small downgrade_user"><i class="fa-solid fa-user-minus"></i></button>';
                        $(".user_"+userId).html('<a href="profile.php?id='+data.userId+'">'+data.user_name+'</a> <span class="badge in-progress">Koordynator</span> '+btn1+' '+btn2+' '+btn3);
                        $(".user_n_"+userId).html('<a href="profile.php?id='+data.user_id+'">'+data.user_name+'</a> <span class="badge in-progress">Koordynator</span>');
                        console.log("Zwiekszasz uprawnienia uzytkownika: "+data.user_id+" z projektu: "+data.project_id);
                        $("#event_list").load("ajax/projects/event_list.php?id="+data.project_id);
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $(document).on("click", ".downgrade_user", function() {
        var userId = $(this).data("user-id");
        var projectId = $(this).data("project-id");
        var $button = $(this);
        var data = {
            action: "downgrade",
            user_id: userId,
            project_id: projectId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/projects/users_manage.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        var btn1 = '<button data-user-id="'+data.user_id+'" data-project-id="'+data.project_id+'" class="btn btn-red btn-small ban_user"><i class="fa-solid fa-ban"></i></button>';
                        var btn2 = '<button data-user-id="'+data.user_id+'" data-project-id="'+data.project_id+'" class="btn btn-red btn-small delete_user"><i class="fa-solid fa-trash-can"></i></button>';
                        var btn3 = '<button data-user-id="'+data.user_id+'" data-project-id="'+data.project_id+'" class="btn btn-red btn-small upgrade_user"><i class="fa-solid fa-user-plus"></i></button>';
                        $(".user_"+userId).html('<a href="profile.php?id='+data.user_id+'">'+data.user_name+'</a> '+btn1+' '+btn2+' '+btn3);
                        $(".user_n_"+userId).html('<a href="profile.php?id='+data.user_id+'">'+data.user_name+'</a>');
                        console.log("Zmniejszasz uprawnienia uzytkownika: "+data.user_id+" z projektu: "+data.project_id);
                        $("#event_list").load("ajax/projects/event_list.php?id="+data.project_id);
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $(".file_delete").click(function() {
        var fileId = $(this).data("file-id");
        var projectId = $(this).data("project-id");
        var $button = $(this);
        var data = {
            action: "delete",
            file_id: fileId,
            project_id: projectId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/projects/file_manage.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        $(".file_"+fileId).remove();
                        console.log("Usuwasz plik o id:"+data.file_id+" z projektu: "+data.project_id);
                        $("#event_list").load("ajax/projects/event_list.php?id="+data.project_id);
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $("#createNewTask").on("click", function() {
        window.location.href = 'tasks.php?create=true';
    });
    $(".edit_task").on("click", function() {
        var task_id = $(this).data("task-id");
        window.location.href = 'tasks.php?edit=true&task_id='+task_id;
    });
    $(".delete_task").on("click", function() {
        var task_id = $(this).data("task-id");
        var project_id = $('#projectId').val();
        var $button = $(this);
        var data = {
            task_id: task_id,
            project_id: project_id
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/projects/task_delete.php",
                data: data,
                success: function(response) {
                    console.log(response);
                    let data = JSON.parse(response);
                    if (data.success) {
                        $(".task_"+task_id).remove();
                        $(".task_n_"+task_id).remove();
                        $("#event_list").load("ajax/projects/event_list.php?id="+project_id);
                        console.log("usuwanie zadanie o id:"+task_id);
                    } else {
                        console.log(data.error);
                    }
                }
            });
        } else {
            $button.html('<i class="fa-solid fa-check"></i>');
            $button.data('clicked', true);
        }
    });
    $("#projectChat").on("click", function() {
        $("#chatbox").show();
        $("#projectChat").hide();
        $("#closeChat").show();
    });
    $("#closeChat").on("click", function() {
        $("#chatbox").hide();
        $("#projectChat").show();
        $("#closeChat").hide();
    });
    $(document).on("submit", "#message-form",function(e){
        e.preventDefault();
        var message = $('#message').val();
        var project_id = $('#mp_id').val();
        var data = {
            message: message,
            project_id: project_id
        };
        $.ajax({
            type: "POST",
            url: "ajax/projects/send.php",
            data: data,
            success: function(response) {
                console.log(response);
                let data = JSON.parse(response);
                if (data.success) {
                    var message = $('#message').val("");
                    $("#loadMessages").load("ajax/projects/load_chat.php?project_id="+project_id);
                } else {
                    console.log(data.error);
                }
            }
        });
    });
});