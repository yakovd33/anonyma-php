$("#cons-new-comment-add-image").click(function () {
    $("#cons-new-comment-add-image-input").click();
});

// Submit answer
$("#cons-add-ans").submit(function (e) {
    e.preventDefault();

    ans_form = document.getElementById("cons-add-ans");
    ans_data = new FormData(ans_form);
    ans_data.append('ans-content', $("#cons-new-comment-input").html());
    if ($("#cons-new-comment-add-image-input").val().length > 0) {
        ans_data.append('ans-add-img', $("#cons-new-comment-add-image-input")[0].files[0]);
    }
    ans_data.append('is_anonymous', $("#cons-new-ans-toggle-anonymously").find("input[type='checkbox']").prop('checked'));

    $.ajax({
        url: 'ajax/submit-new-consultation-comment.php',
        processData: false,
        contentType: false,
        method: "POST",
        data: ans_data,
        success: function (data) {
            console.log(data);
            parsed_json = JSON.parse(data);
            if (parsed_json.code == "success") {
                // Append new answer
                html = `
                    <div class="cons-answer self" style="animation-name: fadeIn; animation-duration: 2s;">
                        <div class="content-wrap">
                        <div class="answer-content">{{content}}</div>
                            <div style="display: {{display_add_img}}">
                                <div class="toggle-ans-img-show"><i class="fa fa-plus" aria-hidden="true"></i> הצג תמונה מצורפת</div>
                                <div class="ans-add-img-wrap"><img src="{{image_path}}" class="cons-ans-additional-image"></div>
                            </div>
                        </div>
                        <div class="cons-ans-poster-info">
                            <div class="pp" style="{{show_pp}}"><div class="cons-ans-poster-pp" style="background-image: url('{{user_profile_pic}}')"></div></div>                            
                            <div class="name-title">{{name}}</div>
                            <div class="time-answered">עכשיו</div>
                        </div>
                    </div>`

                if (parsed_json.includes_image) {
                    html = html.replace("{{display_add_img}}", "block");
                    html = html.replace("{{image_path}}", parsed_json.image_path);
                } else {
                    html = html.replace("{{display_add_img}}", "none");
                }

                if (!parsed_json.is_anonymous) {
                    html = html.replace("{{name}}", '<a href="profile.php?uh="' + parsed_json.profile_hash + '>' + parsed_json.nickname + '</a>')
                } else {
                    html = html.replace("{{show_pp}}", "display: none")
                    html = html.replace("{{name}}", parsed_json.nickname);
                }

                html = html.replace("{{user_profile_pic}}", parsed_json.user_pp);
                html = html.replace("{{content}}", parsed_json.ans_content);

                $("#cons-answer-list").append(html);

                ans_options();
            }
        }
    });

    $("#cons-new-comment-input").html('').focus();
});

// Anonymous
$("#cons-new-ans-toggle-anonymously .icon").click(function () {
    $(this).toggleClass("triggered");

    if ($("input[name='is_anonymous']")[0].checked) {
        $("input[name='is_anonymous']")[0].checked = false;
    } else {
        $("input[name='is_anonymous']")[0].checked = true;
    }
    
    $("input[name='anonymous-nickname']").animate({width: 'toggle'}, 150)
});

$("#cons-toggle-add-ans").click(function () {
    $('html,body').animate({scrollTop: $("#cons-new-comment-input").offset().top}, 400, function() {
        $("#cons-new-comment-input").focus();
    });
});

function ans_options () {
    $.each($(".toggle-ans-img-show"), function () {
        $(this).click(function () {
            $(this).parent().find(".ans-add-img-wrap").toggleClass("toggled");
        });
    });
}

ans_options();

// Votes
$.each($("#consultation-votes.logged .vote-item"), function () {
    $(this).click(function () {        
        if ($(this).hasClass("clicked")) {
            $(this).removeClass("clicked");
            $(this).parent().find(".vote-item.clicked").removeClass("clicked");
        } else {
            $(this).parent().find(".vote-item.clicked").removeClass("clicked"); 
            $(this).addClass("clicked");
        }

        vote_data = new FormData();
        vote_data.append('consultation_hash', $("#cons-hash-val").val());
        vote_data.append('vote', $(this).data('vote'));

        $.ajax({
            url: 'ajax/vote-consultation.php',
            processData: false,
            contentType: false,
            method: "POST",
            data: vote_data,
            success: function (data) {
                console.log(data);
                parsed_json = JSON.parse(data);
                $("#upvote-consultation .num").text(parsed_json.new_ups);
                $("#downvote-consultation .num").text(parsed_json.new_downs);
            }
        });
    });
});

// Sort and order
$("#cons-filter-ans").change(function () {
    sort_opt = $(this).find("option:checked").val();
    if (findGetParameter("sort") == null) {
        location.href += "&sort=" + sort_opt;
    } else {
        window.location.search = jQuery.query.set("sort", sort_opt);
    }
});

$("#cons-sort-asc-desc-toggle").click(function () {
    order_opt = $(this).data('order') == "asc" ? "desc" : "asc";
    if (findGetParameter("order") == null) {
        location.href += "&order=" + order_opt;
    } else {
        window.location.search = jQuery.query.set("order", order_opt);
    }
    $(this).data('order') = order_opt;
});

// Answer options
$(".cons-answer .cons-ans-opts .toggle-opts").click(function () {
    $(this).parent().find(".options-list").toggle();
    event.stopPropagation();
    close_options();
}); // Toggle options

// Close options when clicked outside
function close_options () {
    $(window).click(function() {
        $.each($(".options-list"), function () {
            $(this).hide()
        });
    });

    $.each($(".options-list"), function () {
        $(this).click(function(event){
            event.stopPropagation();
        });
    });
}

// Consultation toggle additional image
$("#toggle-cons-img-show").click(function () {
    $(this).parent().find("#cons-picture").slideToggle(300);
});