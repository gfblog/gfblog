$(function () {

    $('.radio label').each(function () {
        var e = $(this);
        e.click(function () {
            e.closest('.radio').find("label").removeClass("active");
            e.addClass("active");
        });
    });
    $('.checkbox label').each(function () {
        var e = $(this);
        e.click(function () {
            if (e.find('input').is(':checked')) {
                e.addClass("active");
            } else {
                e.removeClass("active");
            }
        });
    });

    $('.icon-navicon').each(function () {
        var e = $(this);
        var target = e.attr("data-target");
        e.click(function () {
            $(target).slideToggle().toggleClass("nav-navicon");
        });
    });

    // 加载更多
    $(document).on("click", ".btn-loadmore", function () {
        var that = this;
        var page = parseInt($(this).data("page"));
        page++;
        $(that).prop("disabled", true);
        $.ajax({
            url: $(that).attr("href"),
            success: function (data, ret) {
                $(data).insertBefore($(that).parent());
                $(that).remove();
                return false;
            },
            error: function (data) {

            }
        });
        return false;
    });

    //自动加载更多
    $(window).scroll(function () {
        var loadmore = $(".btn-loadmore");
        if (loadmore.size() > 0 && !loadmore.prop("disabled")) {
            if ($(window).scrollTop() - loadmore.height() > loadmore.offset().top - $(window).height()) {
                loadmore.trigger("click");
            }
        }
    });
    setTimeout(function () {
        if ($(window).scrollTop() > 0) {
            $(window).trigger("scroll");
        }
    }, 500);

    // 回到顶部
    $(document).on('click', ".gotop", function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });
});