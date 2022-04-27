<?php
include '../commonfile/config_mang.inc.php';
include $config['templatebpath'] . 'class.TemplatePower.inc.php';
include '../commonfile/pageft_mang.inc.php';
include '00security_function.php';
include_once "../03lineNotify.php";


$tpl_str       = '統計資料';
$tpl_name      = 'order_statistic_data';
$table_name    = 'order_list';
$owner_type    = 'mang';
$owner_array01 = owner_check01($table_name);

//$opt                 = strip_input('inputadm', $_REQUEST['opt']);
$pid                 = strip_input('inputadm', $_REQUEST['pid']);//頁碼
$page                = strip_input('inputadm', $_REQUEST['page']);//頁數
$sel00_type          = strip_input('inputadm', $_REQUEST['sel00_type']);

#--- 接收HTML欄位的值 ---#
$start_date = strip_input('inputadm', $_REQUEST['start_date']);//起始日期
$end_date = strip_input('inputadm', $_REQUEST['end_date']);//結束日期
$search_type = strip_input('inputadm', $_REQUEST['search_type']);//搜尋方式


$tpl = new TemplatePower('./' . $tpl_name . '.htm');//輸出的網址
$tpl->assignInclude('external', './00external.php');
$tpl->assignInclude('mainMenu', './00mainMenu.php');
$tpl->prepare();

$tpl->assignglobal('tpl_str', $tpl_str);
$tpl->assignglobal('tpl_name', $tpl_name);

$tpl->gotoBlock('_ROOT');

//輸出分類
$strSQL1 = "select '1' as list_id, '統計資料' as list_title";
$res = SqlQuery1($strSQL1);
while (!$res->EOF) {
    $tpl->newBlock('tablist01_row');
    if ($sel00_type == '') {
        $sel00_type = $res->fields['list_id'];
    }
    if ($res->fields['list_id'] == $sel00_type) {
        $tpl->assign('sel00_type_sel', 'id="tab_active"');
        $tpl->assign('lv00_current_sel', 'id="tab_current"');
    }
    $tpl->assign('list_id', $res->fields['list_id']);
    $tpl->assign('list_title', $res->fields['list_title']);
    $res->MoveNext();
}
//輸出分類

$tpl->gotoBlock('_ROOT');
//Tab UI選單顏色
$tpl->assign('sel00_type' . $sel00_type, 'id="tab_active"');
$tpl->assign('lv00_current' . $sel00_type, 'id="tab_current"');
//Tab UI選單顏色



//基本變數
$tpl->assign('sel00_type', $sel00_type);
if ($sel00_type == 'S1') {
    $tpl->newBlock('block_row01_row');
    $tpl->gotoBlock('_ROOT');
}

$tpl->assign('search_orderid', $search_orderid);
$tpl->assign('search_process_type' . $search_process_type, 'selected="selected"');
$tpl->assign('search_pay_type' . $search_pay_type, 'selected="selected"');
$tpl->assign('search_orderdate1', $search_orderdate1);
$tpl->assign('search_orderdate2', $search_orderdate2);



//基本變數

