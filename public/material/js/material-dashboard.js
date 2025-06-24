/*!

 =========================================================
 * Material Dashboard - v2.1.1
 =========================================================

 * Product Page: https://www.creative-tim.com/product/material-dashboard
 * Copyright 2018 Creative Tim (http://www.creative-tim.com)

 * Designed by www.invisionapp.com Coded by www.creative-tim.com

 =========================================================

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 */

(function () {
    isWindows = navigator.platform.indexOf("Win") > -1 ? true : false;

    if (isWindows) {
        // if we are on windows OS we activate the perfectScrollbar function
        $(".sidebar .sidebar-wrapper, .main-panel").perfectScrollbar();

        // $('html').addClass('perfect-scrollbar-on');
    } else {
        $("html").addClass("perfect-scrollbar-off");
    }
})();

var breakCards = true;

var searchVisible = 0;
var transparent = true;

var transparentDemo = true;
var fixedTop = false;

var mobile_menu_visible = 0,
    mobile_menu_initialized = false,
    toggle_initialized = false,
    bootstrap_nav_initialized = false;

var seq = 0,
    delays = 80,
    durations = 500;
var seq2 = 0,
    delays2 = 80,
    durations2 = 500;

$(document).ready(function () {
    $("body").bootstrapMaterialDesign();

    $sidebar = $(".sidebar");

    md.initSidebarsCheck();

    window_width = $(window).width();

    // check if there is an image set for the sidebar's background
    md.checkSidebarImage();

    //    Activate bootstrap-select
    if ($(".selectpicker").length != 0) {
        $(".selectpicker").selectpicker();
    }

    //  Activate the tooltips
    $('[rel="tooltip"]').tooltip();

    $(".form-control")
        .on("focus", function () {
            $(this).parent(".input-group").addClass("input-group-focus");
        })
        .on("blur", function () {
            $(this).parent(".input-group").removeClass("input-group-focus");
        });

    // remove class has-error for checkbox validation
    $(
        'input[type="checkbox"][required="true"], input[type="radio"][required="true"]'
    ).on("click", function () {
        if ($(this).hasClass("error")) {
            $(this).closest("div").removeClass("has-error");
        }
    });
});

$(document).on("click", ".navbar-toggler", function () {
    $toggle = $(this);

    if (mobile_menu_visible == 1) {
        $("html").removeClass("nav-open");

        $(".close-layer").remove();
        setTimeout(function () {
            $toggle.removeClass("toggled");
        }, 400);

        mobile_menu_visible = 0;
    } else {
        setTimeout(function () {
            $toggle.addClass("toggled");
        }, 430);

        var $layer = $('<div class="close-layer"></div>');

        if ($("body").find(".main-panel").length != 0) {
            $layer.appendTo(".main-panel");
        } else if ($("body").hasClass("off-canvas-sidebar")) {
            $layer.appendTo(".wrapper-full-page");
        }

        setTimeout(function () {
            $layer.addClass("visible");
        }, 100);

        $layer.click(function () {
            $("html").removeClass("nav-open");
            mobile_menu_visible = 0;

            $layer.removeClass("visible");

            setTimeout(function () {
                $layer.remove();
                $toggle.removeClass("toggled");
            }, 400);
        });

        $("html").addClass("nav-open");
        mobile_menu_visible = 1;
    }
});

// activate collapse right menu when the windows is resized
$(window).resize(function () {
    md.initSidebarsCheck();

    // reset the seq for charts drawing animations
    seq = seq2 = 0;

    setTimeout(function () {
        md.initDashboardPageCharts();
    }, 500);
});

