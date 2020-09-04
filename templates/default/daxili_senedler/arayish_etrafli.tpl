<style>
    .internal-documents-details > div{
        margin: 20px 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        font-weight: bold;
        cursor: move; /* fallback if grab cursor is unsupported */
        cursor: grabbing;
        cursor: -moz-grabbing;
        cursor: -webkit-grabbing;
    }
</style>

<div class="internal-documents-details">
    $detailed_information$
    $state$
    <div data-position="$shobe_key$" id="shobe">
        <small>$47shobe$:</small><br>
        <span vezife="">$shobe$</span>
    </div>

    <div data-position="$vezife_key$" id="selahiyyetli_vezife">
        <small>$147vezife$:</small><br>
        <span vezife="">$vezife$</span>
    </div>

    <div data-position="$arayish_tarix_key$" id="arayis_tarixi">
        <small>$47arayish_tarix$:</small><br>
        <span vezife="ishtirakchilar">$order_date$</span>
    </div>

    <div data-position="$arayish_qurum_key$" id="arayish_teqdim_edilen_qurum">
        <small>$47arayish_qurum$:</small><br>
        <span vezife="mushteri">$qurum_ad$</span>
    </div>

    <div data-position="$arayish_iq_tarix_key$" id="arayis_ise_qebul_tarixi">
        <small>$47arayish_iq_tarix$:</small><br>
        <span vezife="mushteri">$work_reception_date$</span>
    </div>

    <div data-position="$qeyd_key$" id="qeyd">
        <small>$47qeyd$:</small><br>
        <span></span>
    </div>

    $tree$
</div>

<script>
    loadEtrafliFront(".internal-documents-details", $dom_position_result$);

    if($senedlerin_etraflisinin_tenzimlenmesi$){
        sortEtrafliFront('.internal-documents-details', "ds_arayish");
    }
</script>