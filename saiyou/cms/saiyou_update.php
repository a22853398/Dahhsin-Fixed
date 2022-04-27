<?php
include('../commonfile/config_mang.inc.php');
include('00security_function.php');
$tpl_str = '採用情報';
$tpl_name = 'saiyou';
$table_name = 'saiyou';
$owner_type = 'update';
$owner_array01 = owner_check01($table_name);
$realpath01 = '../uploadfile/file'.$tpl_name.'/';
$dirname = '';

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
$filesel = strip_input('inputadm',$_REQUEST['filesel']);

$lv00_type = strip_input('inputadm',$_REQUEST['lv00_type']);
if($lv00_type == ''){
  $lv00_type = '1';
}
//要擷取的變數區
$sortid = strip_input('inputadm',$_REQUEST['sortid']);
$depart01 = strip_input('inputadm',$_REQUEST['depart01']);
$title01 = strip_input('inputadm',$_REQUEST['title01']);
$content01 = strip_input('inputadm',$_REQUEST['content01']);
$requirement01 = strip_input('inputadm',$_REQUEST['requirement01']);
$method01 = strip_input('inputadm',$_REQUEST['method01']);
$place01 = strip_input('inputadm',$_REQUEST['place01']);
$worktime01 = strip_input('inputadm',$_REQUEST['worktime01']);
$salary01 = strip_input('inputadm',$_REQUEST['salary01']);
$other01 = strip_input('inputadm',$_REQUEST['other01']);
$visible = strip_input('inputadm',$_REQUEST['visible']);

  $opt = trim($opt);
  switch ($opt) {
	case 'add':
		if($owner_array01[1] == '1'){

		//鎖定表列
		//	$strSQL1="LOCK TABLES ".$table_name." WRITE; ";
		//	$res = SqlQuery1($strSQL1);
		//鎖定表列
			//確認最後單號
			$rndstr = create_rndid(32);
			$nowdate = date('ymd',$NOW);
			$strSQL1="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name." where SUBSTRING(serialid,1,7) = 'M".$nowdate."' ";
			//echo $strSQL1.'<br />';
			$res_getid = SqlQuery1($strSQL1);
			$nextval = $res_getid->fields['MAXID'] + 1;
			$nextval_id = 'M'.$nowdate.sprintf('%04s',$nextval).substr($rndstr,-4);
			$dirname = $nextval_id.'/';
		    //確認最後單號
		//解除鎖定表列
		//	$strSQL1="UNLOCK TABLES; ";
		//	$res = SqlQuery1($strSQL1);
		//解除鎖定表列

		$strSQL1 =  "insert into ".$table_name.
		            " (serialid, lv00_type, sortid, depart01, title01, content01, requirement01, method01, place01, worktime01, salary01, other01, add_date, add_ipaddress, update_date, update_ipaddress, member_area, member_id, visible) ".
		            "values(";
		$strSQL1 .="'". $nextval_id ."',";
		$strSQL1 .="'". $lv00_type ."',";
		$strSQL1 .="'". $sortid ."',";
		$strSQL1 .="'". $depart01 ."',";
		$strSQL1 .="'". $title01 ."',";
		$strSQL1 .="'". $content01 ."',";
		$strSQL1 .="'". $requirement01 ."',";
		$strSQL1 .="'". $method01 ."',";
		$strSQL1 .="'". $place01 ."',";
		$strSQL1 .="'". $worktime01 ."',";
		$strSQL1 .="'". $salary01 ."',";
		$strSQL1 .="'". $other01 ."',";
		$strSQL1 .="'". $sys_datetime ."',";
		$strSQL1 .="'". $remote_ipaddress ."',";
		$strSQL1 .="'". $sys_datetime ."',";
		$strSQL1 .="'". $remote_ipaddress ."',";
		$strSQL1 .="'". $_SESSION['session_user_lv01_name'] ."',";
		$strSQL1 .="'". $_SESSION['session_user_name'] ."',";
		$strSQL1 .="'". "Y" ."')";

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
		$strSQL1 .="lv00_type='". $lv00_type ."',";
		$strSQL1 .="sortid='". $sortid ."',";
		$strSQL1 .="depart01='". $depart01 ."',";
		$strSQL1 .="title01='". $title01 ."',";
		$strSQL1 .="content01='". $content01 ."',";
		$strSQL1 .="requirement01='". $requirement01 ."',";
		$strSQL1 .="method01='". $method01 ."',";
		$strSQL1 .="place01='". $place01 ."',";
		$strSQL1 .="worktime01='". $worktime01 ."',";
		$strSQL1 .="salary01='". $salary01 ."',";
		$strSQL1 .="other01='". $other01 ."',";
		$strSQL1 .="update_date='". $sys_datetime ."',";
		$strSQL1 .="update_ipaddress='". $remote_ipaddress ."',";
		$strSQL1 .="visible='". $visible ."',";
		$strSQL1 .="member_area = '". $_SESSION['session_user_lv01_name'] ."',";
		$strSQL1 .="member_id = '". $_SESSION['session_user_name'] ."'";
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
		$strSQL1="update ".$table_name." set visible = 'N' where serialid = '". $pid ."' ";
		//echo $strSQL1.'<br />';
		$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
		$result = SqlQuery1($strSQL1);
		$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'undel01':
		if($owner_array01[2] == '1'){
		$strSQL1="update ".$table_name." set visible = 'Y' where serialid = '". $pid ."' ";
		//echo $strSQL1.'<br />';
		$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
		$result = SqlQuery1($strSQL1);
		$config['sql_link1']->CompleteTrans();//關閉transaction
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'del01_real':
		if($owner_array01[3] == '1'){
			$strSQL1="delete from ".$table_name." where serialid = '". $pid ."' ";
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