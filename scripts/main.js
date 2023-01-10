$("#search-bar i").click(function () {
    $(this).parent().find("input[name='query']").toggle("fast").focus();
});

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
    .substr(1)
        .split("&")
        .forEach(function (item) {
        tmp = item.split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
    return result;
}

// Load login/register forms
$("nav #member-options #login-btn").click(function (e) {
    e.preventDefault();
    $("#login-form-wrap").fadeIn(200);

    if ($("#login-form-wrap").find("#login-form").length > 0) {
        $("#login-form-wrap").find("#login-form").fadeIn(200);
    } else {
        $.ajax({
            url: 'modules/general/login_pup_form.php',
            processData: false,
            contentType: false,
            method : "POST",
            success: function (e) {
                $("body").addClass("no-scroll");
                $("#login-form-wrap").append(e);
            }
        });
    }
});

$("nav #member-options #join-btn").click(function (e) {
    e.preventDefault();
    if ($("#sign-up-wrap").length == 0) {
        $.ajax({
            url: 'modules/general/sign_up_fillup.php',
            processData: false,
            contentType: false,
            method : "POST",
            success: function (e) {
                $("body").addClass("no-scroll");
                $("body").append(e);
            }
        });
    } else {
        $("#sign-up-wrap").slideDown();
    }
});

function close_signup_fillup () {
    $("body").removeClass("no-scroll");
    $("#sign-up-wrap").slideUp();
}

function close_signin_fillup () {
    $("body").removeClass("no-scroll");
    $("#login-form-wrap").slideUp();
}

// Logout
$("#logout-btn").click(function () {
    $.get('logout.php', function () {
        location.reload();
    });
});

// Hint
$(".hint .question-mark").mouseover(function (e) {
    $(this).parent().find(".text").show().css('left', e.clientX + 'px').css('top', e.clientY + 20 + 'px');
});

$(".hint .question-mark").mouseleave(function (e) {
    $(this).parent().find(".text").hide();
});

var typing_machine_input = function (str, element){
  str = str.split('');
  speed = 140;
  var output = '', i = 0;
  
  var start = setInterval(function(){
    output += str[i] == '.' && i + 1 !== str.length ? '. \n' : str[i];
    element.attr('placeholder', output);
    i++;
    if(i === str.length) {
      clearInterval(start);
    }
  }, speed);
}

// Random chat
$("#guest-random-chat").submit(function (e) {
    e.preventDefault();
    new_chat_listener();
});

$("#random-chat-start").click(function (e) {
    e.preventDefault();
    new_chat_listener()
});

function open_guest_random_chat (hash) {
    window.location.href = "guest_chat.php?chash=" + hash
}

function images_fullscreen () {
    $(".toggle-fullscreen-photo").click(function () {
        $(this).parent().toggleClass("fullscreen-img");
    });

    $(document).keydown(function (e) {
        if (e.which == 27) {
            $.each($(".fullscreen-img"), function () {
                $(this).removeClass("fullscreen-img");
            });
        }
    });
}

images_fullscreen();

function new_chat_listener(){
    newChatData = new FormData();
    newChatData.append('search', '');
    newChatData.append('nickname', $("#guest-random-talk-card #choose-nickname").val());
    newChatData.append('icon-name', $("#guest-random-talk-card #chat-icon-name").val());

    $.ajax({
        url: 'ajax/get-guest-random-chat.php',
        processData: false,
        contentType: false,
        method : "POST",
        data : newChatData,
        success: function (data) {
            console.log(data);
            $("#random-chat-start i").css('visibility', 'visible');
            if (data.length > 0) {
                if (JSON.parse(data)[0] == "wait") {
                    check_connected_data = new FormData();
                    check_connected_data.append('check_hash', JSON.parse(data)[1]);
                    check_connected_to_chat_interval = setInterval(function () {
                        $.ajax({
                            url: 'ajax/guest_chat_check_if_second_user_is_connected.php',
                            processData: false,
                            contentType: false,
                            method : "POST",
                            data: check_connected_data,
                            success: function (e) {
                                console.log(e);
                                if (e.length != 0) {
                                    clearInterval(check_connected_to_chat_interval);
                                    open_guest_random_chat(JSON.parse(e)[1]);
                                }
                            }
                        });
                    }, 1000);
                } else if (JSON.parse(data)[0] == "found") {
                    open_guest_random_chat(JSON.parse(data)[1]);
                }
            }
        }
    });
}

// Ranked users list
$.each($("#user-ranking  #user-ranking-tab-triggers .tab-trigger"), function () {
    $(this).click(function () {
        $(this).parent().find(".tab-trigger.selected").removeClass("selected");
        $(this).addClass("selected");
        $(this).parent().parent().find(".tab.open").removeClass("open");
        $(this).parent().parent().find(".tab[data-tabname='" + $(this).data('tabtrigger') + "']").addClass("open");
    });
});