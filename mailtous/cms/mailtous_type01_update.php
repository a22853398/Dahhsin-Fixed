<?php
include('../commonfile/config_mang.inc.php');
include('00security_function.php');
$tpl_str = '寄信給我們';
$tpl_name = 'mailtous_type01';
$table_name = 'mailtous_type01';
$owner_type = 'update';
$owner_array01 = owner_check01($table_name);
$realpath01 = '../uploadfile/file'.$tpl_name.'/';
$dirname = '';
$lv00_type = strip_input('inputadm',$_REQUEST['lv00_type']);
if($lv00_type == ''){
  $lv00_type = '1';
}


$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
$filesel = strip_input('inputadm',$_REQUEST['filesel']);

$sortid = strip_input('inputadm',$_REQUEST['sortid']);//排序
$parent_id = strip_input('inputadm',$_REQUEST['parent_id']);//哪一個
$lv_type = '';//父子，子母
$value01 = strip_input('inputadm',$_REQUEST['value01']);//代碼
if($parent_id == '0'){
    $lv_type = '0';
}else{
    $lv_type = '1';
    $value01 = $parent_id.'_'.$value01;
}
$email01 = strip_input('inputadm',$_REQUEST['email01']);//寄信信箱
$email02 = strip_input('inputadm',$_REQUEST['email02']);//CC信箱
$title01 = strip_input('inputadm',$_REQUEST['title01']);// 標題
$content01 = strip_input('inputadm',$_REQUEST['content01']);//自動回覆內容
$visible = strip_input('inputadm',$_REQUEST['visible']);//啟用





$opt = trim($opt);
switch ($opt) {
	case 'add':
	    /* 排序根據父母變動處理 */
        if($parent_id != '0'){
            $strSql_whereParent = "SELECT sortid FROM $table_name WHERE value01 = '$parent_id'";
            $strSql_howManyChildren = "SELECT * FROM $table_name WHERE parent_id='$parent_id' AND visible='Y' ORDER BY sortid DESC LIMIT 1";
            $sortid = intval(SqlQuery1($strSql_howManyChildren)->fields["sortid"])+1;
        }
		if($owner_array01[1] == '1'){
    		$strSQL1  ="insert into ".$table_name." (sortid, lv_type, parent_id, title01, value01, email01, email02, content01, update_date, visible, member_area, member_id) values(";
    		$strSQL1 .="'". $sortid ."',";
    		$strSQL1 .="'". $lv_type ."',";
    		$strSQL1 .="'". $parent_id ."',";
    		$strSQL1 .="'". $title01 ."',";
    		$strSQL1 .="'". $value01 ."',";
    		$strSQL1 .="'". $email01 ."',";
    		$strSQL1 .="'". $email02 ."',";
    		$strSQL1 .="'". $content01 ."',";
    		$strSQL1 .="'". date("Y-m-d H:i:s") ."',";
    		$strSQL1 .="'". $visible ."',";
    		$strSQL1 .="'". $_SESSION['session_user_lv01_name'] ."',";
    		$strSQL1 .="'". $_SESSION['session_user_name'] ."')";
    		//echo $strSQL1.'<br />';
    		$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
    		$result = SqlQuery1($strSQL1);
    		$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'edit':
		if($owner_array01[2] == '1'){
    		$strSQL1  ="update ".$table_name." set ";
    		$strSQL1 .="lv_type='". $lv_type ."',";
    		$strSQL1 .="parent_id='". $parent_id ."',";
    		$strSQL1 .="sortid='". $sortid ."',";
    		$strSQL1 .="title01='". $title01 ."',";
    		$strSQL1 .="value01='". $value01 ."',";
    		$strSQL1 .="email01='". $email01 ."',";
    		$strSQL1 .="email02='". $email02 ."',";
    		$strSQL1 .="content01='". $content01 ."',";
    		$strSQL1 .="member_area= '". $_SESSION['session_user_lv01_name'] ."',";
    		$strSQL1 .="member_id='". $_SESSION['session_user_name'] ."',";
    		$strSQL1 .="update_date='". $sys_datetime ."', ";
    		$strSQL1 .="visible='". $visible ."' ";
    		$strSQL1 .=" where serialid='". $pid ."'";
    		//echo $strSQL1.'<br />';
    		$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
    		$result = SqlQuery1($strSQL1);
    		$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'del01':
		if($owner_array01[2] == '1'){
    		$strSQL1="update ".$table_name." set visible = 'N' where serialid = ". $pid ." ";
    		//echo $strSQL1.'<br />';
    		$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
    		$result = SqlQuery1($strSQL1);
    		$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'undel01':
		if($owner_array01[2] == '1'){
    		$strSQL1="update ".$table_name." set visible = 'Y' where serialid = ". $pid ." ";
    		//echo $strSQL1.'<br />';
    		$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
    		$result = SqlQuery1($strSQL1);
    		$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'del01_real':
		if($owner_array01[3] == '1'){
			$strSQL1="delete from ".$table_name." where serialid = ". $pid ." ";
			//echo $strSQL1.'<br />';
			$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
			$result = SqlQuery1($strSQL1);
			$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'bth01':
		if($owner_array01[2] == '1'){
		//取回更新陣列
			$batchranvar = '';
			if(isset($_POST['serialid'])) {
				for($i=0;$i<count($_POST['serialid']);$i++) {
					//echo $_REQUEST['list1'][$i].'_list<br />';
					if($_POST['serialid'][$i] != '' && $_POST['sortid'][$i] != ''){							
					    $strSQL1  ="update ".$table_name." set ";
						if($_POST['sortid'][$i] != "")
				    	    $strSQL1 .="sortid = '". $_POST['sortid'][$i] ."',";
							$strSQL1 .="member_area = '". $_SESSION['session_user_lv01_name'] ."',";
							$strSQL1 .="member_id = '". $_SESSION['session_user_name'] ."' ";
							$strSQL1 .=" where serialid = '".$_POST['serialid'][$i]."' ";
							//echo $strSQL1.'<br />';
							$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
							$result = SqlQuery1($strSQL1);
							$config['sql_link1']->CompleteTrans();//關閉transaction
					}
				}
			}
		//取回更新陣列
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;
}
?>