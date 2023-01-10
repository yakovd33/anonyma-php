// Move steps
function next_step () {
    var current_step_num = $("#new-consultation-steps .step.open").data('stepnum');
    $("#new-consultation-steps .step.open").fadeOut("slow").removeClass("open");
    $("#new-consultation-steps .step[data-stepnum='" + (current_step_num + 1) + "'").slideDown(200).delay(200).addClass("open");
}

$.each($("#publicity-triggers .publicity-trigger"), function () {
    $(this).click(function () {
        next_step();
        setTimeout(function () {
            typing_machine_input($('#new-consultation-title').data('text'), $('#new-consultation-title'));
        }, 700);
        $('#new-consultation-title').focus()});
});

// Letters count
var valid_length = parseInt($("#new-consultation-letters-left-count").text());
$("#new-consultation-desc").keyup(function (e) {
    e.preventDefault();
    if (valid_length - $(this).html().length > 0) {
        $("#new-consultation-letters-left-count").html(valid_length - $(this).html().length);
    } else {
        e.preventDefault();
        $("#new-consultation-letters-left-count").html(0);
    }
});

$("#new-consultation-trigger-add-image").click(function () {
    $("#new-consultation-additional-photo").click();
});

// Submit
$("#new-consultation-steps").submit(function (e) {
    e.preventDefault();

    var myForm = document.getElementById('new-consultation-steps');
    datt = new FormData(myForm);
    datt.append('description', $("#new-consultation-desc").text());
    datt.append('is_anonymous', $("input[name='is_anonymous']")[0].checked);
    datt.append('is_infinite_answers', $("input[name='is_infinite_answers']")[0].checked);

    if ($("#new-consultation-additional-photo").val() != "") {
        datt.append('add_image', $("#new-consultation-additional-photo")[0].files[0]);
    }

    $.ajax({
        url: 'ajax/submit_new_consultation.php',
        processData: false,
        contentType: false,
        method: "POST",
        data: datt,
        success: function (response) {
            console.log(response);
            parsed_json = JSON.parse(response);
            new_hash = response.new_hash;
            if (new_hash != "") {
                location.href = "consultation.php?cp=" + parsed_json.new_hash
            }
        }
    })
});

// Num ans
$("input[name='is_infinite_answers']").change(function () {
    if ($(this)[0].checked) {
        $("#select-num-answers").hide();
    } else {
        $("#select-num-answers").css('display', 'block');
    }
});

// Anonymous
$("#new-consultation-trigger-anonymously .icon").click(function () {
    $(this).toggleClass("triggered");

    if ($("input[name='is_anonymous']")[0].checked) {
        $("input[name='is_anonymous']")[0].checked = false;
    } else {
        $("input[name='is_anonymous']")[0].checked = true;
    }
    
    $("input[name='anonymous-nickname']").animate({width: 'toggle'}, 150)
});

// Tags
$("#new-post-tags-input").click(function () {
    $(this).find("#current-tag-input").focus();
});

$("#current-tag-input").keyup(function (e) {
    if ($(this).val()[$(this).val().length - 1] == ',') {
        $(this).val($(this).val().substring(0, $(this).val().length - 1)); // Cut the comma
        new_tag_text = $(this).val();
        $(this).val('');
        $("#added-tags").append('<label class="tag-label">' + new_tag_text + '<input type="checkbox" class="added-tag" name="tags[]" value="' + new_tag_text + '" checked><span class="tag-delete"><i class="fa fa-times-circle" aria-hidden="true"></i></span></label>');
        delete_tags();
    }

    if ($(this).val().length == 0 && e.which == 8) {
        $(this).val($("#added-tags").find(".tag-label").last().find(".added-tag").val());
        $("#added-tags").find(".tag-label").last().remove();
    }
});

// Delete tags
function delete_tags () {
    $.each($("#added-tags .tag-delete"), function () {
        $(this).click(function () {
            $(this).parent().remove();
        });
    });
}

delete_tags();

$("#new-consultation-trigger-add-tag").click(function () {
    $("#new-post-tags-input").toggle().find("#current-tag-input").focus();
});