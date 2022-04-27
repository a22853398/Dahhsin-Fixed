<?php
include('../commonfile/config_mang.inc.php');
include('00security_function.php');
$tpl_str = '客服按鈕顯示';
$tpl_name = 'customer_service_button01';
$table_name = 'customer_service_button01';
$owner_type = 'update';
$owner_array01 = owner_check01($table_name);

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);//回覆編號
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
$filesel = strip_input('inputadm',$_REQUEST['filesel']);

$lv00_type = strip_input('inputadm',$_REQUEST['lv00_type']);
if($lv00_type == ''){
    $lv00_type = '1';
}

/*數字換成星期字串的函式*/
function numberToWeekdayStr($int){
    switch($int){
        case 0:
            return "Sunday";
            break;
        case 1:
            return "Monday";
            break;
        case 2:
            return "Tuesday";
            break;
        case 3:
            return "Wednesday";
            break;
        case 4:
            return "Thursday";
            break;
        case 5:
            return "Friday";
            break;
        case 6:
            return "Saturday";
            break;    
    }
}

//先開啟，再迴圈跑完全部，再關掉transaction
$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
/*迴圈抓每個checkbox的值*/
for($i=0; $i<7; $i++){
    for($j=0; $j<24; $j++){
        $checkboxName = strtolower(numberToWeekdayStr($i))."_".$j;
        $buttonShow = $_REQUEST[$checkboxName];
        $sqlBool = 'N';
        switch($buttonShow){//要寫到SQL的true/false
            case 'on':
                $sqlBool = 'Y';
                break;
            default:
                $sqlBool = 'N';
                break;
        }
        //echo $checkboxName." ".$sqlBool."<br>";
        $sqlStr = "UPDATE customer_service_button01 SET button_show='".$sqlBool."' WHERE weekday_name='".numberToWeekdayStr($i)."' AND hour='".$j."' ";
        $resSqlStr = SqlQuery1($sqlStr);
    }    
}
$config['sql_link1']->CompleteTrans();//關閉transaction
redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);

/*
$opt = trim($opt);
switch ($opt) {
	case 'edit':
	    break;
}
*/

?>