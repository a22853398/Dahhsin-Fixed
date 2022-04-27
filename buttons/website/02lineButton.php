<style>
/* LINE展開的按鈕 */
    #btn_line{
        display: block;
        position: fixed;
        right: 23px;
        bottom: 90px;   
        /* padding: 10px 15px; */    
        /* font-size: 20px; */
        /* background: #777; */
        color: white;
        cursor: pointer;
        opacity: 0%;
        z-index: 5;
        animation-duration: 0.5s;
        animation-name: line_zoomin;
        animation-timing-function: ease-out;
        animation-delay: 2.2s;
    }
/* LINE按鈕載入動畫 */    
    @keyframes line_zoomin {
      from {
          bottom: 0px;
          opacity: 0%;
          transform:scale(0,0);
      } 
      to {
          bottom: ;
          opacity: 100%;
          transform:scale(1,1);
      }
    }
/* LINE QR code 展開區塊 */    
    .btn_modal{
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 10; /* Sit on top */
        /* padding-top: 0; */ /* Location of the box */
        right: 90px; /* 20 */
        bottom: 90px; /* 160*/
        width: 225px; /* Full width */
        height: 225px; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        margin-right: auto;
        margin-top: auto; 
        float: right;
        
        transform-origin: bottom right;
        /*-webkit-animation-name: zoom;*/
        /*-webkit-animation-duration: 0.3s;*/
        animation-name: zoom;
        animation-duration: 0.3s;
    }
    /* QR code */
    .btn_modal_content{
        display: block;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        margin:auto;
        opacity: 100%;
    }
    /* LINE QRcode的動畫 */
    @keyframes zoom {
      from {transform:scale(0,0)} 
      to {transform:scale(1,1)}
    }
    @keyframes zoomout {
      from {transform:scale(1,1)} 
      to {transform:scale(0,0)}
    }
    /* The Close Button 關掉的按鈕*/
    .close {
        position: absolute;
        top: 5px;
        right: 5px;
        color: #f1f1f1;
        font-size: 20px;
        font-weight: bold;
        transition: 0.3s;
    }
    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
    #btn_mobile{
        display: block;
        position: fixed;
        right: 23px;
        bottom: 165px;   
        height: 64px;
        width: 59px;
        text-align: center;
        vertical-align: text-top;
        padding-bottom: 5px;
        background: url("images/faq_icon_blue.png"); 
        color: white;
        cursor: pointer;
        opacity: 0%;
        z-index: 4;
        font-size: 20px;
        font-weight: bold;
        border: none;
        
        animation-duration: 0.5s;
        animation-name: line_zoomin;
        animation-timing-function: ease-out;
        animation-delay: 0.8s;
    }
    #sagi{
        display: block;
        position: fixed;
        right: 23px;
        bottom: 235px;   
        height: 64px;
        width: 59px;
        text-align: center;
        vertical-align: text-top;
        padding-bottom: 5px;
        background: url("images/faq_icon_yellow.png"); 
        color: red;
        cursor: pointer;
        opacity: 0%;
        z-index: 4;
        font-size: 20px;
        font-weight: bold;
        border: none;
        
        animation-duration: 0.5s;
        animation-name: line_zoomin;
        animation-timing-function: ease-out;
        animation-delay: 0.8s;
    }
</style>
<button id="sagi">提醒</button>
<button id="btn_mobile">手機</button>
<img src="images/btn_line_round.png" id="btn_line" title="點我加入Line" alt="點我加入Line">
<div class="btn_modal" id="line_modal">
    <span class="close" id="btn_close">&times;</span>
    <img src="https://qr-official.line.me/sid/M/347vbugn.png" class="btn_modal_content">
</div>

