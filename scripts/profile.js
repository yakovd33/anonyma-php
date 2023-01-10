$.each($("#answers-chars-trigger-scale .trigger"), function () {
    $(this).click(function () {
        console.log($(this).data('tabid'));
        $("#answers-charts").find(".active").removeClass("active");
        $("#answers-charts").find("#" + $(this).data('tabid')).addClass("active");
    });
});