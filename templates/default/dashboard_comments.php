<link rel="stylesheet" type="text/css" href="assets/plugins/simditor/styles/simditor.css"/>
<link rel="stylesheet" type="text/css" href="assets/plugins/simditor_emoji/styles/simditor-emoji.css"/>

<?php
$priv = new Privilegiya();
$butunSenedlerUzre = $priv->getByExtraId('butun_senedler_uzre');
$emeliyyatHuququUzre = $priv->getByExtraId('emeliyyat_huququ_uzre');

?>
<style>
    .sweet-alert {
        width: 350px;
        left: 53%;
    }

    .modal-content {
        border-radius: 0px !important;
        width: 120%;
        margin-left: -96px;
        height: 470px;
    }

    .modal-header {
        display: none;
    }

    .modal-head {
        box-shadow: rgb(235, 236, 240) 0px 2px 0px 0px;
    }

    .modal-dialog {
        width: 50%;
        position: relative;
        max-height: 500px;
        width: 50%;
    }

    .modal-body {
        height: 418px;
        overflow: auto;
        padding-bottom: 0;
    }

    .modal-header h4 {
        display: none;
    }

    .close {
        margin-top: 20px;
        width: 20px;
    }

    .md-editor .dropdown-menu li {
        cursor: pointer;
    }

    .simditor .simditor-body {
        min-height: 120px !important;
    }

    .simditor {
        max-width: 95%;
        display: none;
    }

    #comment {
        width: 95%;
        height: 34px;
        margin-bottom: 11px;
    }

    .message-block {
        width: 75%;
    }


    .btn_testiq {
        max-width: 100%;
        text-align: center;
        cursor: default;
        height: 2.28571em;
        line-height: 2.28571em;
        vertical-align: middle;
        width: auto;
        color: rgb(255, 255, 255) !important;
        border-width: 0px;
        text-decoration: none;
        background: rgb(0, 82, 204);
        border-radius: 3px !important;
        padding: 0px 8px;
        transition: background 0.1s ease-out 0s, box-shadow 0.15s cubic-bezier(0.47, 0.03, 0.49, 1.38) 0s;
        outline: none !important;
        margin-top: 7px;
        display: none;
    }

    .btn_imtina {
        display: none;
        text-align: center;
        line-height: 2.21571em;
        color: rgb(66, 82, 110) !important;
        border-width: 0px;
        text-decoration: none;
        background: none;
        padding: 0px 8px;
        margin-left: 100px;
        margin-top: -29px;
    }

    .iykpFP {
        display: block;
        color: rgb(122, 134, 154);
        white-space: nowrap;
        bottom: 7px;
        font-size: 12px;
        line-height: 1.33333;
        position: relative;
    }

    .css-1ordfia {
        background-color: rgb(223, 225, 230);
        box-sizing: border-box;
        color: rgb(66, 82, 110);
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        max-width: 100%;
        text-transform: uppercase;
        vertical-align: baseline;
        border-radius: 3px;
        padding: 2px 3px 3px;
    }

    .sened_nomresi {
        max-width: 350px;
    }

    .sened_nomresi a {
        position: absolute;
        margin-top: 15px;
        font-size: 15px;
        margin-left: 15px;
    }

    a.css-1fkefoj {
        color: rgb(107, 119, 140) !important;
    }

    .epXzqq {
        position: absolute;
        margin-left: 40px;
        margin-top: 22px;
    }

    .iRBGFC {
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 1.0em;
        font-style: inherit;
        font-weight: 600;
        margin-top: 3px;
        margin-right: 12px;
        text-transform: uppercase;
        color: rgb(94, 108, 132);
        line-height: 12px;
        overflow: hidden;
    }

    #bosh_modal1 {
        z-index: 9;
    }

    .status_type {
        margin-top: 6px;
        background: #FF003C;
        width: 70px;
        height: 30px;
        color: white;
        border-radius: 4px !important;
    }

    .status_type_gonderilib {
        background: #FF003C;
        width: 122px;
        height: 30px;
        color: white;
        border-radius: 4px !important;
    }

    .status_type_bagli {
        background: rgb(0, 135, 90) !important;
        width: 74px;
        height: 30px;
        color: white;
        border-radius: 4px !important;
    }

    .status_type_gonderilmeyib {
        background: rgb(0, 135, 90) !important;
        width: 155px;
        height: 30px;
        color: white;
        border-radius: 4px !important;
    }

    .profile_photo a img {
        border-radius: 50% !important;
        margin-top: 17px;
    }

    .my_photo a img {
        border-radius: 50% !important;
        margin-left: 0px;
        margin-top: 0px;
        position: absolute;
    }

    .my_photo {
        width: 56px;
        top: 0px;
        position: relative;
    }

    .today {
        cursor: pointer;
        color: rgb(66, 82, 110);
        position: relative;
    }

    .text_comment {
        width: 65%;
        left: 65px;
        position: relative;
    }

    .text_comment p {
        word-wrap: break-word;
    }

    .jira-users-container {
        z-index: 3;
        display: none;
        height: 300px;
        width: 240px;
        position: sticky;
        background-color: white;
        border: 1px solid #e0dbdb;
        border-radius: 4px !important;
        box-shadow: 1px 1px 10px 4px #8080808c;
        left: 60px;
        overflow-y: auto;
    }

    .jira-users-container .tag-user-container:hover {
        background-color: rgb(235, 236, 240);
    }

    .jira-users-container .user {
        padding-left: 8px;
        padding-top: 8px;
        cursor: pointer;
    }

    .jira-users-container .user img {
        width: 30px;
        border-radius: 15px !important;
    }

    .jira-users-container .tag-user-container {
        bottom: 0px;
        position: relative;
    }

    .jira-users-container .user a {
        margin-right: 20px;
        border-radius: 50% !important;
        font-size: 20px;
        width: 30px;
        height: 30px;
        background: rgba(221, 228, 233, 0.98);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        text-decoration: none;
    }

    .jira-users-container .user span {
        position: relative;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: rgb(9, 30, 66);
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
        font-size: 14px;
        font-weight: 500;
        font-style: normal;
    }

    .editor-style img[data-emoji], .simditor .simditor-body img[data-emoji] {
        width: 1.6em !important;
    }

    .user_photo a img {
        border-radius: 50% !important;
    }

    .user-name-date {
        display: flex;
        position: relative;
        top: -12px;
        left: 65px;
    }

    .user-container .uc-title {
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 0.977143em;
        font-style: inherit;
        font-weight: 600;
        margin-top: 20px;
        text-transform: uppercase;
        color: rgb(94, 108, 132);
        line-height: 12px;
        overflow: hidden;
        margin-bottom: 4px !important;
    }

    .user-container .user-info-container .uc-username {
        margin-top: 4px;
        margin-left: 4px;
        -webkit-appearance: none;
        color: inherit;
        font-size: 14px;
        font-family: inherit;
        letter-spacing: inherit;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .user-info-container .uc-img {
        border-radius: 15px !important;
    }

    .editor-container .user-info-container .uc-img {
        border-radius: 15px !important;
    }

    .user-info-container .uc-user-tag {
        width: 30px;
        height: 30px;
        border-radius: 15px !important;
        background: rgba(221, 228, 233, 0.98);
        justify-content: center;
        text-align: center;
        padding-top: 5px;
        color: #337ab7;
    }

    .presentation .user-info-container {
        position: relative;
        top: 20px;
        margin-left: 26px;
    }

    .presentation {
        max-width: 91.8%;
        margin-left: -22px;
    }

    .editor-container .user-info-container .uc-user-tag {
        width: 36px;
        height: 36px;
        border-radius: 18px !important;
        background: rgba(221, 228, 233, 0.98);
        justify-content: center;
        text-align: center;
        padding-top: 3px;
        color: #337ab7;
        font-size: 19px;
    }

    .editor-container .user-info-container {
        position: absolute;
    }

    .user-container .user-info-container {
        display: flex;
        margin-bottom: 6px;
        margin-top: 8px;
    }

    .user-container .user-info-container:hover {
        background-color: rgb(235, 236, 240);
        cursor: pointer;
    }

    .text_editor {
        bottom: 0px;
        padding-bottom: 10px;
        position: sticky;
        background: white
    }

    .users_count {
        margin-top: -17px;
    }

    .count-container {
        padding-top: 10px;
    }

    .comment-container {
        display: flex;
        -webkit-box-pack: start;
        justify-content: flex-start;
        -webkit-box-align: baseline;
        align-items: baseline;
    }

    .comment-container .uc-title {
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 1.5em;
        font-style: inherit;
        font-weight: 600;
        letter-spacing: -0.003em;
        line-height: 26px;
        color: rgb(23, 43, 77);
        overflow: hidden;
        margin-top: 0px;
    }

    .messages-container {
        max-width: 95%;
        margin-bottom: 20px;
    }

    .editor-container .text-container {
        width: 101%;
        margin-left: 40px;
    }

    .btns-container {
        left: 64px;
        position: relative;
    }

    .btns-container .btn-container {
        position: relative;
        font-size: 14px;
        text-align: center;
        cursor: default;
        color: rgb(107, 119, 140) !important;
        border-width: 0px;
        background: none;
        padding: 0px;
        transition: background 0.1s ease-out 0s, box-shadow 0.15s cubic-bezier(0.47, 0.03, 0.49, 1.38) 0s;
        outline: none !important;
    }

    .deleteBtn {
        left: 10px;
    }

    .btns-container .btn-container:hover {
        cursor: pointer;
        text-decoration: underline;
        color: rgb(137, 147, 164) !important;
    }

    .showSweetAlert {
        border-radius: 4px !important;
        border: 1px solid #c1c1c1;
    }

    .tag-document-number {
        display: inline;
        color: rgb(255, 255, 255);
        cursor: pointer;
        line-height: 2.614;
        margin-left: 4px;
        font-size: 0.8em;
        position: relative;
        top: -16px;
        font-weight: normal;
        word-break: break-word;
        background: rgb(0, 82, 204);
        border-width: 2px;
        border-style: solid;
        border-color: transparent;
        border-image: initial;
        border-radius: 20px !important;
        padding: 2px 1.3em 3px 1.23em;
    }

    .messages-container .tag-document-number {
        white-space: nowrap;
    }

    .tag-user-name {
        display: inline;
        color: rgb(66, 82, 110);
        cursor: pointer;
        line-height: 1.714;
        font-size: 0.8em;
        font-weight: normal;
        word-break: break-word;
        background: rgba(9, 30, 66, 0.08);
        border-width: 1px;
        border-style: solid;
        border-color: transparent;
        border-image: initial;
        border-radius: 20px !important;
        padding: 0px 0.3em 2px 0.23em;
    }

    .text_comment .tag-user-name {
        background: rgb(0, 82, 204);
        color: white;
        font-size: 13px;
    }

    .all-comments-container {
        display: none;
        margin: 16px 0px;
    }

    .css-header-comments-container:hover {
        background: rgba(9, 30, 66, 0.08);
    }

    .css-header-comments-container {
        -webkit-box-align: baseline;
        align-items: baseline;
        box-sizing: border-box;
        display: inline-flex;
        font-size: inherit;
        font-style: normal;
        font-weight: normal;
        max-width: 100%;
        text-align: center;
        white-space: nowrap;
        cursor: default;
        height: 2.28571em;
        line-height: 2.28571em;
        vertical-align: middle;
        width: 100%;
        color: rgb(80, 95, 121) !important;
        border-width: 0px;
        text-decoration: none;
        background: rgba(9, 30, 66, 0.04);
        border-radius: 3px;
        padding: 0px 8px;
        transition: background 0.1s ease-out 0s, box-shadow 0.15s cubic-bezier(0.47, 0.03, 0.49, 1.38) 0s;
        outline: none !important;
    }

   .css-header-comments-container .css-comments-text {
        align-self: center;
        display: inline-flex;
        flex-wrap: nowrap;
        max-width: 100%;
        position: relative;
        width: 100%;
        -webkit-box-pack: center;
        justify-content: center;
    }

    .css-header-comments-container .css-comments-text .other-comments-text {
        -webkit-box-align: center;
        align-items: center;
        align-self: center;
        max-width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        opacity: 1;
        flex: 1 1 auto;
        margin: 0px 4px;
        overflow: hidden;
        transition: opacity 0.3s ease 0s;
    }

    .link-document-container {
        left: 100%;
        z-index: 3;
        display: none;
        position: sticky;
        height: 220px;
        width: 350px;
        background-color: white;
        border: 1px solid #e0dbdb;
        border-radius: 4px !important;
        box-shadow: 1px 1px 10px 4px #8080808c;
        overflow-y: auto;
    }

    .link-document-container .document-container div {
        bottom: 0px;
        position: relative;
        cursor: pointer;
        height: 40px;
    }

    .link-document-container .document-container div span {
        top: 11px !important;
        padding-left: 40px;
        position: relative;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: rgb(9, 30, 66);
        overflow: hidden;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
        font-size: 14px;
        font-weight: 500;
        font-style: normal;
    }

    .link-document-container .document-container div:hover {
        background-color: rgb(235, 236, 240);
        cursor: pointer;
    }

    .link-document-container .search-document {
        z-index: 5;
        top: 0;
        background: white;
        position: sticky;
    }

    .link-document-container .search-document #documentSearch {
        width: 100%;
        background-color: rgb(235, 236, 240);
        border: 5px solid white;
        border-radius: 8px !important;
        padding: 5px;
    }

    .attach-file {
        width: 140px;
        height: 140px;
        position: relative;
        top: 0px;
        left: 0px;
        display: flex;
        box-sizing: border-box;
        -webkit-box-pack: justify;
        justify-content: space-between;
        flex-direction: column;
        border-radius: 3px !important;
        background: #f4f5f7;
        padding: 13px;
    }

    .attach-file .fa-download {
        position: relative;
        margin-left: 41px;
        margin-bottom: 12px;
        font-size: 30px;
    }


