new_message_beep = new Audio('media/newm.mp3');

$("#chat-input-box").keydown(function (e) {
    if (e.keyCode == 13 && !e.shiftKey) {
        e.preventDefault();
        if ($("#text-input").html().replace(/(<([^>]+)>)/ig,"").length >= 1) {
            // Clean the text input
            setTimeout(function () {
                $("#chat-input-box").find("#text-input").html('');
            }, 300);
            
            new_message_data = new FormData();
            new_message_data.append('new_message', '');
            new_message_data.append('chash', findGetParameter("chash"));
            new_message_data.append('token', $(this).find("#guest-new-message-token").val());
            new_message_data.append('message_content', $(this).find("#text-input").html());
            new_message_data.append('image', '');
            
            // Submit new message
            $.ajax({
                url: 'ajax/guest-chat-submit-message.php',
                processData: false,
                contentType: false,
                method : "POST",
                data: new_message_data,
                success: function (data) {
                    console.log(data);
                    // Append new message
                    $("#chat-messages").append("<div class='guest-chat-message self'><div class='message-content'>" + JSON.parse(data)[2] + "</div><div class='sent-date'>" + JSON.parse(data)[1] + "</div></div>");
                    $("#chat-messages").scrollTop($("#chat-messages").prop("scrollHeight")) // Scroll messages container to the bottom
                }
            });
        }
    }
});

$("#chat-input-box").click(function () {
    $(this).find("#text-input").focus();
});

// Get new messages
get_messages_interval = setInterval(function () {
    get_messages_data = new FormData();
    get_messages_data.append('get_new_messages', '');
    get_messages_data.append('chash', findGetParameter("chash"));

    $.ajax({
        url: 'ajax/guest-chat-new-messages.php',
        processData: false,
        contentType: false,
        method : "POST",
        data: get_messages_data,
        success: function (data) {
            console.log(data);
            parsed_json = JSON.parse(data);
            if (parsed_json['code'] == 'success') {
                for (i = 0; i < parsed_json['messages'].length; i++) {
                    parsed_message = JSON.parse(parsed_json['messages'][i]);
                    message_sent_date = parsed_message['sent_date'];
                    message_content = parsed_message['message'];

                    if (parsed_message['photo_path'] == undefined) { // A text message
                        $("#chat-messages").append("<div class='guest-chat-message'><div class='message-content'>" + message_content + "</div><div class='sent-date'>" + message_sent_date + "</div></div>");
                    } else { // Photo message
                        $("#chat-messages").append("<div class='guest-chat-message'><div class='message-content'><img class='chat-photo-image' src='" + parsed_message['photo_path'] + "'</div><div class='sent-date'>" + message_sent_date + "</div></div>");
                    }
                }
            }

            // Play beep if there are new messages
            if (parsed_json['messages'].length > 0) {
                new_message_beep.play();
                $("#chat-messages").scrollTop($("#chat-messages").prop("scrollHeight"));
            }
        }
    });
}, 6000);

$("#chat-input-box #text-input").focus(); // Focus on the text input
$("#chat-messages").scrollTop($("#chat-messages").prop("scrollHeight")); // Scroll messages container to bottom

// Timer
timer_interval = setInterval(function () {
    $("#guest-chat-frame #chat-head #timer #seconds").html(pad(parseInt((parseInt($("#guest-chat-frame #chat-head #timer #seconds").html()) + 1) % 60)))
    if (parseInt($("#guest-chat-frame #chat-head #timer #seconds").html()) == 0) {
        $("#guest-chat-frame #chat-head #timer #minutes").html(pad((parseInt($("#guest-chat-frame #chat-head #timer #minutes").html()) + 1)  % 60))
    }
}, 1000);


function pad (num) {
    if (parseInt(num) < 10) {
        return "0" + num;
    } else {
        return num;
    }
}

// Send photos
$("#chat-additionals-triggers #photos-trigger").click(function () {
    $(this).parent().parent().find("#additional-image").click();
});

$("#additional-image").change(function () {
    if ($(this).length == 1) {
        new_message_data = new FormData();
        new_message_data.append('new_message', '');
        new_message_data.append('chash', findGetParameter("chash"));
        new_message_data.append('token', $("#guest-new-message-token").val());
        new_message_data.append('image', document.getElementById('additional-image').files[0]);

        // Submit new message
        $.ajax({
            url: 'ajax/guest-chat-submit-message.php',
            processData: false,
            contentType: false,
            method : "POST",
            data: new_message_data,
            success: function (data) {
                console.log(data);
                document.getElementById('additional-image').value = "";
                // Append new message
                $("#chat-messages").append("<div class='guest-chat-message self'><div class='message-content'><img class='chat-photo-image' src='" + JSON.parse(data)[2] + "'></div><div class='sent-date'>" + JSON.parse(data)[1] + "</div></div>");
                $("#chat-messages").scrollTop($("#chat-messages").prop("scrollHeight")) // Scroll messages container to the bottom
            }
        });
    }
});

// Image fullscreen
$.each($(".chat-photo-image"), function () {
    $(this).click(function () {
        $(this).parent().toggleClass("fullscreen-img");
        $(this).toggleClass('fullscreen');
        //$(this).css('left', 'calc(50% - ' + $(this).width() + 'px / 2)').css('top', 'calc(50% - ' + $(this).height() + 'px / 2)');
        $(this).animate({
            left: ($(document).width() - $(this).width()) / 2 + 'px',
            top: ($(document).height() - $(this).height()) / 2 + 'px',
        }, 300);

        window.current_image = $(this);
        setTimeout(function () {
            window.current_image.removeClass("bounceIn").removeClass("animated");
        }, 750);
        $(this).addClass("bounceIn").addClass("animated");

        $(this).toggleClass("nofullscreen");
    });
});