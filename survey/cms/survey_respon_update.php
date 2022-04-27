<?php
include('../commonfile/config_mang.inc.php');
include('00security_function.php');
$tpl_str = '問卷回覆瀏覽';
$tpl_name = 'survey_respon';
$table_name = 'survey_respon';
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

$opt = trim($opt);

switch ($opt) {
    /* 基本上應該只會用到編輯，且只准許改分析狀態 */
	case 'edit':
		if($owner_array01[2] == '1'){
		    $memberArea = $_SESSION['session_user_lv01_name'];//後台人員群組
		    $memberID = $_SESSION['session_user_name'];//後台人員帳號名稱
		    $memberIpaddress = $remote_ipaddress;//後台人員操作IP位址
		    $check_status = $_POST['check_status'];//分析狀態
    		$strSQL1 = "UPDATE ".$table_name." 
    		            SET member_area = '".$memberArea."', 
    		                member_id = '".$memberID."', 
    		                update_date = '".date("Y-m-d h:i:s")."', 
    		                update_ipaddress = '".$memberIpaddress."', 
    		                check_status = '".$check_status."' 
                        WHERE serialid = '".$pid."'" ;
		}
		
        $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
		$res = SqlQuery1($strSQL1);//執行SQL
		$config['sql_link1']->CompleteTrans();//關閉transaction
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	    break;
}

?>