</style>
<div class="modal-content">
    <div class="modal-head" style="height: 50px">
        <div class="sened_nomresi">
            <a type="button" class="css-1fkefoj"><span class="css-j8fq0c"><span class="css-eaycls"></span></span><span style="position: relative;left: 8px;" class="refresh-comments-container"><i class="fa fa-refresh"></i></span></a>

        </div>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-7" style="min-height: 337px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="comment-container">
                            <h2 class="uc-title"><?= dsAlt('2616reyler_modali', 'Rəylər'); ?></h2>

                        </div>
                        <div class="all-comments-container">
                            <button type="button" class="css-header-comments-container">
                                    <span class="css-comments-text">
                                        <span class="other-comments-text">
                                            <span></span>
                                        </span>
                                    </span>
                            </button>
                        </div>
                        <div class="messages-container">


                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 roles" style="left: 33px;">
            </div>
        </div>
        <div style="width: 62%;" class="col-md-8 offset-md-4 editor-container text_editor">
            <div class="jira-users-container"></div>
            <div class="link-document-container">
                <div class="search-document">
                    <input type="text" id="documentSearch" placeholder="Axtarış"/>
                </div>
                <div class="documents"></div>

            </div>
            <div class="user-info-container"></div>
            <div class="text-container">
                <textarea id="editor" autofocus></textarea>
                <button type="button" class="btn_testiq"><?= dsAlt('2616yadda_saxla', 'Yadda saxla'); ?></button>
                <button type="button" class="btn_imtina"><?= dsAlt('2616bagla', 'Bagla'); ?></button>
                <input type="text" id="comment" placeholder="   <?= dsAlt('2616rey_bildir', 'Rəy bildir'); ?>..."/>
                <small class="sc-bUIkmT iykpFP"><span><strong></strong> <?= dsAlt('2616sherh_etmek', 'Şərh etmək üçün'); ?>  <span
                                class="css-1ordfia"><span
                                    class="css-q5kkvr">M</span></span> <?= dsAlt('2616duymesini_basin', 'düyməsini basın'); ?>.</span></small>
            </div>

        </div>

    </div>