####################################################################################################
//資料列表
/*
$tpl->gotoBlock('_ROOT');
switch ($sel00_type) {
case 'S1':
    $searchstr = "";
    if ($search_key1 != "") {
        $searchstr .= "OR a.gcname like '%" . $search_key1 . "%' OR a.orderid like '%" . $search_key1 . "%' ";
    }

    if ($searchstr != "") {
        $searchstr = "and ( " . substr($searchstr, 2, strlen($searchstr)) . " ) ";
    }

    if ($search_pay_type != "") {
        $searchstr .= " and a.pay_type = '" . $search_pay_type . "' ";
    }

    if ($search_orderdate1 != "") {
        $searchstr .= " and a.add_date >= '" . $search_orderdate1 . "' and a.add_date <= '" . $search_orderdate2 . "' ";
    }

    if ($search_orderid != "") {
        $searchstr .= " and a.orderid like '%" . $search_orderid . "%' ";
    }

    if ($search_process_type != "") {
        $searchstr .= " and a.process_type = '" . $search_process_type . "' ";
    }

    $strSQL1 = "select a.orderid,a.ord_price,a.mbr_serialid,a.gcname,a.gcname_ex,a.pay_type,a.pay_typestr,a.tran_type,a.tran_typestr,a.add_date,a.process_type,a.pay_check,a.add_ipaddress,
  	(select var01 from shop_defset where lv00_type = '6' and var03 = a.process_type and visible = 'Y' ) as process_typestr,
  	(select var01 from shop_defset where lv00_type = '7' and var03 = a.pay_check and visible = 'Y' ) as pay_check_typestr
  	from " . $table_name . " as a
  	where 1=1 " . $searchstr . " order by a.orderid DESC ";
    $strSQL2 = "select COUNT(*) from (select a.orderid from " . $table_name . " as a where 1=1 " . $searchstr . "  ) as tmp1 ";
    break;

default:
    $strSQL1 = "select a.orderid,a.ord_price,a.mbr_serialid,a.gcname,a.gcname_ex,a.pay_type,a.pay_typestr,a.tran_type,a.tran_typestr,a.add_date,a.process_type,a.pay_check,a.add_ipaddress,
  	(select var01 from shop_defset where lv00_type = '6' and var03 = a.process_type and visible = 'Y' ) as process_typestr,
  	(select var01 from shop_defset where lv00_type = '7' and var03 = a.pay_check and visible = 'Y' ) as pay_check_typestr
  	from " . $table_name . " as a
  	where 1=1
  	order by a.orderid desc ";
    $strSQL2 = "select COUNT(a.orderid) from " . $table_name . " as a where 1=1 ";
    break;
}
//echo $strSQL1.'<br />';
//echo $strSQL2.'<br />';
*/
/*
$total = GetQueryValueCount1($strSQL2, 'value');
pageft($total, $page_splt01);
$res = SqlQueryLimit1($strSQL1, $displaypg, $firstcount);
*/
/*
$tpl->assign('pagenav', $pagenav);
$tpl->assign('pagejump01', $pagejump01);
$tpl->assign('page_splt01_add', ($page_splt01 * ($page - 1)));
$tpl->assign('page_splt01', $page_splt01);
$tpl->assign('page', $page);
*/
/*
$i = 0;
while (!$res->EOF) {
    $i++;
    $tpl->newBlock('Table1_row');
    $tpl->assign('rowid01ck', ($i % 2 == 1) ? 'class="odd"' : '');

    //if($owner_array01[2] == '1'){
    /*
    if($sel00_type == 'X1'){
    if($owner_array01[3] == '1')
    $tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del_real" title="刪除" alt="刪除" />');
    if($owner_array01[2] == '1')
    $tpl->assign('bt_undel', '<img src="js/icon/accept.png" class="bt_undel" title="啟用" alt="啟用" />');
    }else{
    if($owner_array01[2] == '1')
    $tpl->assign('bt_undel', '<img src="js/icon/delete.png" class="bt_del" title="停用" alt="停用" />' );
    }
     */
