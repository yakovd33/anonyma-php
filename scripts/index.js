function set_hot_new_consultation_slider_display_number () {
    if ($(window).width() > 500) {
        $.cookie('hot-new-consultation-display-number', 4);
    } else {
        $.cookie('hot-new-consultation-display-number', 1);
    }
}

set_hot_new_consultation_slider_display_number();

$(window).resize(function () {
    set_hot_new_consultation_slider_display_number();
});

if ($(window).width() > 500) {
    $("#hot-new-consultations .consultations").slick({
        slidesToShow: $.cookie('hot-new-consultation-display-number'),
        slidesToScroll: 3,
        arrows: true,
    });
} else {
    $("#hot-new-consultations .consultations").slick({
        slidesToShow: $.cookie('hot-new-consultation-display-number'),
        arrows: false,
    });
}