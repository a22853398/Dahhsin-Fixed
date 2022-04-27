<?php
set_time_limit(600);
include('../commonfile/config_mang.inc.php');
include('../commonfile/str_function.inc.php');
include('00security_function.php');
$tpl_str = 'Coupon';
$tpl_name = 'coupon_event01';
$table_name = 'coupon_event01';
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
$sortid = strip_input('inputadm',$_REQUEST['sortid']);
$title01 = strip_input('inputadm',$_REQUEST['title01']);
$ord_limit = strip_input('inputadm',$_REQUEST['ord_limit']);
$dis_amt = strip_input('inputadm',$_REQUEST['dis_amt']);

$news_date = strip_input('inputadm',$_REQUEST['news_date']);
$news_end_date = strip_input('inputadm',$_REQUEST['news_end_date']);
$visible= strip_input('inputadm',$_REQUEST['visible']);
if($visible == ''){
  $visible = 'N';
}

  $opt = trim($opt);
  switch ($opt) {
	case 'add':
		if($owner_array01[1] == '1'){

			//鎖定表列
			//	$strSQL1="LOCK TABLES ".$table_name." WRITE; ";
			//	$res = SqlQuery1($strSQL1);
			//鎖定表列
			//確認最後單號起始
			$strSQL1="select SUBSTRING( serialid,1,1) as key01 from ".$table_name." order by serialid desc ";
			//echo $strSQL1.'<br />';
			$res_getid = SqlQuery1($strSQL1);
			if($res_getid->RecordCount() == 0 ) {
				$nextkey01 = 'A';
			} else {
				$nextkey01 = $res_getid->fields['key01'];
			}
			//確認最後單號
			//確認最後單號
			$rndstr = create_rndid(32);
			$nowdate = date('ymd',$NOW);
			$strSQL1="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name." where SUBSTRING(serialid,1,7) = '".$nextkey01.$nowdate."' ";
			//echo $strSQL1.'<br />';
			$res_getid = SqlQuery1($strSQL1);
			if($res_getid->fields['MAXID'] >= 9999){
				$nextkey01 = chr(ord($nextkey01)+1);
				$strSQL1="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name." where SUBSTRING(serialid,1,7) = '".$nextkey01.$nowdate."' ";
				//echo $strSQL1.'<br />';
				$res_getid = SqlQuery1($strSQL1);
			}
			$nextval = $res_getid->fields['MAXID'] + 1;
			$nextval_id = ''.$nextkey01.$nowdate.sprintf('%04s',$nextval).substr($rndstr,-4);
			$dirname = $nextval_id.'/';
			//確認最後單號
			//解除鎖定表列
			//	$strSQL1="UNLOCK TABLES; ";
			//	$res = SqlQuery1($strSQL1);
			//解除鎖定表列
		

			$strSQL1  ="insert into ".$table_name." (serialid,lv00_type,sortid,title01,dis_amt,news_date,news_end_date,visible,member_area,member_id,add_ipaddress,add_date,update_ipaddress,update_date) values(";
			$strSQL1 .="'". $nextval_id ."',";
			$strSQL1 .="'". $lv00_type ."',";
			$strSQL1 .="'". $sortid ."',";
			$strSQL1 .="'". $title01 ."',";
			$strSQL1 .="'". $dis_amt ."',";
			$strSQL1 .="'". $news_date ."',";
			$strSQL1 .="'". $news_end_date ."',";
			$strSQL1 .="'". $visible ."',";

			$strSQL1 .="'". $_SESSION['session_user_lv01_name'] ."',";
			$strSQL1 .="'". $_SESSION['session_user_name'] ."',";
			$strSQL1 .="'". $remote_ipaddress ."',";
			$strSQL1 .="'". $sys_datetime ."',";
			$strSQL1 .="'". $remote_ipaddress ."',";
			$strSQL1 .="'". $sys_datetime ."')";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
		}
		//redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
		redirect($tpl_name.'_edit.php?pid='.$nextval_id.'&sel00_type='.$lv00_type);
	break;

	case 'edit':
		if($owner_array01[2] == '1'){
			
		$strSQL1  ="update ".$table_name." set ";
		$strSQL1 .="lv00_type='". $lv00_type ."',";
		$strSQL1 .="sortid='". $sortid ."',";
		$strSQL1 .="title01='". $title01 ."',";
		$strSQL1 .="dis_amt='". $dis_amt ."',";
		$strSQL1 .="news_date='". $news_date ."',";
		$strSQL1 .="news_end_date='". $news_end_date ."',";
		$strSQL1 .="visible='". $visible ."',";

		$strSQL1 .="member_area = '". $_SESSION['session_user_lv01_name'] ."',";
		$strSQL1 .="member_id = '". $_SESSION['session_user_name'] ."',";
		$strSQL1 .="update_ipaddress='". $remote_ipaddress ."', ";
		$strSQL1 .="update_date='". $sys_datetime ."' ";
		$strSQL1 .=" where serialid='". $pid ."'";
		//echo $strSQL1.'<br />';
		$result = SqlQuery1($strSQL1);
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'del01':
		if($owner_array01[2] == '1'){
		$strSQL1="update ".$table_name." set visible = 'N' where serialid = '". $pid ."' ";
		//echo $strSQL1.'<br />';
		$result = SqlQuery1($strSQL1);
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'undel01':
		if($owner_array01[2] == '1'){
		$strSQL1="update ".$table_name." set visible = 'Y' where serialid = '". $pid ."' ";
		//echo $strSQL1.'<br />';
		$result = SqlQuery1($strSQL1);
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'del01_real':
		if($owner_array01[3] == '1'){
			$strSQL1="delete from ".$table_name." where serialid = '". $pid ."' ";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
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
							$result = SqlQuery1($strSQL1);
					  }
					}
				}
		//取回更新陣列
		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

}
?>