md = {
    misc: {
        navbar_menu_visible: 0,
        active_collapse: true,
        disabled_collapse_init: 0,
    },

    checkSidebarImage: function () {
        $sidebar = $(".sidebar");
        image_src = $sidebar.data("image");

        if (image_src !== undefined) {
            sidebar_container =
                '<div class="sidebar-background" style="background-image: url(' +
                image_src +
                ') "/>';
            $sidebar.append(sidebar_container);
        }
    },

    showNotification: function (from, align) {
        type = ["", "info", "danger", "success", "warning", "rose", "primary"];

        color = Math.floor(Math.random() * 6 + 1);

        $.notify(
            {
                icon: "add_alert",
                message:
                    "Welcome to <b>Material Dashboard Pro</b> - a beautiful admin panel for every web developer.",
            },
            {
                type: type[color],
                timer: 3000,
                placement: {
                    from: from,
                    align: align,
                },
            }
        );
    },

    initDocumentationCharts: function () {
        if (
            $("#dailySalesChart").length != 0 &&
            $("#websiteViewsChart").length != 0
        ) {
            /* ----------==========     Daily Sales Chart initialization For Documentation    ==========---------- */

            dataDailySalesChart = {
                labels: ["M", "T", "W", "T", "F", "S", "S"],
                series: [[12, 17, 7, 17, 23, 18, 38]],
            };

            optionsDailySalesChart = {
                lineSmooth: Chartist.Interpolation.cardinal({
                    tension: 0,
                }),
                low: 0,
                high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                chartPadding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                },
            };

            var dailySalesChart = new Chartist.Line(
                "#dailySalesChart",
                dataDailySalesChart,
                optionsDailySalesChart
            );

            var animationHeaderChart = new Chartist.Line(
                "#websiteViewsChart",
                dataDailySalesChart,
                optionsDailySalesChart
            );
        }
    },

    initFormExtendedDatetimepickers: function () {
        $(".datetimepicker").datetimepicker({
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove",
            },
        });

        $(".datepicker").datetimepicker({
            format: "MM/DD/YYYY",
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove",
            },
        });

        $(".timepicker").datetimepicker({
            //          format: 'H:mm',    // use this format if you want the 24hours timepicker
            format: "h:mm A", //use this format if you want the 12hours timpiecker with AM/PM toggle
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove",
            },
        });
    },

    initSliders: function () {
        // Sliders for demo purpose
        var slider = document.getElementById("sliderRegular");

        noUiSlider.create(slider, {
            start: 40,
            connect: [true, false],
            range: {
                min: 0,
                max: 100,
            },
        });

        var slider2 = document.getElementById("sliderDouble");

        noUiSlider.create(slider2, {
            start: [20, 60],
            connect: true,
            range: {
                min: 0,
                max: 100,
            },
        });
    },

    initSidebarsCheck: function () {
        if ($(window).width() <= 991) {
            if ($sidebar.length != 0) {
                md.initRightMenu();
            }
        }
    },

    checkFullPageBackgroundImage: function () {
        $page = $(".full-page");
        image_src = $page.data("image");

        if (image_src !== undefined) {
            image_container =
                '<div class="full-page-background" style="background-image: url(' +
                image_src +
                ') "/>';
            $page.append(image_container);
        }
    },

    initDashboardPageCharts: function () {
        if (
            $("#dailySalesChart").length != 0 ||
            $("#completedTasksChart").length != 0 ||
            $("#dailyPurchaseChart").length != 0
        ) {
            /* ----------==========     Daily Sales Chart initialization    ==========---------- */

            if ($("#dailySalesChart").length != 0) {
                getSalesToko().then((res) => {
                    let labels = res.map((data) => {
                        return data.group_date;
                    });
                    let series = res.map((data) => {
                        return data.count;
                    });
                    let dates = res.map((data) => {
                        return new Date(data.last_update);
                    });
                    let max = Math.max.apply(null, series);
                    let max_date = new Date(Math.max.apply(null, dates));

                    var awal;
                    var akhir;
                    var perc;

                    if (series.length >= 2) {
                        awal = parseFloat(series[series.length - 2]);
                        akhir = parseFloat(series[series.length - 1]);
                    } else {
                        awal = parseFloat(series[0]);
                        akhir = parseFloat(series[0]);
                    }

                    if (akhir < awal) {
                        $("#growth-sales")
                            .removeClass("fa-long-arrow-up")
                            .addClass("fa-long-arrow-down");
                        $("#growth-sales")
                            .parent()
                            .removeClass("text-success")
                            .addClass("text-danger");
                        perc = ((awal - akhir) / awal) * 100;
                        $(".desc-sales").text("decrease sales in month");
                    } else {
                        perc = ((akhir - awal) / awal) * 100;
                        $(".desc-sales").text("increase sales in month");
                    }
                    setTimeout(() => {
                        $(".percentage-sales").text(`${perc.toFixed(2)}%`);
                    }, 500);

                    let updt = timeSince(max_date);
                    $(".last-update").text(updt);

                    dataDailySalesChart = {
                        labels: labels,
                        series: [series],
                    };

                    optionsDailySalesChart = {
                        lineSmooth: Chartist.Interpolation.cardinal({
                            tension: 0,
                        }),
                        low: 0,
                        high: max, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                        chartPadding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0,
                        },
                        plugins: [
                            Chartist.plugins.ctPointLabels({
                                textAnchor: "middle",
                                labelInterpolationFnc: function (value) {
                                    return numFormatter(value);
                                },
                            }),
                        ],
                        axisY: {
                            offset: 20,
                            labelInterpolationFnc: function (value) {
                                return numFormatter(value);
                            },
                        },
                    };

                    var dailySalesChart = new Chartist.Line(
                        "#dailySalesChart",
                        dataDailySalesChart,
                        optionsDailySalesChart
                    );

                    md.startAnimationForLineChart(dailySalesChart);
                });
            }

            /* ----------==========     Daily Purchase Chart initialization    ==========---------- */

            if ($("#dailyPurchaseChart").length != 0) {
                getPurchase().then((res) => {
                    let labels = res.map((data) => {
                        return getMonthNamem(data.trans_month);
                    });
                    let series = res.map((data) => {
                        return data.count;
                    });
                    let dates = res.map((data) => {
                        return new Date(data.last_update);
                    });
                    let max = Math.max.apply(null, series);
                    let max_date = new Date(Math.max.apply(null, dates));

                    let updt = timeSince(max_date);
                    $(".last-update-purchase").text(updt);

                    dataDailyPurchaseChart = {
                        labels: labels,
                        series: [series],
                    };

                    optionsDailyPurchaseChart = {
                        lineSmooth: Chartist.Interpolation.cardinal({
                            tension: 0,
                        }),
                        low: 0,
                        high: max, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                        chartPadding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0,
                        },
                        plugins: [
                            Chartist.plugins.ctPointLabels({
                                textAnchor: "right",
                                labelInterpolationFnc: function (value) {
                                    return numFormatter(value);
                                },
                            }),
                        ],
                        axisY: {
                            offset: 20,
                            labelInterpolationFnc: function (value) {
                                return numFormatter(value);
                            },
                        },
                    };

                    var dailyPurchaseChart = new Chartist.Line(
                        "#dailyPurchaseChart",
                        dataDailyPurchaseChart,
                        optionsDailyPurchaseChart
                    );

                    md.startAnimationForLineChart(dailyPurchaseChart);
                });
            }

            /* ----------==========     Completed Tasks Chart initialization    ==========---------- */

            if ($("#completedTasksChart").length != 0) {
                getPertumbuhanAnggota().then((res) => {
                    let labels = res.map((data) => {
                        return data.group_date;
                    });
                    let series = res.map((data) => {
                        return data.count;
                    });
                    let max = Math.max.apply(null, series);

                    dataCompletedTasksChart = {
                        labels: labels,
                        series: [series],
                    };

                    optionsCompletedTasksChart = {
                        lineSmooth: Chartist.Interpolation.cardinal({
                            tension: 0,
                        }),
                        low: 0,
                        high: max, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                        chartPadding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0,
                        },
                        plugins: [
                            Chartist.plugins.ctPointLabels({
                                textAnchor: "middle",
                            }),
                        ],
                    };

                    var completedTasksChart = new Chartist.Line(
                        "#completedTasksChart",
                        dataCompletedTasksChart,
                        optionsCompletedTasksChart
                    );

                    // start animation for the Completed Tasks Chart - Line Chart
                    md.startAnimationForLineChart(completedTasksChart);
                });
            }
        }
    },

    initMinimizeSidebar: function () {
        $("#minimizeSidebar").click(function () {
            var $btn = $(this);

            if (md.misc.sidebar_mini_active == true) {
                $("body").removeClass("sidebar-mini");
                md.misc.sidebar_mini_active = false;
            } else {
                $("body").addClass("sidebar-mini");
                md.misc.sidebar_mini_active = true;
            }

            // we simulate the window Resize so the charts will get updated in realtime.
            var simulateWindowResize = setInterval(function () {
                window.dispatchEvent(new Event("resize"));
            }, 180);

            // we stop the simulation of Window Resize after the animations are completed
            setTimeout(function () {
                clearInterval(simulateWindowResize);
            }, 1000);
        });
    },

    checkScrollForTransparentNavbar: debounce(function () {
        if ($(document).scrollTop() > 260) {
            if (transparent) {
                transparent = false;
                $(".navbar-color-on-scroll").removeClass("navbar-transparent");
            }
        } else {
            if (!transparent) {
                transparent = true;
                $(".navbar-color-on-scroll").addClass("navbar-transparent");
            }
        }
    }, 17),

    initRightMenu: debounce(function () {
        $sidebar_wrapper = $(".sidebar-wrapper");

        if (!mobile_menu_initialized) {
            $navbar = $("nav").find(".navbar-collapse").children(".navbar-nav");

            mobile_menu_content = "";

            nav_content = $navbar.html();

            nav_content =
                '<ul class="nav navbar-nav nav-mobile-menu">' +
                nav_content +
                "</ul>";

            navbar_form = $("nav").find(".navbar-form").get(0).outerHTML;

            $sidebar_nav = $sidebar_wrapper.find(" > .nav");

            // insert the navbar form before the sidebar list
            $nav_content = $(nav_content);
            $navbar_form = $(navbar_form);
            $nav_content.insertBefore($sidebar_nav);
            $navbar_form.insertBefore($nav_content);

            $(".sidebar-wrapper .dropdown .dropdown-menu > li > a").click(
                function (event) {
                    event.stopPropagation();
                }
            );

            // simulate resize so all the charts/maps will be redrawn
            window.dispatchEvent(new Event("resize"));

            mobile_menu_initialized = true;
        } else {
            if ($(window).width() > 991) {
                // reset all the additions that we made for the sidebar wrapper only if the screen is bigger than 991px
                $sidebar_wrapper.find(".navbar-form").remove();
                $sidebar_wrapper.find(".nav-mobile-menu").remove();

                mobile_menu_initialized = false;
            }
        }
    }, 200),

    startAnimationForLineChart: function (chart) {
        chart.on("draw", function (data) {
            if (data.type === "line" || data.type === "area") {
                data.element.animate({
                    d: {
                        begin: 600,
                        dur: 700,
                        from: data.path
                            .clone()
                            .scale(1, 0)
                            .translate(0, data.chartRect.height())
                            .stringify(),
                        to: data.path.clone().stringify(),
                        easing: Chartist.Svg.Easing.easeOutQuint,
                    },
                });
            } else if (data.type === "point") {
                seq++;
                data.element.animate({
                    opacity: {
                        begin: seq * delays,
                        dur: durations,
                        from: 0,
                        to: 1,
                        easing: "ease",
                    },
                });
            }
        });

        seq = 0;
    },
    startAnimationForBarChart: function (chart) {
        chart.on("draw", function (data) {
            if (data.type === "bar") {
                seq2++;
                data.element.animate({
                    opacity: {
                        begin: seq2 * delays2,
                        dur: durations2,
                        from: 0,
                        to: 1,
                        easing: "ease",
                    },
                });
            }
        });

        seq2 = 0;
    },

    initFullCalendar: function () {
        $calendar = $("#fullCalendar");

        today = new Date();
        y = today.getFullYear();
        m = today.getMonth();
        d = today.getDate();

        $calendar.fullCalendar({
            viewRender: function (view, element) {
                // We make sure that we activate the perfect scrollbar when the view isn't on Month
                if (view.name != "month") {
                    $(element).find(".fc-scroller").perfectScrollbar();
                }
            },
            header: {
                left: "title",
                center: "month,agendaWeek,agendaDay",
                right: "prev,next,today",
            },
            defaultDate: today,
            selectable: true,
            selectHelper: true,
            views: {
                month: {
                    // name of view
                    titleFormat: "MMMM YYYY",
                    // other view-specific options here
                },
                week: {
                    titleFormat: " MMMM D YYYY",
                },
                day: {
                    titleFormat: "D MMM, YYYY",
                },
            },

            select: function (start, end) {
                // on select we show the Sweet Alert modal with an input
                swal({
                    title: "Create an Event",
                    html:
                        '<div class="form-group">' +
                        '<input class="form-control" placeholder="Event Title" id="input-field">' +
                        "</div>",
                    showCancelButton: true,
                    confirmButtonClass: "btn btn-success",
                    cancelButtonClass: "btn btn-danger",
                    buttonsStyling: false,
                })
                    .then(function (result) {
                        var eventData;
                        event_title = $("#input-field").val();

                        if (event_title) {
                            eventData = {
                                title: event_title,
                                start: start,
                                end: end,
                            };
                            $calendar.fullCalendar(
                                "renderEvent",
                                eventData,
                                true
                            ); // stick? = true
                        }

                        $calendar.fullCalendar("unselect");
                    })
                    .catch(swal.noop);
            },
            editable: true,
            eventLimit: true, // allow "more" link when too many events

            // color classes: [ event-blue | event-azure | event-green | event-orange | event-red ]
            events: [
                {
                    title: "All Day Event",
                    start: new Date(y, m, 1),
                    className: "event-default",
                },
                {
                    id: 999,
                    title: "Repeating Event",
                    start: new Date(y, m, d - 4, 6, 0),
                    allDay: false,
                    className: "event-rose",
                },
                {
                    id: 999,
                    title: "Repeating Event",
                    start: new Date(y, m, d + 3, 6, 0),
                    allDay: false,
                    className: "event-rose",
                },
                {
                    title: "Meeting",
                    start: new Date(y, m, d - 1, 10, 30),
                    allDay: false,
                    className: "event-green",
                },
                {
                    title: "Lunch",
                    start: new Date(y, m, d + 7, 12, 0),
                    end: new Date(y, m, d + 7, 14, 0),
                    allDay: false,
                    className: "event-red",
                },
                {
                    title: "Md-pro Launch",
                    start: new Date(y, m, d - 2, 12, 0),
                    allDay: true,
                    className: "event-azure",
                },
                {
                    title: "Birthday Party",
                    start: new Date(y, m, d + 1, 19, 0),
                    end: new Date(y, m, d + 1, 22, 30),
                    allDay: false,
                    className: "event-azure",
                },
                {
                    title: "Click for Creative Tim",
                    start: new Date(y, m, 21),
                    end: new Date(y, m, 22),
                    url: "http://www.creative-tim.com/",
                    className: "event-orange",
                },
                {
                    title: "Click for Google",
                    start: new Date(y, m, 21),
                    end: new Date(y, m, 22),
                    url: "http://www.creative-tim.com/",
                    className: "event-orange",
                },
            ],
        });
    },

    initVectorMap: function () {
        var mapData = {
            AU: 760,
            BR: 550,
            CA: 120,
            DE: 1300,
            FR: 540,
            GB: 690,
            GE: 200,
            IN: 200,
            RO: 600,
            RU: 300,
            US: 2920,
        };

        $("#worldMap").vectorMap({
            map: "world_mill_en",
            backgroundColor: "transparent",
            zoomOnScroll: false,
            regionStyle: {
                initial: {
                    fill: "#e4e4e4",
                    "fill-opacity": 0.9,
                    stroke: "none",
                    "stroke-width": 0,
                    "stroke-opacity": 0,
                },
            },

            series: {
                regions: [
                    {
                        values: mapData,
                        scale: ["#AAAAAA", "#444444"],
                        normalizeFunction: "polynomial",
                    },
                ],
            },
        });
    },
};

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.