/*
    if ($owner_array01[3] == '1' && ($res->fields['batch_date'] == '' || $res->fields['batch_date'] == '0000-00-00 00:00:00') && $res->fields['OrderFreightType'] == '0') {
        $tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del" title="取消" alt="取消" />');
    }

    $tpl->assign('bt_edit', '<img src="js/icon/page_edit.png" class="bt_edit" title="檢視" alt="檢視" />');
    //}

    $tpl->assign('serialid', $res->fields['orderid']);
    $tpl->assign("serialidbt", '<input type="hidden" name="serialid[]" value="' . $res->fields['orderid'] . '" />');
    //$tpl->assign('sortid', $res->fields['sortid'] );
    //$tpl->assign("sortid", '<input type="text" size="3" name="sortid[]" value="'.$res->fields['sortid'].'" title="'.$res->fields['sortid'].'" />' );

    $tpl->assign('orderid', $res->fields['orderid']);
    $tpl->assign('ord_price', $res->fields['ord_price']);
    $tpl->assign('mbr_serialid', $res->fields['mbr_serialid']);
    $tpl->assign('gcname', $res->fields['gcname']);
    $tpl->assign('gcname_ex', $res->fields['gcname_ex']);
    $tpl->assign('pay_type', $res->fields['pay_type']);

    //if($res->fields['pay_type'] == '02')
    //  $tpl->assign('pay_typestr', '<span style="color: red;">'.$res->fields['pay_typestr'].'</style>' );
    //else
    $tpl->assign('pay_typestr', $res->fields['pay_typestr']);

    $tpl->assign('tran_type', $res->fields['tran_type']);
    $tpl->assign('tran_typestr', $res->fields['tran_typestr']);

    if ($res->fields['process_type'] == '04') {
        $tpl->assign('process_typestr', '<span style="color: red;">' . $res->fields['process_typestr'] . '</style>');
    } else {
        $tpl->assign('process_typestr', $res->fields['process_typestr']);
    }

    if ($res->fields['pay_check'] == '02') {
        $tpl->assign('pay_check_typestr', '<span style="color: red;">' . $res->fields['pay_check_typestr'] . '</style>');
    } else {
        $tpl->assign('pay_check_typestr', $res->fields['pay_check_typestr']);
    }

    $tpl->assign('add_date', $res->fields['add_date']);

    $arr01 = explode(",", $res->fields['add_ipaddress']);
    //$tpl->assign('add_ipaddress', $res->fields['add_ipaddress']);
    $tpl->assign('add_ipaddress', $arr01[1]);

    $res->MoveNext();
}

$tpl->gotoBlock('_ROOT');
//輸出處理狀態
$strSQL1 = "select var01,var02,var03 from shop_defset where lv00_type = '6' and visible = 'Y' order by sortid ";
//echo $strSQL1.'<br />';
$res = SqlQuery1($strSQL1);
while (!$res->EOF) {
    $tpl->newBlock('process_type_row');
    if ($res->fields['var03'] == $search_process_type) {
        $tpl->assign('list_sel', 'selected="selected"');
    }
    $tpl->assign('list_id', $res->fields['var03']);
    $tpl->assign('list_title', $res->fields['var01']);
    $res->MoveNext();
}
//輸出處理狀態

$tpl->gotoBlock('_ROOT');
//輸出付款方式
$strSQL1 = "select var01,var02,var03 from shop_defset where lv00_type = '3' and visible = 'Y' order by sortid ";
//echo $strSQL1.'<br />';
$res = SqlQuery1($strSQL1);
while (!$res->EOF) {
    $tpl->newBlock('pay_type_row');
    if ($res->fields['var03'] == $search_pay_type) {
        $tpl->assign('list_sel', 'selected="selected"');
    }
    $tpl->assign('list_id', $res->fields['var03']);
    $tpl->assign('list_title', $res->fields['var01']);
    $res->MoveNext();
}
*/
//輸出付款方式
####################################################################################################

#--- 擷取標題純文字，捨去html，回傳 String ---#
function pickUpTitleText($string){
    $t1 = strpos($string, ">");//判斷是不是彩色標題 int，找第一個 ">" 在哪
    $t2 = strrpos($string, "<");//int從後面找第一個 "<" 在哪
    $t3 = substr($string, $t1+1, $t2-($t1+1));//擷取字串，string $t1要+1是因為會連">"一起抓，第三個參數為長度，$t2-($t1+1)因為前面的起始位置是$t1+1，所以要一起算
    if($t1 > 0){
        return $t3;
    }else{
        return $string;
    }
    //return $t1;
}
#--- 日期字串 ---#
$currentTime = date('Y-m-d', strtotime("now"));//現在時間
//如果沒有選時間的話，時間初始化
if($start_date == ""){
    $start_date = date('Y-m-01', strtotime("now"));//時間初始化為本月
}
if($end_date == ""){
    $end_date = $currentTime;
}
$end_date_SQL = date('Y-m-d', strtotime($end_date."+1 day"));//因為SQL的時間不會包含後面的，所以後面要加一天

/*依照不同搜尋方法決定SQL文*/
class ReturnSqlString{
    //計算幾筆
    public function returnSqlCount($sqlfields, $start_time, $end_time){
        $sql_temp = "SELECT ".$sqlfields." AS fieldsName, COUNT(".$sqlfields.") AS dataCount FROM order_list 
                    WHERE add_date BETWEEN '".$start_time."' AND '".$end_time."'
                    GROUP BY ".$sqlfields;
        return $sql_temp;
    }
    //計算幾筆，只有成功訂單
    public function returnSqlCountSuccess($sqlfields, $start_time, $end_time){
        $sql_temp = "SELECT ".$sqlfields." AS fieldsName, COUNT(".$sqlfields.") AS dataCount FROM order_list 
                    WHERE (add_date BETWEEN '".$start_time."' AND '".$end_time."') 
                    AND process_type NOT IN ('X1','X2')
                    GROUP BY ".$sqlfields;
        return $sql_temp;
    }
    
