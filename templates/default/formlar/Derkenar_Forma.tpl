<title>$document_number$</title>
<link href="../../asset/global/plugins/bootstrap/css/bootstrap.min.css?v2" rel="stylesheet" type="text/css"/>
<link href="../../asset/global/plugins/uniform/css/uniform.default.css?v1" rel="stylesheet" type="text/css"/>
<link href="../../asset/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css?v1" rel="stylesheet" type="text/css"/>
<script src="../../asset/global/plugins/jquery.min.js?v=3" type="text/javascript"></script>
<script src="../../asset/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js?v=2" type="text/javascript"></script>
<style>
    .row{
        width: 800px; margin: auto;
        /*background-color: grey;*/
    }
    .xett {
        border-bottom: 3px solid black;
        margin-bottom: 10px;
        width: 600px;
    }
    .alt-xett {
        border-bottom: 1px solid black;
        margin-top: 25px;
        line-height: 30px !important;
    }
    .fontSize{
        font-size: 25px;
    }

    @media print{

        .print{
            display: none;
        };
        body{
            -webkit-print-color-adjust: exact;
        }
        .colorText{
            color:red !important;
        };
        strong{
            color: red;
        }
    }
    .colorText{ color: red };

</style>
<div class="container">
    <div class="row" >
      <div class="col-md-12" >
          <div style="width: 40%">
              <img style="width: 230px; height: 230px; margin-top: 10px;" src="../../assets/img/logo/Gerb_Azerbaijan.png">
          </div>
          <div class="fontSize" style=" width: 57%; position: absolute; top: 0; margin-top: 45px;  margin-left: 30%; text-align: center;">
              <strong>Azərbaycan Respublikası Ərazilərinin Minalardan Təmizlənməsi üzrə Milli Agentlik (ANAMA)</strong>
              <hr>
              <strong>Agentliyin direktoru</strong>
          </div>
      </div>
    </div>
    <div class="xett" style="margin-top: 14px; width: 100%; margin-left: 30px;"></div>
    <div class="row">
        <!--<div class="form-group fontSize" style="text-align: center">
            <label >
                <span class="alt-xett">$document_number$</span>
                <strong class="colorText" style="color:red">$gonderen_teshkilat_ad$ № <span class="alt-xett colorText">$gonderen_teshkilatin_nomresi$</span> </strong>
                M - <span class="alt-xett">$derkenar_id$</span>
            </label>
        </div>
         <div class="form-group fontSize" style="">
            <label style="width: 30%;     margin-left: 110px;" >
                <span class="alt-xett">$senedin_daxil_olma_tarixi$</span>
            </label>
            <label style="width: 25%;">
                <span class="alt-xett colorText" style="color:red" >$created_at$</span>
            </label>
            <label >
                <span class="alt-xett">$derkenar_tarixi$</span>
            </label>
        </div>
<div class="form-group fontSize">
            <label >
                <span class="alt-xett">$document_number$</span>
                <strong class="colorText" style="color:red;     margin-left: 80px;">$gonderen_teshkilat_ad$ № <span class="alt-xett colorText">$gonderen_teshkilatin_nomresi$</span> </strong>
                <span class="alt-xett" style="    margin-left: 80px;"> M -$derkenar_id$</span>
            </label>
        </div>
        <div class="form-group fontSize" style="">
            <label style="" >
                <span class="alt-xett">$senedin_daxil_olma_tarixi$</span>


                <span class="alt-xett colorText" style="color:red;  margin-left: 125px;" >$created_at$</span>

                <span class="alt-xett" style=" margin-left: 165px;">$derkenar_tarixi$</span>
            </label>
        </div>
        -->
        <div class="form-group fontSize col-md-12" >
            <div class="col-xs-4"> <span class="alt-xett" style="font-weight: bold">$document_number$</span></div>
            <div class="col-xs-5"><strong class="colorText" style="color:red">$gonderen_teshkilat_ad$ № <span class="alt-xett colorText">$gonderen_teshkilatin_nomresi$</span> </strong></div>
            <div class="col-xs-3" style="font-weight: bold">  M - <span class="alt-xett">$derkenar_id$</span></div>



        </div>
        <div class="form-group fontSize col-xs-12" style="font-weight: bold">
            <div class="col-xs-5">  <span class="alt-xett">$senedin_daxil_olma_tarixi$</span></div>
            <div class="col-xs-3"> <span class="alt-xett colorText" style="color:red;" >$created_at$</span></div>
            <div class="col-xs-4"> <span class="alt-xett" style="margin-left: 40px;">$derkenar_tarixi$</span></div>
        </div>

        <div style="height: 6%"></div>
        <div class="form-group fontSize" style="text-align: center">
            $mesul_shexsler$
            <strong><span class="alt-xett"> məlumat üçün:</span></strong>
            $derkenar_shexsler$
            <h4 class="print" style="font-weight: bold; color: white">Nəzarətə görə</h4>
            <div style="height: 3%"></div>


        </div>
        <div class="colorText" style=" position: absolute; top: 370px; margin-left: 610px; border-radius: 58px; width: 80px; border: 6px solid red; height: 80px;" id="nezaretdedir">
            <strong style="color: black !important; position: absolute; margin-left: 22px; margin-top: 13px !important; font-size: 30px">N</strong>
        </div>

    </div>
    <div class="row" style="margin-top: 33px"></div>
    <div class="row">
        <div class="form-group fontSize" style="text-align: center ; word-break: break-all;width: 95%;">
            <label >
                $derkenar_metn_ad$
            </label>
        </div>
    </div>
    <div class="row">
        <div class="form-group fontSize" style="text-align: right">
            <label style="margin-right: 100px;" >
               $rey_muellifi_ad$
            </label>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <p class="print"><span style="font-size: 19px; font-family: times new roman,times;"><a
                            style="color: #064076;font-size: 17px;font-weight: bold;cursor: pointer;"
                            onclick="window.print();">Çap et</a></span>
                <!--<span style="font-size: 19px; font-family: times new roman,times;margin-left: 30px;"><a target="_blank"href='daxil_olan_sened2word.php?id=$sid$&export=word' style="color: #064076;font-size: 17px;font-weight: bold;cursor: pointer;">Word</a></span> -->
            </p>
        </div>
    </div>
</div>


<script src="../../asset/global/plugins/bootstrap/js/bootstrap.min.js?v=1" type="text/javascript"></script>
<script src="../../asset/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js?v=1" type="text/javascript"></script>
<script src="../../asset/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js?v=1" type="text/javascript"></script>
<script>
if ('$mektub_nezaretdedir$'==0){
    $('#nezaretdedir').hide();
}else {
    $('#nezaretdedir').show();
}

</script>