</div>
</div>

<script type="text/javascript" src="assets/plugins/simditor/site/assets/scripts/module.js"></script>
<script type="text/javascript" src="assets/plugins/simditor/site/assets/scripts/hotkeys.js"></script>
<script type="text/javascript" src="assets/plugins/simditor/site/assets/scripts/uploader.js"></script>
<script type="text/javascript" src="assets/plugins/simditor/site/assets/scripts/simditor.js"></script>
<script type="text/javascript" src="assets/plugins/simditor_emoji/lib/simditor-emoji.js"></script>
<script type="text/javascript" src="assets/plugins/simditor-dropzone.js"></script>
<script>
    var Id = <?php echo json_encode($Id) ?>;
    var senedTipi = <?php echo json_encode($senedTipi) ?>;
    var sId = <?php echo json_encode($sId) ?>;
    var commentId = '';
    var session_user = [];

    let selectedUsers = [];

    function addSelectedUser(user_id) {
        selectedUsers.push(user_id);
    }

    function showOtherUsers(headerClass, classs) {
        if ($('.' + classs).hasClass('hide')) {
            $('.' + classs).removeClass('hide');
            $('.' + classs).addClass('show');
            $('.' + headerClass + ' .uc-title i').attr('class', 'fa fa-chevron-up count-container');
            var count_user = $('.' + headerClass + ' i span').html();
            var new_count_user = parseInt(count_user) + 3;
            $('.' + headerClass + ' .uc-title i span').html(new_count_user);

        } else {
            $('.' + headerClass + ' .uc-title i').attr('class', 'fa fa-chevron-down count-container');
            var new_count_user = $('.' + headerClass + ' i span').html();
            var old_count_user = parseInt(new_count_user) - 3;
            $('.' + headerClass + ' .uc-title i span').html(old_count_user);
            $('.' + classs).removeClass('show');
            $('.' + classs).addClass('hide');
        }

    }

    function parseName(input) {
        var fullName = input.split(' ');
        var result = {};

        var name = fullName.slice(0, 1).join(' ').charAt(0);
        var lastName = fullName.slice(1, 2).join(' ').charAt(0);

        result.name = name;
        result.lastName = lastName;

        return result;
    }

    function showUserPhoto(headerClass, imgsrc, key, id_user) {

        if (headerClass == 'text_editor' || headerClass == 'created-by') {

            if (key == 'photo') {
                $('.' + headerClass + ' .user-info-container').prepend('<img class="uc-img" height="30" width="30" src=' + imgsrc + '>');

            } else {

                $('.' + headerClass + ' .user-info-container').prepend('<div class="uc-user-tag">' + imgsrc.name + imgsrc.lastName + '</div>');
            }
        } else {
            if (key == 'photo') {

                $('<img class="uc-img" height="30" width="30" src=' + imgsrc + '>').insertBefore('.' + headerClass + ' #' + id_user);
            } else {
                $('<div class="uc-user-tag">' + imgsrc.name + imgsrc.lastName + '</div>').insertBefore('.' + headerClass + ' #' + id_user);
            }
        }
    }

    function statusView(classs, status) {
        $('.status').html('<div class="iRBGFC">Status</div><div class = ' + classs + '><div style="font-size: 20px;margin-left: 14px;">' + status + '</div></div>');
    }

    function senedUzreEmeliyyatGorenUserler(classs, operation, user, id, key) {
        if (key = 'header') {
            if (user != '') {
                $('.' + classs).append('<div class="user-info-container"><span id=' + id + ' class="uc-username">' + user + '</span></div>');
                $('.' + classs).show()
            }
        } else if (key == 'other') {
            $('.' + classs).append('<div class="user-info-container"><span id=' + id + ' class="uc-username">' + user + '</span></div>');
        } else {
            $('.users_info').prepend('<div class="user-container ' + classs + ' "> <div class="uc-title">' + operation + ' </div> <div class="user-info-container"><span class="uc-username">' + user + '</span> </div> </div>');

        }
    }

    $.post('prodoc/ajax/dashboard/roles/umumi.php',{'sened_id':Id, 'tip' : senedTipi, 'isComment' : 1}, function (result) {
        var result = JSON.parse(result);
        if(result.status=='success'){
            $('.roles').html(result.html);
        }
    })

    $(document).keypress(function (e) {
        var key = e.which;

        if (key == 109)  // the ''M'' key code
        {
            editorHideShow('simditor', 'btn_testiq', 'btn_imtina', 'comment', 'iykpFP');
            setTimeout(function() {
                $('.simditor-body').focus();

            },100);

        }
    });

    function templateShow(photo, user, classs, operation = '', key) {
        var img = new Image();

        var uphoto = "<?php echo UPLOADS_DIR_WEB_PATH; ?>" + 'profile-images/' + photo;
        var photo_nl = parseName(user);
        var id_user = user.replace(/\s/g, '');

        img.onload = function () {
            showUserPhoto(classs, uphoto, 'photo', id_user);
        };
        img.onerror = function () {

            showUserPhoto(classs, photo_nl, 'nameLastname', id_user);
        };
        img.src = uphoto;

        if (classs != 'text_editor') {
            senedUzreEmeliyyatGorenUserler(classs, operation, user, id_user, key);
        }

    }


    $('.refresh-comments-container').on('click',function () {
        $('.messages-container').html('');
        $('.text_editor .user-info-container').html('');
        pageDownload(Id,senedTipi)
    })
    function commentsShow(e, display) {

        var img = new Image();
        var commentUserPhoto = "<?php echo UPLOADS_DIR_WEB_PATH; ?>" + 'profile-images/' + e.comment_created_by_photo;
        var user = parseName(e.comment_created_by);

        $('.messages-container').prepend('<div style="display: ' + display + '" id=' + e.rey_id + '  class="presentation"><div  class="user-info-container"></div><div class="user-name-date"><div class="iRBGFC">' + e.comment_created_by + '</div><div class="today">' + e.created_at + '</div></div><div class="text_comment">' + e.text + '</div><div id=' + e.created_by_id + ' class="btns-container"></div></div>');
        $('#' + e.rey_id + ' #' + sId + '').html('<button type="button" class="btn-container editBtn">Düzəliş</button><button type="button" class="btn-container deleteBtn">Sil</button>')

        img.onload = function () {
            $('.messages-container #' + e.rey_id + ' .user-info-container').append('<img class="uc-img" height="30" width="30" src=' + commentUserPhoto + '>');
        };
        img.onerror = function () {
            $('.messages-container #' + e.rey_id + ' .user-info-container').append('<div class="uc-user-tag">' + user.name + user.lastName + '</div>');
        };
        img.src = commentUserPhoto;
    }
    pageDownload(Id,senedTipi);

    function pageDownload(Id,senedTipi) {

        $.get('prodoc/ajax/dashboard/comments_modal.php',
            {
                'id': Id,
                'senedTipi': senedTipi
            },
            function (response) {

                response = JSON.parse(response);

                response['comments'].slice(0, 10).forEach(function (e) {
                    commentsShow(e, 'block')
                });
                response['comments'].slice(10).forEach(function (e) {

                    var countComments = response['comments'].slice(10).length;
                    if (countComments > 0) {
                        $('.all-comments-container').show();
                    }
                    $('.other-comments-text span').html('Digər ' + countComments + ' şərhə baxın');
                    commentsShow(e, 'none')

                });

                response['comment_photo'].forEach(function (e) {
                    if (e.photo_rey != null) {
                        var comment_photo = "<?php echo PRODOC_FILES_WEB_PATH; ?>" + 'comment/' + e.photo_rey;

                        if (e.photo_rey.substr(e.photo_rey.length - 3) == 'jpg') {
                            photoListOrdered(e, 'jpg', response)
                        } else if (e.photo_rey.substr(e.photo_rey.length - 3) == 'pdf' || e.photo_rey.substr(e.photo_rey.length - 3) == 'ocx') {
                            photoListOrdered(e, 'file', response)
                        }
                    }
                })


                response['erpuserid'].forEach(function (e) {
                    $('.sened_nomresi .css-eaycls').html(e.sened_novu + ' &nbsp/&nbsp&nbsp ' + e.document_number);
                    session_user = e.session_user_name;
                    templateShow(e.s_photo, e.session_user_name, 'text_editor')
                });
            });
    }

    function photoListOrdered(elementValue,extension,response) {
        var url = '';
        var tag = '';
        var html = '';
        var comment_photo = "<?php echo PRODOC_FILES_WEB_PATH; ?>" + 'comment/' + elementValue.photo_rey;
        if ($('li','#' + elementValue.rey_id + ' .text_comment').length > 0){
            $('#' + elementValue.rey_id + ' .text_comment ol li').each(function (number,value) {

                if (extension == 'file'){
                    url = response['comment_photo'][number].photo_rey;
                    comment_photoes = "<?php echo PRODOC_FILES_WEB_PATH; ?>" + 'comment/' + url;
                    tag = 'a';
                    html = '<a target="_blank" href=' + comment_photoes + '><div class="attach-file">' + url + '<i class="fa fa-download"></i></div></a>';

                }else{
                    url = response['comment_photo'][number].photo_rey;
                    comment_photo = "<?php echo PRODOC_FILES_WEB_PATH; ?>" + 'comment/' + url;
                    tag = 'img';
                    html = '<img alt=' + url + ' src=' + comment_photo + ' width="220" height="200">';
                }
                $(this).find(tag).remove();
                $(this).append(html);
            })
        }else{
            if (extension == 'file'){
                $('#' + elementValue.rey_id + ' .text_comment').append('<a target="_blank" href=' + comment_photo + '><div class="attach-file">' + elementValue.original_comment_photo + '<i class="fa fa-download"></i></div></a>')
            }else{
                $('#' + elementValue.rey_id + ' .text_comment').append('<img alt=' + elementValue.photo_rey + ' src=' + comment_photo + ' width="220" height="200">')
            }
        }
    }

    function editorHideShow(simditor, testiq, imtina, comment, ProTip) {
        $('.' + simditor).css('display', 'block');
        $('.text_editor').css('position', 'relative');
        $('.' + testiq).css('display', 'block');
        $('.' + imtina).css('display', 'block');
        $('#' + comment).css('display', 'none');
        $('.' + ProTip).css('display', 'none');
        $('.simditor-toolbar').css('width', '');
    }

    $('.css-header-comments-container').on('click', function () {
        $(this).hide()
        $('.presentation').show()
    })

    $(document).on('click', '.editBtn', function () {
        editorHideShow('simditor', 'btn_testiq', 'btn_imtina', 'comment', 'iykpFP');
        commentId = $(this).parents('div:eq(1)').attr('id');
        var editCommentValue = $(this).parents('div:eq(1)').find('.text_comment').html();
        $('.simditor-body').html(editCommentValue);
        $('.simditor-body').focus();
    });

    $(document).on('click', '.deleteBtn', function () {
        var commentId = $(this).parents('div:eq(1)').attr('id');
        var editCommentValue = $(this).parents('div:eq(1)').find('.text_comment').html();

        swals({
                title: "Bu şərh silinsin?",
                text: "",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Sil",
                cancelButtonText: "İmtina et"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.get('prodoc/ajax/deleteComment.php',
                        {
                            'removeCommentId': commentId
                        },
                        function (response) {
                            response = JSON.parse(response);
                            if (response.status == 'remove') {
                                $('#' + commentId).remove();
                            }
                        });
                }
            }
        );
    });

    $(document).on('click', '.close', function () {
        $('.sa-button-container .cancel').trigger('click');
    });

    $("#comment").bind("click", function () {
        $('.simditor-body').html('<p></p>');
        editorHideShow('simditor', 'btn_testiq', 'btn_imtina', 'comment', 'iykpFP');
        $('.simditor-body').focus();
    });

    $('.jira-users-container').on('click', '.tag-user-container .user', function () {
        var tagUserId = $(this).attr('id');
        var tagUserName = $(this).find('span').text();
        var valEditorText = $('.simditor-body').text();
        var validatorHtml = $('.simditor-body').html();
        var text = ("'" + valEditorText + "'").split('@');
        var text2 = text[1].replace("'", '');
        var tag = '<span user-id=' + tagUserId + ' contenteditable="false" class="tag-user-name"> ' + tagUserName + '</span>';
        var tag_html = validatorHtml.replace('@' + text2, tag);

        addSelectedUser(tagUserId);

        $('.jira-users-container').hide();
        $('.simditor-body').focus();
        $('.simditor-body').html(tag_html);

    });

    $(".btn_imtina").bind("click", function () {
        $('.toolbar-item-tag').removeClass('disabled')
        $('.link-document-container').hide()
        $('.jira-users-container').hide()
        $('#comment').show()
        $('.simditor-body').html('');
        $('.simditor').css('display', 'none');
        $(this).css('display', 'none');
        $('.btn_testiq').css('display', 'none');
        $('.iykpFP').css('display', 'block');
        $('.my_photo').css('margin-top', '-64px');
        $('.text_editor').css('position', 'sticky');
    });

    $(".btn_testiq").bind("click", function () {
        var editorHtml = $('.simditor-body').html();
        var editorHtml2 = $('.simditor-body').html();
        var my_photo = $('.editor-container .user-info-container').html();
        var sened_tip = $('[sened-id=' + Id + ']').attr('data-tip');
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        var d = new Date();
        var hours = d.getHours();
        var minutes = d.getMinutes();
        var date = d.getDate();
        var month = monthNames[d.getMonth()];
        var year = d.getFullYear();

        if (month < 10) {
            month = '0' + month;
        }
        if (date < 10) {
            date = '0' + date;
        }

        var found = editorHtml.search("<img");


        var today = month + " " + date + "," + year + "," + hours + ":" + minutes;
        let duzelish = '<?php print dsAlt('2616duzelish_reyler', "Düzəliş"); ?>'
        let sil = '<?php print dsAlt('2616sil_reyler', "Sil"); ?>'
        if (<?php print $butunSenedlerUzre ?> == 1 || <?php print $emeliyyatHuququUzre ?> == 1)
        {
            if (editorHtml !== "<p><br></p>" && editorHtml !== "") {

                if ($('.simditor-body img').attr('src') != undefined && $('.simditor-body img').attr('src').length > 70){
                    editorHtml = editorHtml.replace(/<img[^>]*>/g, "");
                }

                if (commentId != '') {
                    $.post('prodoc/ajax/updateComment.php',
                        {
                            'text': editorHtml,
                            'commentId': commentId,
                        },
                        function (response) {
                            response = JSON.parse(response);
                            if (response.status == 'update_hazir') {
                                $('#' + commentId + ' .text_comment').html(editorHtml)
                            }
                        });
                } else {
                    $.post('prodoc/ajax/createComment.php',
                        {
                            'id': Id,
                            'text': editorHtml,
                            'tag_users': selectedUsers,
                            'sened_tip': sened_tip,
                            'key': found
                        },
                        function (response) {
                            response = JSON.parse(response);

                            if (response.status == 'hazir') {
                                $('.messages-container').append('<div id=' + response.reyId + ' class="presentation"><div class="user-info-container">' + my_photo + '</div><div class="user-name-date"><div class="iRBGFC">' + session_user + '</div><div class="today">' + today + '</div></div><div class="text_comment">' + editorHtml2 + '</div><div id=' + sId + ' class="btns-container"><button type="button" class="btn-container editBtn">'+ duzelish +'</button><button type="button" class="btn-container deleteBtn">'+ sil +'</button></div></div>');
                                $('.simditor-body').html('');
                                $('#'.reyId).animate({scrollTop: 0}, 300);
                            }
                        });
                }
                $('.btn_imtina').trigger('click');
            }
        }
    else
        {
            errorModal('Sizin rəy yazmaq hüququnuz yoxdur.', 1500, true);
        }


        selectedUsers = [];
    })

    Simditor.locale = 'en-US'

    var editor = new Simditor({

        textarea: $('#editor'),

        upload: {
            url: 'prodoc/ajax/fileUpload/save_comment_file.php',
            params: {'sened_id': Id}
        },
        toolbar: ['title',
            'bold',
            'italic',
            'color',
            'image',
            'ol',
            'ul',
            'link',
            'tag',
        ],
        //'emoji' hide
        emoji: {
            imagePath: 'assets/plugins/simditor_emoji/images/emoji/'
        },

    });

    function split(val) {
        return val.split(/[@|#]\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }

    var space = false;

    $(".simditor-body").on("keyup change", function(event) {
        space = false ;

        if (event.which === 32){
            space = true ;
        }
        }).autocomplete({
        minLength: 0,
        source: function(request) {
            var term = request.term,
                results = [];
            if (term.indexOf("@") >= 0) {
                if (space == false){
                    $('.jira-users-container').show();
                    $('.link-document-container').hide();
                    term = extractLast(term);

                    if (term.length > 0) {
                        $('.jira-users-container').html('');

                        $.post('prodoc/includes/plugins/axtarish.php', {
                            'ne': 'comment_tag_all_users',
                            'a': term
                        }, function (result) {
                            result = JSON.parse(result);
                            result['results'].forEach(function (key, value) {

                                var img = new Image();

                                var photo = "<?php echo UPLOADS_DIR_WEB_PATH; ?>" + 'profile-images/' + key.photo;

                                var users = '';
                                var photo_nl = parseName(key.text);

                                img.onload = function () {
                                    users = '<div class="tag-user-container"><div id=' + key.id + ' class="user"><img src=' + photo + ' alt=""> <span style="padding-left: 6px;">' + key.text + '</span> </div> </div>';
                                    $('.jira-users-container').append(users);
                                };
                                img.onerror = function () {
                                    users = '<div class="tag-user-container"><div id=' + key.id + ' class="user"><a>' + photo_nl.name + photo_nl.lastName + '</a><span style="top: -24px;padding-left: 40px;">' + key.text + '</span> </div> </div>';
                                    $('.jira-users-container').append(users);
                                };
                                img.src = photo;
                            })
                        });
                    }
                }else{
                    $('.jira-users-container').hide();
                }
            }else{
                $('.simditor-toolbar .toolbar-item-tag').removeClass('disabled')
                $('.jira-users-container').hide();
            }
        }
    });

    function documentsShow(soz) {
        $.post('prodoc/includes/plugins/axtarish.php', {
            'ne': 'all_documents',
            'a': soz
        }, function (result) {
            result = JSON.parse(result);
            result['results'].forEach(function (key, value) {
                if (key.document_number == null) {
                    key.document_number = '-';
                }
                users = '<div class="document-container"><div id=' + key.id + ' class="document-number"><span style="top: -24px;padding-left: 40px;">' + key.text + ' / ' + key.document_number + '</span> </div> </div>';
                $('.link-document-container .documents').append(users);
            })

        });
    }

    $('.simditor-toolbar .toolbar-item-link').on('click', function () {
        if ($('.link-document-container').hasClass('active') == false) {
            $('.jira-users-container').hide();
            $('.link-document-container').show()
            $('#documentSearch').focus();
            $('.link-document-container').addClass('active')
            documentsShow('')
        } else {
            $('#documentSearch').removeAttr('value');
            $('.link-document-container').removeClass('active')
            $('.link-document-container').hide()
        }
    })

    $('.simditor-toolbar .toolbar-item-tag').on('click', function () {
        if ($('.simditor-toolbar .toolbar-item-tag').hasClass('disabled') == false){
            $('.simditor-toolbar .toolbar-item-tag').addClass('disabled');
            $('.simditor-body').append(' @');
            $('.link-document-container').hide();
            $('.jira-users-container').show();
        }
    })

    $('#documentSearch').on('change paste keyup', function () {
        $('.link-document-container .documents').html('');
        var soz = $(this).val();
        documentsShow(soz)
    })

    $('.link-document-container').on('click', '.document-number', function () {
        var documentNumber = $(this).text();
        var documentNumberId = $(this).attr('id');
        var documentNumberTemplate = '<span id=' + documentNumberId + ' contenteditable="false" class="tag-document-number">' + documentNumber + '</span>'
        $('.simditor-body').append(documentNumberTemplate);
        $('.link-document-container ').removeClass('active');
        $('.link-document-container ').hide();
    })

    $(document).find('.toolbar-item-tag').append('<svg style="color:#858585;top: 5px;position: relative;" width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation"><path d="M12.062 13.93c-.904 0-1.451-.734-1.451-1.945 0-1.226.538-1.952 1.466-1.952.928 0 1.422.764 1.422 1.967 0 1.195-.502 1.93-1.438 1.93M12 5c-3.925 0-7 3.075-7 7 0 4.596 3.522 7 7 7 .874 0 1.614-.09 2.26-.279a.751.751 0 0 0-.42-1.44c-.508.147-1.11.22-1.84.22-2.648 0-5.5-1.722-5.5-5.5 0-3.085 2.417-5.5 5.5-5.5 3.24 0 5.5 1.952 5.5 4.75 0 2.045-1.043 3-1.748 3-.008 0-.752-.11-.752-.75v-4a.75.75 0 1 0-1.5 0v.187c-.346-.585-1.016-.952-1.795-.952C10.102 8.736 9 10.04 9 11.938c0 1.984 1.103 3.312 2.753 3.312.865 0 1.51-.387 1.865-1.076.334 1.016 1.37 1.576 2.132 1.576 1.598 0 3.25-1.683 3.25-4.5C19 7.628 16.058 5 12 5" fill="currentColor" fill-rule="evenodd"></path></svg>')


</script>
