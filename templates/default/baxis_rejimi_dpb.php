<div class="modal-body form">
    <form class="form-horizontal form-bordered ">
        <div class="form-body" style="padding-left: 5px; padding-right: 5px">

            <div style="padding: 0px;position: relative;width: 100%;overflow: hidden;height: 450px; background: #DDD;"
                 id="senedShekilDiv">
                <img src="data:image/gif;base64," style="width: 100%;" class="cursor1" id="shekilImgSened">
            </div>


            <div style="width: 100%; text-align: center;padding-top: 13px;border-top: 1px solid #45B6AF !important;">
                <div id="slider" style="width: 200px;margin: 0 auto; background: #3B9C96;"></div>
            </div>

            <div style="width: 100%;text-align: center;padding-top: 13px;">
                <button rotate="left" type="button" class="btn blue btn-sm">
                    <i class="fa fa-rotate-left"></i></button>
                <button rotate="right" type="button" class="btn blue btn-sm">
                    <i class="fa fa-rotate-right"></i></button>
            </div>

            <div style="width: 100%; margin-top: 10px; overflow-y: hidden; overflow-x: auto; text-align: center; white-space: nowrap;background: #F8F8F8;"
                 id="shekillerLenti">
                <?php
                if (isset($skanShekillerMass)) {
                    foreach ($skanShekillerMass AS $skanS) {
                        print "<img sid=" . (int)$skanS[0] . " src='uploads/daxil_olan_senedler/scanner/" . htmlspecialchars($skanS[1]) . "' src2='uploads/daxil_olan_senedler/scanner/" . htmlspecialchars($skanS[1]) . "' style=\"height: 75px;margin: 2px;border: 1px solid #B2B2B2;\">";
                    }
                }
                ?>
            </div>
            <div style="margin: 20px 10px;border-top: 1px dashed;padding-top: 13px;">
                <span class="badge badge-default badge-roundless"> Ctrl </span> +
                <span class="badge badge-default badge-roundless"> Shift </span> düymələrini eyni anda sıxıb saxlamaqla
                sənədi tam ekranda izləyə bilərsiz
            </div>
        </div>
    </form>
</div>
<link rel="stylesheet" href="assets/plugins/jquery-ui/jquery-ui.css">
<script>
    $(document).ready(function () {
        var current_position = 0;

        $("button[rotate]").click(function () {
            var direction = $(this).attr('rotate');
            if (direction == 'left') {
                if (current_position == 0 || current_position != -180) {
                    current_position -= 90;
                    DoRotate(current_position);
                }

            }
            else if (direction == 'right') {
                if (current_position == 0 || current_position != 180) {
                    current_position += 90;
                    DoRotate(current_position);
                }
            }
        });

        function DoRotate(d) {
            $("#shekilImgSened").css({
                transform: 'rotate(' + d + 'deg)'
            });
        }


        $("#slider").slider({
            slide: function (event, ui) {
                $("#shekilImgSened").css("width", (parseInt(ui.value) + 100) + "%");
            },
            stop: function () {
                dragStop2();
            }
        });
        $("#shekilImgSened").draggable({
            drag: function (e, ui) {
                $(this).switchClass("cursor1", "cursor2");
            },
            stop: function () {
                dragStop2();
            }
        });
        $("#senedShekilDiv").bind('mousewheel DOMMouseScroll', function (event) {
            var v = parseInt($("#slider").slider("value")) + ((event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) ? 5 : -5);
            $("#slider").slider("value", v);
            $("#shekilImgSened").css("width", (v + 100) + "%");
            dragStop2();
            return false;
        });
    });

    function dragStop2() {
        var t = $("#shekilImgSened"),
            animateM = {},
            x = t.position().left,
            y = t.position().top,
            w = t.outerWidth(),
            h = t.outerHeight(),
            w2 = t.parent('div').outerWidth(),
            h2 = t.parent('div').outerHeight();
        t.switchClass("cursor2", "cursor1");
        x > 0 && (animateM['left'] = 0);
        y > 0 && (animateM['top'] = 0);
        y + h - h2 < 0 && (animateM['top'] = h2 - h);
        x + w - w2 < 0 && (animateM['left'] = w2 - w);
        if (animateM != {}) {
            t.animate(animateM, 200);
        }
    }

    function shekilBoyut() {
        $(".senedQeydiyyat2Div>.col-md-12").switchClass("col-md-12", "col-md-8", 300);
        $(".senedQeydiyyat2Div>.col-md-4").removeClass("baqli");
        $("#shekillerLenti>img:eq(0)").click();
        $("#qeydiyyat").animate({scrollTop: 0}, 300);
        $("#qeydiyyat>.modal-dialog").animate({width: "98%"}, 300);
    }

    $("#shekillerLenti").on("click", ">img", function () {
        $("#shekillerLenti>.sechilib").removeClass("sechilib");
        $(this).addClass("sechilib");
        var shekil = $(this).attr("src2"),
            shekilSid = $(this).attr("sid");
        console.log($("#shekilImgSened"));
        $("#shekilImgSened").attr("src", shekil).attr("sid", shekilSid);
        dragStop2();
    });
    var shekilBoyutdu = false;
    $(document).keydown(function (e) {
        if (e.ctrlKey && e.shiftKey && $("#shekillerLenti>.sechilib").length > 0 && !shekilBoyutdu && $("#qeydiyyat").hasClass("in")) {
            var shekil = $("#shekillerLenti>.sechilib"),
                surl = shekil.attr("src2"),
                sw = shekil.outerWidth(),
                sh = shekil.outerHeight(),
                ww = $(window).outerWidth(),
                wh = $(window).outerHeight();
            $("body").append('<div style="text-align: center; z-index: 99999999; position: fixed; top: 0; left: 0; background: #000; width: 100%; height: 100%;" tamekranshekil><img src="' + surl + '"' + (sw * wh / sh > ww ? ' style="width: 100%;"' : ' style="height: 100%;"') + '></div>');
            shekilBoyutdu = true;
        }
    });
    $(document).keyup(function () {
        shekilBoyutdu && $("body>div[tamekranshekil]").remove() & (shekilBoyutdu = false);
    });
</script>