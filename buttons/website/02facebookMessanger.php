<?php 
$facebook_str = "
<!-- Messenger 洽談外掛程式 Code -->
<div id='fb-root'></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            xfbml   : true,
            version : 'v10.0'
        });
    };

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/zh_TW/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<!-- Your 洽談外掛程式 code -->
<div class='fb-customerchat' attribution='biz_inbox' page_id='528380117293741'></div>
";



$date = date("Y/m/d");
$dayOfWeek = date(l);
//echo $dayOfWeek;
$currentTime = date('H:i:s', strtotime("now"));//時間(小時、分、秒)
$currentHour = date('H');//現在幾點，小時

/* 舊版，手動操作區，手動在這邊改code讓FB按鈕要不要顯示
switch($dayOfWeek){
    case Friday:
        if($currentTime > '18:00:00'){//星期五晚上六點之後就不顯示
            //不做事
        }else{
            echo $facebook_str;
        }
        break;
    case Saturday:
    case Sunday:
        break;
    default:
        echo $facebook_str;
        break;
}
*/

require_once "commonfile/config.inc.php";
/*直接抓現在時間和星期幾去SQL抓資料*/
$compareTimeSql = "SELECT * FROM customer_service_button01 WHERE weekday_name = '$dayOfWeek' AND hour = '$currentHour'";
$resCompareTimeSql = SqlQuery1($compareTimeSql);
if($resCompareTimeSql->fields['button_show'] == 'Y'){
    echo $facebook_str;
}


/* 繞迴圈比較，但是加上小時之後會達成兩次，所以變成會有兩個按鈕
$sqlStr = "SELECT * FROM customer_service_button01";
$countSqlStr = "SELECT COUNT(*) FROM (".$sqlStr.") AS tmp";
$resWeekDay = SqlQuery1($sqlStr);
//echo GetQueryValueCount1($countSqlStr, 'value');

for($i=0; $i<GetQueryValueCount1($countSqlStr, 'value'); $i++){
    $weekdayName = $resWeekDay->fields['weekday_name'];
    $bool_button_show = $resWeekDay->fields['button_show'];
    $hour = $resWeekDay->fields['hour'];
    $hourTime = $hour.":00:00";
    $hourTimeRange = intval($hour+1).":00:00";
    
    if($dayOfWeek === $weekdayName){ 
        if($currentTime >= $hourTime && $currentTime < $hourTimeRange){
            if($bool_button_show == 'Y'){
                echo $currentTime." ";
                echo $hour." ";
                echo $hourTime." ";
                echo $hourTimeRange." ";
                echo $facebook_str." ";
            }    
        }
    }
    $resWeekDay->MoveNext();
}
*/
include('02lineButton.php');//把LINE和快速導覽也套用到每個一頁面
?>
