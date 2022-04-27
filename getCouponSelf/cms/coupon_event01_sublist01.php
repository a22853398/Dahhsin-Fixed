<?php
ini_set('max_execution_time', '0');
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('../commonfile/str_function.inc.php');
include('00security_function.php');
$tpl_str = 'Coupon清單';
$tpl_name = 'coupon_event01';
$table_name = 'coupon_event01';
$owner_type = 'edit';
$owner_array01 = owner_check01($table_name);
$realpath01 = '../uploadfile/file'.$tpl_name.'/';
$dirname = '';

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$batch01 = strip_input('inputadm',$_REQUEST['batch01']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
$lv00_type = 'COUPON';

  $res = SqlQuery1("SET SESSION wait_timeout = 2880000");
  $res = SqlQuery1("SET SESSION interactive_timeout = 2880000");
//  $res = SqlQuery1("set GLOBAL max_allowed_packet = 2*1024*1024*10 ");
  
//處理 新增 修改 刪除
  $opt = trim($opt);
  switch ($opt) {
	case 'colorbth01_del01_real':
		if($owner_array01[3] == '1'){
			$strSQL1="delete from ".$table_name."_sublist01 where serialid = '". $pid ."' ";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
		}
		exit;
	break;
	case 'colorbth01':
		if($owner_array01[2] == '1'){
			//取回更新陣列
			$batchranvar = '';
			if(isset($_POST['row01'])) {
				for($i=0;$i<count($_POST['row01']);$i++) {
					//echo $_REQUEST['list1'][$i].'_list<br />';
					if($_POST['row01'][$i] != '' && $_POST['sortid'][$i] != ''){
						if($_POST['row01'][$i] == 'OLD'){
									
							$strSQL1  ="update ".$table_name."_sublist01 set ";
							if($_POST['sortid'][$i] != "")
								$strSQL1 .="sortid = '". $_POST['sortid'][$i] ."',";
							if($_POST['var01'][$i] != "")
								$strSQL1 .="var01 = '". $_POST['var01'][$i] ."',";							
							if($_POST['var02'][$i] != "")
								$strSQL1 .="var02 = '". $_POST['var02'][$i] ."',";
							$strSQL1 .="member_area = '". $_SESSION['session_user_lv01_name'] ."',";
							$strSQL1 .="member_id = '". $_SESSION['session_user_name'] ."' ";
							$strSQL1 .=" where serialid = '".$_POST['serialid'][$i]."' ";
							//echo $strSQL1.'<br />';
							$result = SqlQuery1($strSQL1);
						}
						if($_POST['row01'][$i] == 'NEW' && $_POST['var01'][$i] != ''){		
							//確認會員不存在Coupon活動
							$strSQL1="select * from ".$table_name."_sublist01 where lv01_id = '".$pid."' and var01='".$_POST['var01'][$i]."'";
							$res = SqlQuery1($strSQL1);
							//確認會員不存在Coupon活動
									
							//確認會員存在
							$strSQL1="select mbr_id from webmbr_list where mbr_id = '".$_POST['var01'][$i]."' and visible = 'Y' ";
							//echo $strSQL1.'<br />';
							$res_ck = SqlQuery1($strSQL1);
							//確認會員存在								
							if($res->RecordCount() == 0 && $res_ck->RecordCount() == 1){								
									
								//確認最後單號
								$rndstr = create_rndid(32);
								$nowdate = date('ymd',$NOW);
								//$strSQL1="select MAX(SUBSTRING(serialid,-4)) as MAXID from ".$table_name."_sublist01 where SUBSTRING(serialid,1,7) = 'F".$nowdate."' ";
								$strSQL1="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name."_sublist01 where SUBSTRING(serialid,1,7) = 'F".$nowdate."' ";
								//echo $strSQL1.'<br />';
								$res_getid = SqlQuery1($strSQL1);
								$nextval = $res_getid->fields['MAXID'] + 1;
								$nextval_id = 'F'.$nowdate.sprintf('%04s',$nextval).substr($rndstr,-4);
								//確認最後單號

								$strSQL1  ="insert into ".$table_name."_sublist01 (serialid,lv00_type,lv01_id,sortid,var01,var02,coupon,news_date,news_end_date,mail_ck,mail_exp,mail_date,member_area,member_id,add_date) values(";
								$strSQL1 .="'". $nextval_id ."',";
								$strSQL1 .="'". $lv00_type ."',";
								$strSQL1 .="'". $pid ."',";
								$strSQL1 .="'". $_POST['sortid'][$i] ."',";
								$strSQL1 .="'". $_POST['var01'][$i] ."',";
								$strSQL1 .="'". $_POST['var02'][$i] ."',";
								$strSQL1 .="'". strtoupper(substr($rndstr,-6)) ."',";
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="'". $_SESSION['session_user_lv01_name'] ."',";
								$strSQL1 .="'". $_SESSION['session_user_name'] ."',";
								$strSQL1 .="'". $sys_datetime ."')";
								//echo $strSQL1.'<br />';
								$result = SqlQuery1($strSQL1);
							}
						}
					}
				}
			}
			//取回更新陣列

			//大量輸入新增
			if($batch01 != ''){
				$arr01_tmp = explode(',',$batch01);
				$arr01_tmp_count = count($arr01_tmp);
				if($arr01_tmp_count > 0){
					for($k=0;$k<$arr01_tmp_count;$k++){
						if($arr01_tmp[$k] != ''){			
							//確認會員不存在Coupon活動
							$strSQL1="select * from ".$table_name."_sublist01 where lv01_id = '".$pid."' and var01='".$arr01_tmp[$k]."'";
							$res = SqlQuery1($strSQL1);
							//確認會員不存在Coupon活動
								
							//確認會員存在
							$strSQL1="select mbr_id from webmbr_list where mbr_id = '".$arr01_tmp[$k]."' and visible = 'Y' ";
							//echo $strSQL1.'<br />';
							$res_ck = SqlQuery1($strSQL1);
							//確認會員存在
							if($res->RecordCount() == 0 && $res_ck->RecordCount() == 1){

								//確認最後單號
								$rndstr = create_rndid(32);
								$nowdate = date('ymd',$NOW);
								$strSQL1="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name."_sublist01 where SUBSTRING(serialid,1,7) = 'F".$nowdate."' ";
								//echo $strSQL1.'<br />';
								$res_getid = SqlQuery1($strSQL1);
								$nextval = $res_getid->fields['MAXID'] + 1;
								$nextval_id = 'F'.$nowdate.sprintf('%04s',$nextval).substr($rndstr,-4);
								//確認最後單號

								$strSQL1  ="insert into ".$table_name."_sublist01 (serialid,lv00_type,lv01_id,sortid,var01,var02,coupon,news_date,news_end_date,mail_ck,mail_exp,mail_date,member_area,member_id,add_date) values(";
								$strSQL1 .="'". $nextval_id ."',";
								$strSQL1 .="'". $lv00_type ."',";
								$strSQL1 .="'". $pid ."',";
								$strSQL1 .="'". ($k+1) ."',";
								$strSQL1 .="'". $arr01_tmp[$k] ."',";
								$strSQL1 .="'',";
								$strSQL1 .="'". strtoupper(substr($rndstr,-6)) ."',";
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="NULL," ;
								$strSQL1 .="'". $_SESSION['session_user_lv01_name'] ."',";
								$strSQL1 .="'". $_SESSION['session_user_name'] ."',";
								$strSQL1 .="'". $sys_datetime ."')";
								//echo $strSQL1.'<br />';
								$result = SqlQuery1($strSQL1);
							}
						}
					}
				}
			}
			//大量輸入新增
		}
		redirect($tpl_name.'_sublist01.php?page='.$page.'&sel00_type='.$sel00_type.'&pid='.$pid);
		exit;
	break;
	
	case 'colorbth02':
		if($owner_array01[2] == '1'){
			$strSQL1="select mbr_id from webmbr_list where visible = 'Y' ";
			//echo $strSQL1.'<br />';
			$res_ck = SqlQuery1($strSQL1);
			//確認會員存在
			if($res_ck->RecordCount()>1){
				$k=1;
				
				$strSQL1  ="insert into ".$table_name."_sublist01 (serialid,lv00_type,lv01_id,sortid,var01,var02,coupon,news_date,news_end_date,mail_ck,mail_exp,mail_date,member_area,member_id,add_date) values ";
				while(!$res_ck->EOF){
					//確認最後單號
					$rndstr = create_rndid(32);
					$nowdate = date('ymd',$NOW);
					$strSQL2="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name."_sublist01 where SUBSTRING(serialid,1,7) = 'F".$nowdate."' ";
					//echo $strSQL2.'<br />';
					$res_getid = SqlQuery1($strSQL2);
					$nextval = $res_getid->fields['MAXID'] + 1;
					$nextval_id = 'F'.$nowdate.sprintf('%04s',$nextval).substr($rndstr,-4);
					//確認最後單號

					//$strSQL1  ="insert into ".$table_name."_sublist01 (serialid,lv00_type,lv01_id,sortid,var01,var02,coupon,news_date,news_end_date,mail_ck,mail_exp,mail_date,member_area,member_id,add_date) values(";
					$strSQL1 .="('". $nextval_id ."',";
					$strSQL1 .="'". $lv00_type ."',";
					$strSQL1 .="'". $pid ."',";
					$strSQL1 .="'". $k++ ."',";
					$strSQL1 .="'". $res_ck->fields['mbr_id'] ."',";
					$strSQL1 .="'',";
					$strSQL1 .="'". strtoupper(substr($rndstr,-6)) ."',";
					$strSQL1 .="NULL," ;
					$strSQL1 .="NULL," ;
					$strSQL1 .="NULL," ;
					$strSQL1 .="NULL," ;
					$strSQL1 .="NULL," ;
					//($news_date != "") ? $strSQL1 .="'". $news_date ."'," : $strSQL1 .="NULL," ;
					//($news_end_date != "") ? $strSQL1 .="'". $news_end_date ."'," : $strSQL1 .="NULL," ;
					$strSQL1 .="'". $_SESSION['session_user_lv01_name'] ."',";
					$strSQL1 .="'". $_SESSION['session_user_name'] ."',";
					$strSQL1 .="'". $sys_datetime ."'),";
					//echo $strSQL1.'<br />';
					//$result = SqlQuery1($strSQL1);
					
					$res_ck->MoveNext();
				}


				$strSQL1 = substr($strSQL1, 0, -1).";";
				$result = SqlQuery1($strSQL1);
			}			
		}

		redirect($tpl_name.'_sublist01.php?page='.$page.'&sel00_type='.$sel00_type.'&pid='.$pid);
		exit;
	break;
  }
//處理 新增 修改 刪除

//確認傳入參數
 //$pid=init_var($pid,0);
 if(strlen($pid) != 15){
   errback("本筆資料不存在");
   exit;
 }
  $tpl = new TemplatePower( './'.$tpl_name.'_sublist01.htm' );
  $tpl->assignInclude( 'external', './00external.php' );
  $tpl->assignInclude( 'mainMenu', './00mainMenu.php' );
  $tpl->prepare();

  $tpl->assignglobal('tpl_str', $tpl_str );
  $tpl->assignglobal('tpl_name', $tpl_name );
  $tpl->assignglobal('WYSIWYG_path', $config['WYSIWYG_path'] );
  $tpl->assign('pid', $pid );
  $tpl->assign('page', $page );
  $tpl->assign('sel00_type', $sel00_type );
  $tpl->assign('lv00_type', $lv00_type );

  $tpl->assign('page_splt01_add', '0' );

if($owner_array01[2] == '1'){
	$tpl->newBlock('tablist01_doPost');
	$tpl->gotoBlock('_ROOT');
}

//輸出資料
$tpl->gotoBlock("_ROOT");
  $strSQL1="select * from ".$table_name." where serialid = '". $pid ."' ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);

$tpl->gotoBlock("_ROOT");
  $tpl->assign('sortid', $res->fields['sortid'] );
  $tpl->assign('lv01_type', $res->fields['lv01_type'] );
  $tpl->assign('lv02_type', $res->fields['lv02_type'] );
  
  $tpl->assign('title01', $res->fields['title01'] );
  $tpl->assign('dis_amt', $res->fields['dis_amt'] );
  $tpl->assign('news_date', $res->fields['news_date'] );
  $tpl->assign('news_end_date', $res->fields['news_end_date'] );
  $tpl->assign('member_area', $res->fields['member_area'] );
  $tpl->assign('member_id', $res->fields['member_id'] );
  $tpl->assign('update_date', $res->fields['update_date'] );
//輸出資料

$tpl->gotoBlock('_ROOT');
//輸出產品清單
    $strSQL1="select * from ".$table_name."_sublist01 where lv01_id = '". $pid ."' order by sortid ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  if ($res->RecordCount()==0) $tpl->newBlock('All_member');

	$i=0;
  while(!$res->EOF){
			$i++;
			$tpl->newBlock('Table1_row');
			$tpl->assign('rowid01ck', ($i % 2 == 1) ? 'class="odd"' : '' );

			 	if($owner_array01[3] == '1' && $res->fields['visible']=="" )
			 	  $tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del_real" title="刪除" alt="刪除" />');

		$tpl->assign('serialid', $res->fields['serialid'] );
		$tpl->assign("serialidbt", '<input type="hidden" name="serialid[]" value="'.$res->fields['serialid'].'" />' );
		//$tpl->assign('sortid', $res->fields['sortid'] );
		$tpl->assign("sortid", '<input type="text" size="3" name="sortid[]" value="'.$res->fields['sortid'].'" title="'.$res->fields['sortid'].'" />' );
		$tpl->assign('lv01_id', $res->fields['lv01_id'] );

		$tpl->assign('var01', $res->fields['var01'] );
		$tpl->assign("var01bt", '<input type="text" size="40" name="var01[]" value="'.$res->fields['var01'].'" title="'.$res->fields['var01'].'" style="background-color:#F2F2F2" readonly />' );
		$tpl->assign('coupon', $res->fields['coupon'] );
		$tpl->assign('var02', $res->fields['var02'] );
		
		if ($res->fields['visible']!="") {
			$tpl->assign("var02bt", '<input type="text" size="30" name="var02[]" value="'.$res->fields['var02'].'" title="'.$res->fields['var02'].'" style="background-color:#F2F2F2" readonly />' );
		} else {
			$tpl->assign("var02bt", '<input type="text" size="30" name="var02[]" value="'.$res->fields['var02'].'" title="'.$res->fields['var02'].'" />' );			
		}

		$tpl->assign('add_date', $res->fields['add_date'] );
		$tpl->assign('member_str01', $res->fields['member_area'].'/'.$res->fields['member_id'] );
  $res->MoveNext();
  }
//輸出產品清單

  $tpl->printToScreen();
?>
