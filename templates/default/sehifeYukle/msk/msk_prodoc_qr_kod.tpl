<style type="text/css">
    .qr-kod-edit-text{
        text-align:left;
        width: 250px;
        white-space: pre-line;
    }
    .info-text{
        font-size:12px;
    }
</style>

<div class="tab-pane" id="tab_28">
    <div class="row">
        <div class="col-md-5">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-advance table-hover" id="qr-kod">
                    <thead>
                    <tr>
                        <th style="width: 30%;"><i class="fa fa-qrcode"></i> QR-Kod
                            <i style="cursor:pointer;" class="fa fa-info-circle info" id="info"></i>
                            <div id="template" style="display:none;">
                                <p class="info-text">QR-kod düzəldilməsi üçün lazım olacaq açar sözləri :</p>
                                <strong class="info-text">1. $qeydiyyat_tarixi$</strong>
                                <br>
                                <strong class="info-text">2. $senedin_nomresi$</strong>
                            </div>
                        </th>
                        <th style="width: 60%;text-align:left;"></th>
                    </tr>
                    </thead>
                    <tbody>
                        $contactTypes$
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="assets/scripts/tippy.all.min.js"></script>
<script type="text/javascript">

    $("#qr-kod-btn").click(function(){

        if($("#qr-kod-btn").hasClass("qr-kod-save-btn")){
            var qrkod =$("#qr-kod-save-input").val();

            $.post("prodoc/ajax/changeQrkod.php",{qrkod:qrkod})
                .done(function(result){
                    var data = JSON.parse(result);
                    $(".qr-kod-edit-text").text(data['qrkod']);
                })
                .fail(function(error){
                    console.log(error);
                });

            $("#qr-kod-btn").css("backgroundColor","#1CAF9A")
            $(".btn-text").text(" Düzəliş et");
            $(this).find('i').removeClass('fa-save').addClass('fa-edit');
            $("#qr-kod-save-input").remove();
            $(".qr-kod-edit-text").css("display","");
            $(this).removeClass("qr-kod-save-btn");

        }
        else{

            var qrkod = $(".qr-kod-edit-text").text();

            $(this).addClass("qr-kod-save-btn");
            $("#qr-kod-btn").css("backgroundColor","#D64635");
            $(".btn-text").text(" Yadda Saxla");
            $(this).find('i').removeClass('fa-edit').addClass('fa-save');
            $(".qr-kod-edit-text").css("display","none");
            $("#qr-kod-text").append(`<textarea id="qr-kod-save-input" class="form-control row=5 cols=80" style="margin: 0px 2px 0px 0px; width: 350px; height: 131px;">`);
            $("#qr-kod-save-input").val(qrkod);
        }
    });

    $("#info").hover(function(){
        tippy('.info', {
            placement:'right',
            animation: 'shift-toward',
            arrow: true,
            html: '#template'
        });
    });

</script>