function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        }, wait);
        if (immediate && !timeout) func.apply(context, args);
    };
}

function getPertumbuhanAnggota() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "dashboard/get-pertumbuhan-anggota",
            type: "GET",
            success: function (data) {
                var jsonResponse = JSON.parse(data);
                if (jsonResponse.status) {
                    var data = jsonResponse.data;
                    resolve(data);
                } else {
                    reject();
                    showNotification(jsonResponse.message, "danger");
                }
            },
            error: function (xhr) {
                // if error occured
                var msg = xhr.responseJSON.message;
                showNotification(msg, "danger");
                reject();
            },
        });
    });
}

function getSalesToko() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "dashboard/get-sales-toko",
            type: "GET",
            success: function (data) {
                var jsonResponse = JSON.parse(data);
                if (jsonResponse.status) {
                    var data = jsonResponse.data;
                    resolve(data);
                } else {
                    reject();
                    showNotification(jsonResponse.message, "danger");
                }
            },
            error: function (xhr) {
                // if error occured
                var msg = xhr.responseJSON.message;
                showNotification(msg, "danger");
                reject();
            },
        });
    });
}

function getPurchase() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "dashboard/get-purchase",
            type: "GET",
            success: function (data) {
                var jsonResponse = JSON.parse(data);
                if (jsonResponse.status) {
                    var data = jsonResponse.data;
                    resolve(data);
                } else {
                    reject();
                    showNotification(jsonResponse.message, "danger");
                }
            },
            error: function (xhr) {
                // if error occured
                var msg = xhr.responseJSON.message;
                showNotification(msg, "danger");
                reject();
            },
        });
    });
}