    //選擇所有訂單 
    public function returnSqlAllOrders($sqlfields, $start_time, $end_time){
        $sql_temp = "SELECT orderid, ord_price, gcname, gaddress1, gaddress2, gaddress3, add_date,
                    process_type, 
                    tran_type, tran_typestr,
                    pay_type, pay_typestr,
                    add_ipaddress
                    FROM order_list
                    WHERE add_date BETWEEN '".$start_time."' AND '".$end_time."'
                    ORDER BY ".$sqlfields.", orderid
                    ";
        return $sql_temp;
    }
    //計算幾筆會員
    public function returnSqlCountNewMemember($sqlfields, $start_time, $end_time){
        $sql_temp = "SELECT ".$sqlfields." AS fieldsName, COUNT(serialid) AS dataCount
                    FROM webmbr_list 
                    WHERE 1=1 
                    AND visible = 'Y' 
                    AND deny_check = 'N'
                    AND(reg_date BETWEEN '".$start_time."' AND '".$end_time."')
                    GROUP BY ".$sqlfields."
                    ORDER BY ".$sqlfields."
                    ";
        return $sql_temp;
    }
    
    //計算成功訂單金額
    public function returnTotalAmount($sqlfields, $start_time, $end_time){
        $sql_temp = "SELECT ".$sqlfields." AS fieldsName, SUM(ord_price-tran_price) AS dataCount
                    FROM order_list
                    WHERE 1=1
                    AND process_type NOT IN ('X1','X2')
                    AND (add_date BETWEEN '".$start_time."' AND '".$end_time."')
                    GROUP BY ".$sqlfields."
                    ORDER BY ".$sqlfields."
                    ";
        return $sql_temp;
    }
    