<script type="text/javascript">
    /* modal 出現消失共用的動畫函式*/
    //消失時間為 div的時間
    function fadeOutAnimation(target, animationName, seconds){
        $(target).css('animation-name',animationName);
        setTimeout(function(){
            $(target).css('display','none');
        }, seconds);
    }
    function fadeInAnimation(target, animationName){
        $(target).css('animation-name', animationName);
        $(target).css('display','block');
    }
    //點LINE按鈕，跳出QRcode，若有常見問題則消失
    $('#btn_line').on('click', function(){
        
        if($('#line_modal').css('display') == 'none'){
            fadeInAnimation("#line_modal", "zoom");
        }else{
            fadeOutAnimation("#line_modal", "zoomout", 275);
            console.log('kokop');
        }
    });
    //LINE按鈕內的X
    $('#btn_close').on('click', function(){
        if($('#line_modal').css('display') == 'block'){
            fadeOutAnimation("#line_modal", "zoomout", 275);
        }
    });
    //延遲LINE和手機按鈕載入的時間
    var line_btn = document.getElementById('btn_line');
    var mobile_btn = document.getElementById("btn_mobile");
    var sagi = document.getElementById("sagi");
    function delayLoadLine(){
        setTimeout(function(){
            mobile_btn.style.opacity='100%';
        }, 1000);
        setTimeout(function(){
            line_btn.style.opacity='100%';
        }, 2500);
        setTimeout(function(){
            sagi.style.opacity='100%';
        }, 1000);
    }
    delayLoadLine();
    /* 舊的，沒在使用，現在改用PHP判斷時間決定按鈕顯示位置
    //因為星期變動而改變LINE按鈕位置
    function weekdayChangeFAQPosition(weekday, hour){
        switch(weekday){
            case 5:
                if( hour < 18 ){
                    line_btn.style.bottom = "";
                    mobile_btn.style.bottom = "";
                    sagi.style.bottom = "";
                }else{
                    line_btn.style.bottom = "15px";
                    mobile_btn.style.bottom = "90px";
                    sagi.style.bottom = "165px";
                }
                break;
            case 6:
            case 0:
                line_btn.style.bottom = "15px";
                mobile_btn.style.bottom = "90px";
                sagi.style.bottom = "165px";
                break;
            default:
                line_btn.style.bottom = "";
                mobile_btn.style.bottom = "";
                sagi.style.bottom = "";
                break;
        }
    }
    var today = new Date();
    //weekdayChangeFAQPosition(today.getDay(), today.getHours());
    */
    //因為按鈕顯示的Y或N決定按鈕顯示的位置
    function changeButtonPosition(buttonShow){
        if(buttonShow == 'Y'){
            line_btn.style.bottom = "";
            mobile_btn.style.bottom = "";
            sagi.style.bottom = "";
        }else if(buttonShow == 'N'){
            line_btn.style.bottom = "15px";
            mobile_btn.style.bottom = "90px";
            sagi.style.bottom = "165px";
        }
    }
    
    /* 點螢幕非彈出視窗後要做的事 */    
    function hideDiv(){
        $(document).on('click', function(e){
            //點在FAQ按鈕 + LINE按鈕 + 跟跳出來的視窗之外的地方的話，關掉跳出來的視窗
            if($(e.target).closest("#btn_line").length === 0 
                && $(e.target).closest("#line_modal").length === 0){
                    if($("#line_modal").css('display') == "block"){
                        fadeOutAnimation("#line_modal", "zoomout", 275);
                    }
            }
        });
    }
    hideDiv();
    
    /* 前往手機版 */
    document.getElementById("btn_mobile").addEventListener("click", function(){
        if(window.confirm("您將前往手機版網頁！\n若您使用電腦，可能會不方便瀏覽")){
            window.location.href='m/index.php';
        }
    });
    document.getElementById("sagi").addEventListener("click", function(){
        window.location.href='newsDetail.php?lv01_id=M211107000158eb';
    });
</script>
<?php 
$dayOfWeek = date(l);//星期幾
$currentTime = date('H:i:s', strtotime("now"));//時間(小時、分、秒)
$currentHour = date('H');


require_once 'commonfile/config.inc.php';
/*直接抓現在時間和星期幾去SQL抓資料*/
$compareTimeSql = "SELECT * FROM customer_service_button01 WHERE weekday_name = '$dayOfWeek' AND hour = '$currentHour'";
$resCompareTimeSql = SqlQuery1($compareTimeSql);
echo "<script>changeButtonPosition('".$resCompareTimeSql->fields['button_show']."');</script>";

/* 迴圈比較星期幾和時間，但是會重複執行兩次，所以會有兩個按鈕
$sqlStr = "SELECT * FROM customer_service_button01";
$countSqlStr = "SELECT COUNT(*) FROM (".$sqlStr.") AS tmp";
$resWeekDay = SqlQuery1($sqlStr);
//echo GetQueryValueCount1($countSqlStr, 'value');

for($i=0; $i<GetQueryValueCount1($countSqlStr, 'value'); $i++){
    $weekdayName = $resWeekDay->fields['weekday_name'];
    $bool_button_show = $resWeekDay-> fields['button_show'];
    if($dayOfWeek == $weekdayName){
        if($bool_button_show == 'Y'){
            echo "<script>changeButtonPosition(".$bool_button_show.");</script>";
        }
    }
    $resWeekDay->MoveNext();
}
*/
?>