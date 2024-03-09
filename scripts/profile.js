$(document).ready(function() {
    $("#edit").click(function() {
        $("#profile_edit").show();
        $("#edit").hide();
        $("#close_edit").show();
    });
    $("#close_edit").click(function() {
        $("#profile_edit").hide();
        $("#edit").show();
        $("#close_edit").hide();
        $("#profileEditStatus").html("");
    });
    $(document).on("submit", "#profile_edit_form",function(e){
        e.preventDefault();
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var company = $('#company').val();
        var data = {
            action: "info_update",
            first_name: first_name,
            last_name: last_name,
            company: company
        }
        $.ajax({
            type: "POST",
            url: "ajax/profile/update.php",
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    $("#profileEditStatus").html('<p style="color:#00DB84">'+data.success+'</p>');
                    if (first_name.trim() === '') {
                        first_name = "-";
                    }
                    if (last_name.trim() === '') {
                        last_name = "-";
                    }
                    if (company.trim() === '') {
                        company = "-";
                    }
                    $("#imie").html('<span>Imie:</span> '+first_name);
                    $("#nazwisko").html('<span>Nazwisko:</span> '+last_name);
                    $("#firma").html('<span>Firma:</span> '+company);
                } else {
                    $("#profileEditStatus").html('<p style="color:#AD3939">'+data.error+'</p>');
                }
            }
        });
    });
    $(document).on("submit", "#password_change_form",function(e){
        e.preventDefault();
        var old_password = $('#old_password').val();
        var new_password = $('#new_password').val();
        var confirm_new_password = $('#confirm_new_password').val();
        var data = {
            action: "password_update",
            old_password: old_password,
            new_password: new_password,
            confirm_new_password: confirm_new_password
        }
        $.ajax({
            type: "POST",
            url: "ajax/profile/update.php",
            data: data,
            success: function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    $('#old_password').val("");
                    $('#new_password').val("");
                    $('#confirm_new_password').val("");
                    $("#passwordUpdateStatus").html('<p style="color:#00DB84">'+data.success+'</p>');
                } else {
                    $("#passwordUpdateStatus").html('<p style="color:#AD3939">'+data.error+'</p>');
                }
            }
        });
    });
    $(document).on("click", "#delete_user", function() {
        var userId = $(this).data("user-id");
        var $button = $(this);
        var data = {
            user_id: userId
        };
        if ($button.data('clicked')) {
            $.ajax({
                type: "POST",
                url: "ajax/profile/delete.php",
                data: data,
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                        window.location.href = 'logout.php';
                    } else {
                        console.log("blad");
                    }
                }
            });
        } else {
            $button.html('Czy na pewno chcesz usunąć swój profil?');
            $button.data('clicked', true);
        }
    });
});