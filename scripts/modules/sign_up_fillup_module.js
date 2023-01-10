$("#sign-up-wrap #exit-signup-btn").click(function () {
    close_signup_fillup();
});

$("#sign-up-wrap .step .next-step-btn").click(function () {
    sign_up_form_next_step();
});

$("#sign-up-wrap .step .prev-step-btn").click(function () {
    sign_up_form_prev_step();
});

$(document).keydown(function (e) {
    if (e.which == 27) {
        close_signup_fillup();
    }
});

// Step 1
$("#step-one-gender").change(function () {
    $("#step-one-age").animate({width: 'show'}, 150)
});

$("#sign-up-with-email-option").unbind("click").on('click', function () {
    sign_up_form_next_step();
});

function sign_up_form_next_step () {
    if ($(document).width() >= 699) {
        $("#signup-steps").animate({
            scrollLeft: $("#signup-steps").scrollLeft() + $("#signup-steps").width(),
        }, 250);
    } else {
        $("#signup-steps").animate({
            scrollTop: $("#signup-steps").scrollTop() + $("#signup-steps").height(),
        }, 250);
    }
}

function sign_up_form_prev_step () {
    if ($(document).width() >= 699) {
        $("#signup-steps").animate({
            scrollLeft: $("#signup-steps").scrollLeft() - $("#signup-steps").width()
        }, 250);
    } else {
        $("#signup-steps").animate({
            scrollTop: $("#signup-steps").scrollTop() - $("#signup-steps").height()
        }, 250);
    }
}

// Submition
$("#submit-signup").click(function () {
    sign_up_data = new FormData();
    sign_up_data.append('email', $("#step-one-email").val());
    sign_up_data.append('gender', $("#step-one-gender").val());
    sign_up_data.append('age', $("#step-one-age").val());
    sign_up_data.append('password', $("#step-two-password").val());
    sign_up_data.append('re_pass', $("#step-two-re-password").val());
    sign_up_data.append('token', '');

    $.ajax({
        url: 'ajax/submit-signup.php?new-user',
        processData: false,
        contentType: false,
        method: "POST",
        data: sign_up_data,
        success: function (data) {
            sign_up_submition_response = JSON.parse(data);
            if (sign_up_submition_response.code != "success") {
                $("#sign-up-form-feedback").html(JSON.parse(sign_up_submition_response['errors'])[0] + '<br>');
                if (JSON.parse(sign_up_submition_response['errors'])['empty'].indexOf('email') == 0) {
                    $("#step-one-email").css('background-color', 'pink');
                }
            } else {
                location.reload();
            }
        }
    })    
});