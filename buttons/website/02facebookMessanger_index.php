<?php 
$facebook_str = "
                <!-- Your 洽談外掛程式 code -->
                <div    id='fb-root'></div>
                <div    class='fb-customerchat'
                        attribution='biz_inbox'
                        page_id='XXXXXXXXXXXXX'>
                </div>
                ";
$dayOfWeek = date(l);
$currentTime = date('H:i:s', strtotime("now"));//時間(小時、分、秒)
$currentHour = date('H');

/* 舊版，手動在這邊操作區，手動在這邊改code讓FB按鈕要不要顯示
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
/*直接抓現在時間和星期幾去SQL抓資料*/
$compareTimeSql = "SELECT * FROM customer_service_button01 WHERE weekday_name = '$dayOfWeek' AND hour = '$currentHour'";
$resCompareTimeSql = SqlQuery1($compareTimeSql);
if($resCompareTimeSql->fields['button_show'] == 'Y'){
    echo $facebook_str;
}

/* 繞迴圈比較，但是加上小時之後會達成兩次，所以變成會有兩個按鈕
require_once "commonfile/config.inc.php";
$sqlStr = "SELECT * FROM customer_service_button01";
$countSqlStr = "SELECT COUNT(*) FROM (".$sqlStr.") AS tmp";
$resWeekDay = SqlQuery1($sqlStr);
//echo GetQueryValueCount1($countSqlStr, 'value');

for($i=0; $i<GetQueryValueCount1($countSqlStr, 'value'); $i++){
    $weekdayName = $resWeekDay->fields['weekday_name'];
    $bool_button_show = $resWeekDay-> fields['button_show'];
    if($dayOfWeek == $weekdayName){
        if($bool_button_show == 'Y'){
            echo $facebook_str;
        }
    }
    $resWeekDay->MoveNext();
}
*/
?>


    