function timeSince(date) {
    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = seconds / 31536000;

    if (interval > 1) {
        return Math.floor(interval) + " years";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
        return Math.floor(interval) + " months";
    }
    interval = seconds / 86400;
    if (interval > 1) {
        return Math.floor(interval) + " days";
    }
    interval = seconds / 3600;
    if (interval > 1) {
        return Math.floor(interval) + " hours";
    }
    interval = seconds / 60;
    if (interval > 1) {
        return Math.floor(interval) + " minutes";
    }
    return Math.floor(seconds) + " seconds";
}
var aDay = 24 * 60 * 60 * 1000;

function getMonthNamem(month) {
    const monthName = {
        "01": "Januari",
        "02": "Februari",
        "03": "Maret",
        "04": "April",
        "05": "Mei",
        "06": "Juni",
        "07": "Juli",
        "08": "Agustus",
        "09": "September",
        10: "Oktober",
        11: "November",
        12: "Desember",
    };
    return monthName[month];
}

function numFormatter(num) {
    if (num > 999 && num < 1000000) {
        return num / 1000 + "K"; // convert to K for number from > 1000 < 1 million
    } else if (num > 1000000) {
        return num / 1000000 + "M"; // convert to M for number from > 1 million
    } else if (num < 900) {
        return num; // if value < 1000, nothing to do
    }
}