    //計算Coupon和贈品數量
    public function returnOrderDetail($prod_type, $start_time, $end_time){
        $sql_temp = "SELECT order_listdetail.prod_serialid AS fieldsName, 
                            order_listdetail.prod_name, 
                            COUNT(order_listdetail.orderid) AS dataCount
                            FROM order_listdetail 
                            JOIN order_list ON order_list.orderid = order_listdetail.orderid 
                            WHERE order_listdetail.ord_type = '".$prod_type."' 
                            AND order_list.process_type NOT IN ('X1','X2') 
                            AND order_list.add_date BETWEEN '".$start_time."' AND '".$end_time."'
                            GROUP BY order_listdetail.prod_serialid"
                    ;
        return $sql_temp;
    }
    
}
//新物件&執行函式&產生SQL文
$temp_obj = new ReturnSqlString();
switch($search_type){
    //訂單數量
    case "order_qty":
        $sql_fields = "process_type";
        $sqlStr_count_fin = $temp_obj->returnSqlCount($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_delivery_type":
        $sql_fields = "tran_type";
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_pay_type":
        $sql_fields = "pay_type";    
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_access_type":
        $sql_fields = "SUBSTRING(add_ipaddress, -3)";
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_amount_type":
        $sql_fields = "ROUND(ord_price, -2)";
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_daily":
        $sql_fields = "DATE_FORMAT(add_date, '%Y-%m-%d')";
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_monthly":
        $sql_fields = "DATE_FORMAT(add_date, '%Y-%m')";
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;
    case "order_yearly":
        $sql_fields = "DATE_FORMAT(add_date, '%Y')";
        $sqlStr_count_fin = $temp_obj->returnSqlCountSuccess($sql_fields, $start_date, $end_date_SQL);
        break;    
        
    //會員數量    
    case "member_daily":
        $sql_fields = "DATE_FORMAT(reg_date, '%Y-%m-%d')";
        $sqlStr_count_fin = $temp_obj->returnSqlCountNewMemember($sql_fields, $start_date, $end_date_SQL);
        break;
    case "member_address":
        $sql_fields = "mbr_address1";
        $sqlStr_count_fin = $temp_obj->returnSqlCountNewMemember($sql_fields, $start_date, $end_date_SQL);
        break;
    case "member_comefrom":
        $sql_fields = "reg_from";
        $sqlStr_count_fin = $temp_obj->returnSqlCountNewMemember($sql_fields, $start_date, $end_date_SQL);
        break;
    case "member_monthly":
        $sql_fields = "DATE_FORMAT(reg_date, '%Y-%m')";
        $sqlStr_count_fin = $temp_obj->returnSqlCountNewMemember($sql_fields, $start_date, $end_date_SQL);
        break;
    case "member_yearly":
        $sql_fields = "DATE_FORMAT(reg_date, '%Y')";
        $sqlStr_count_fin = $temp_obj->returnSqlCountNewMemember($sql_fields, $start_date, $end_date_SQL);
        break;
    case "member_dm":
        $sql_fields = "mbr_dm_yn";
        $sqlStr_count_fin = $temp_obj->returnSqlCountNewMemember($sql_fields, $start_date, $end_date_SQL);
        break;
    
    //訂單金額    
    case "amount_version":
        $sql_fields = "SUBSTRING(add_ipaddress, -3)";
        $sqlStr_count_fin = $temp_obj->returnTotalAmount($sql_fields, $start_date, $end_date_SQL);
        break;
    case "amount_daily":
        $sql_fields = "DATE_FORMAT(add_date, '%Y-%m-%d')";
        $sqlStr_count_fin = $temp_obj->returnTotalAmount($sql_fields, $start_date, $end_date_SQL);
        break;
    case "amount_pay_type":
        $sql_fields = "pay_type";
        $sqlStr_count_fin = $temp_obj->returnTotalAmount($sql_fields, $start_date, $end_date_SQL);
        break;
    case "amount_tran_type":
        $sql_fields = "tran_type";
        $sqlStr_count_fin = $temp_obj->returnTotalAmount($sql_fields, $start_date, $end_date_SQL);
        break;
    case "amount_monthly":
        $sql_fields = "DATE_FORMAT(add_date, '%Y-%m')";
        $sqlStr_count_fin = $temp_obj->returnTotalAmount($sql_fields, $start_date, $end_date_SQL);
        break;
    case "amount_yearly":
        $sql_fields = "DATE_FORMAT(add_date, '%Y')";
        $sqlStr_count_fin = $temp_obj->returnTotalAmount($sql_fields, $start_date, $end_date_SQL);
        break;
        
    case "coupon_used":
        $prod_type = "C01";
        $sqlStr_count_fin = $temp_obj->returnOrderDetail($prod_type, $start_date, $end_date_SQL);
        break;
    case "gift_qty":
        $prod_type = "G01";
        $sqlStr_count_fin = $temp_obj->returnOrderDetail($prod_type, $start_date, $end_date_SQL);
        break;
        
    default://初始顯示訂單數量
        $sql_fields = "process_type";
        $sqlStr_count_fin = $temp_obj->returnSqlCount($sql_fields, $start_date, $end_date_SQL);
        break;
}
post_message("\n".$sqlStr_count_fin."\n");
$sqlStr2 = "SELECT COUNT(*) FROM ($sqlStr_count_fin) AS tmp1";//計算選取的表單有幾筆資料，要AS成一個變數
//頁碼相關的取資料
$page = strip_input('inputadm',$_REQUEST['page']);            
$totalCount= GetQueryValueCount1($sqlStr2,'value');//計算SQL有幾個結果，回傳int
$page_splt01 = 50;
pageft($totalCount, $page_splt01);
$resCount = SqlQueryLimit1($sqlStr_count_fin,$displaypg,$firstcount);//取結果，執行SQL，配合頁數
//$resCount = SqlQuery1($sqlStr_count_fin);//普通的取資料

$j=0;//判斷奇數行偶數行的數
while(!$resCount -> EOF){
    $j++;
    //開啟區域
    $tpl->newBlock('Table2_row');
    //存入各欄位
    $tpl->assign('rowid02ck', ($j % 2 == 1) ? 'class="odd"' : '');
    
    $fieldsName = $resCount->fields['fieldsName'];
    $fieldsNameStr = "";
    //顯示字串
    if($search_type == "order_qty" || $search_type == ""){
        switch($fieldsName){
            case "01":
                $fieldsNameStr = "新增";
                break;
            case "02":
                $fieldsNameStr = "";
                break;
            case "03":
                $fieldsNameStr = "備貨";
                break;
            case "04":
                $fieldsNameStr = "<span style='color:red;'>出貨</span>";
                break;
            case "F1":
                $fieldsNameStr = "<span style='color:red;'>完成</span>";
                break;
            case "X1":
                $fieldsNameStr = "管理員取消";
                break;
            case "X2":
                $fieldsNameStr = "使用者取消";
                break;    
        }
    }else if($search_type == "order_delivery_type" || $search_type == "amount_tran_type"){
        switch($fieldsName){
            case "01":
                $fieldsNameStr = "宅配到府";
                break;
            case "02":
                $fieldsNameStr = "超商取貨";
                break;
            case "13":
                $fieldsNameStr = "7-11取貨";
                break;
            case "15":
                $fieldsNameStr = "全家取貨";
                break;
            case "16":
                $fieldsNameStr = "OK取貨";
                break;
            case "17":
                $fieldsNameStr = "萊爾富取貨";
                break;
            case "20":
                $fieldsNameStr = "海外配送";
                break;
        }
    }else if($search_type == "order_pay_type" || $search_type == "amount_pay_type"){
        switch($fieldsName){
            case "01":
                $fieldsNameStr = "ATM轉帳";
                break;
            case "02":
                $fieldsNameStr = "線上刷卡";
                break;
            case "03":
                $fieldsNameStr = "貨到付款";
                break;
            case "04":
                $fieldsNameStr = "郵政劃撥";
                break;
            case "05":
                $fieldsNameStr = "刷卡分三期";
                break;
            case "06":
                $fieldsNameStr = "刷卡分六期";
                break;
            case "07":
                $fieldsNameStr = "行動支付";
                break;    
        }        
    }else if($search_type == "order_access_type" || $search_type == "amount_version"){
        switch($fieldsName){
            case "MB1":
                $fieldsNameStr = "手機版網頁";
                break;
            case "PC1":
                $fieldsNameStr = "電腦版網頁";
                break;  
        }        
    }else if($search_type == "order_amount_type"){
        $fieldsNameStr = intval($fieldsName)-100 . "～" . $fieldsName;
    }else if($search_type == "member_daily"){
        $fieldsNameStr = $fieldsName;
    }else if($search_type == "member_address"){
        $fieldsNameStr = $fieldsName;
    }else if($search_type == "member_comefrom"){
        switch($fieldsName){
            case "facebook":
                $fieldsNameStr = "臉書";
                break;
            case "WWW2012":
                $fieldsNameStr = "官方網站";
                break;    
            case "OLD":
                $fieldsNameStr = "舊官網移植";
                break;
        }
    }else if($search_type == "amount_daily" || $search_type == "amount_monthly" || $search_type == "amount_yearly" 
            || $search_type == "order_daily" || $search_type == "order_monthly" || $search_type == "order_yearly"){
        $fieldsNameStr = $fieldsName;
    }else if($search_type == "coupon_used" || $search_type == "gift_qty"){
        $fieldsNameStr = $resCount->fields['prod_name'];
    }else if($search_type === "member_dm"){
        switch($fieldsName){
            case "Y":
                $fieldsNameStr = "願意";
                break;
            case "N":
                $fieldsNameStr = "不願意";
                break;
            default:
                $fieldsNameStr = "未填寫";
                break;
        }
    }
    
    
    $tpl->assign('fieldsName', $fieldsName);//資料欄位
    $tpl->assign('fieldsNameStr',$fieldsNameStr);//資料欄位字串
    $dataCount = $resCount->fields['dataCount'];//資料數量
    $tpl->assign('dataCount', $dataCount);
    $sum +=  intval($dataCount);//總數
    //換一筆資料
    $resCount->MoveNext();
    
}
//關閉區域
$tpl->gotoBlock('_ROOT');
$tpl->assign('sum', $sum);


/************ 回傳HTML *************/
$tpl->assign('start_date', $start_date);
$tpl->assign('end_date', $end_date);
$tpl->assign('search_type', $search_type);
//頁碼相關
$tpl->assign('pagenav', $pagenav );
$tpl->assign('pagejump01', $pagejump01 );
$tpl->assign('page_splt01_add', ($page_splt01*($page-1)) );
$tpl->assign('page_splt01', $page_splt01 );
$tpl->assign('page', $page );

$tpl->printToScreen();
?>
