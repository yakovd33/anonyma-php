$(document).keydown(function(e) {
    if (e.which == 27) {
        close_signin_fillup();
    }
});

// Login
$("#login-form").submit(function (e) {
    e.preventDefault();
    login_data = $("#login-form").serialize();

    $.post('ajax/login_check.php', login_data, function (data) {
        parsed_response = JSON.parse(data);
        if (parsed_response[0] == "success") {
            location.reload();
        }
    });
});