<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('../commonfile/pageft_mang.inc.php');
include('00security_function.php');
$tpl_str = '客服按鈕顯示';
$tpl_name = 'customer_service_button01';
$table_name = 'customer_service_button01';
$owner_type = 'mang';
$owner_array01 = owner_check01($table_name);

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
if($sel00_type == ''){
  $sel00_type = '1';
}
$search_key1 = strip_input('inputadm',$_REQUEST['search_key1']);

  $tpl = new TemplatePower( './'.$tpl_name.'_mang.htm' );
  $tpl->assignInclude( 'external', './00external.php' );
  $tpl->assignInclude( 'mainMenu', './00mainMenu.php' );
  $tpl->prepare();

  $tpl->assignglobal('tpl_str', $tpl_str );
  $tpl->assignglobal('tpl_name', $tpl_name );

if($owner_array01[1] == '1'){
	$tpl->newBlock('tablist01_add');
	$tpl->gotoBlock('_ROOT');
}
if($owner_array01[2] == '1'){
	$tpl->newBlock('tablist01_batch');
	$tpl->gotoBlock('_ROOT');
}

$tpl->gotoBlock('_ROOT');
//輸出分類
  //沒有分類，全站共用一個客服按鈕時間
  
//輸出分類

$tpl->gotoBlock('_ROOT');
//Tab UI選單顏色
  $tpl->assign('sel00_type'.$sel00_type, 'id="tab_active"' );
  if($sel00_type == 'S1'){
    $tpl->newBlock('block_row01_row');
    $tpl->gotoBlock('_ROOT');
  }
  $tpl->assign('lv00_current'.$sel00_type, 'id="tab_current"' );
//Tab UI選單顏色

//基本變數
  $tpl->assign('sel00_type', $sel00_type );
  $tpl->assign('search_key1', $search_key1 );
//基本變數

//資料列表
$tpl->gotoBlock('_ROOT');
//基本上switch內的case沒有作用，因為沒有$sel00_type這個變數會傳進來

        
$sqlStrWeekday = "SELECT * FROM customer_service_button01 GROUP BY weekday_name ORDER BY serialid";
$sqlStrWeedayCount = "SELECT COUNT(*) FROM (".$sqlStrWeekday.") as tmp";//計算數量
$resWeekday = SqlQuery1($sqlStrWeekday);
$resWeekdayCount = GetQueryValueCount1($sqlStrWeedayCount, "value");//計算數量SQL結果

$titleStr = "";
/*印標題*/
for($i=0; $i<intval($resWeekdayCount); $i++){
    $titleStr .= "<th style='text-align:left; width:12.5%;'>".$resWeekday->fields["weekday_name"]."</th>";
    $resWeekday->MoveNext();
}
$tpl->assign('titleStr', $titleStr);

/*印小時和打勾勾*/
$sqlStrHour = "SELECT * FROM customer_service_button01 GROUP BY hour ORDER BY serialid";
$sqlStrHourCount = "SELECT COUNT(*) FROM (".$sqlStrHour.") as tmp";//計算數量
$resHourCount = GetQueryValueCount1($sqlStrHourCount, "value");//計算數量
$resHour = SqlQuery1($sqlStrHour);//返回結果

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

for($i=0; $i<intval($resHourCount); $i++){
    $tpl->newBlock('Table1_row');
    $tpl->assign('rowid01ck', ($i % 2 == 1) ? 'class="odd"' : '' );
    $tpl->assign('hourTitle', $resHour->fields["hour"]);
    $rowStr = "";
    for($j=0; $j<intval($resWeekdayCount); $j++){
        $sqlStrButtonShow = "SELECT button_show FROM customer_service_button01 WHERE hour=".$i." AND weekday_name='".numberToWeekdayStr($j)."'";
        $resButtonShow = SqlQuery1($sqlStrButtonShow);
        $buttonShow = $resButtonShow->fields["button_show"];
        $nameAndId = strtolower(numberToWeekdayStr($j)).'_'.$i;
        $checkboxStr = "<input class='buttonShow' type='checkbox' name='".$nameAndId."' id='".$nameAndId."' ";//還沒有結束tag
        switch($buttonShow){
            case 'Y':
                $checkboxStr .= "checked";
                break;
            case 'N':
                $checkboxStr .= "";
                break;
        }
        $checkboxStr .= " />";//結束tag
        $rowStr .= "<td style='text-align:left;'>".$checkboxStr."</td>";
        $resButtonShow->MoveNext();
    }
    $tpl->assign('rowStr', $rowStr);
    $resHour->MoveNext();
}

    $tpl->printToScreen();
?>