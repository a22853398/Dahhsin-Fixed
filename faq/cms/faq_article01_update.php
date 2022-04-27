<?php
include('../commonfile/config_mang.inc.php');
include('../commonfile/str_function.inc.php');
include('00security_function.php');
$tpl_str = '常見問題';
$tpl_name = 'faq_article01';
$table_name = 'faq_article01';
$owner_type = 'update';
$owner_array01 = owner_check01($table_name);
$realpath01 = '../uploadfile/file'.$tpl_name.'/';
$dirname = '';

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$mpid = strip_input('inputadm',$_REQUEST['mpid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$filesel = strip_input('inputadm',$_REQUEST['filesel']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
$lv00_type = strip_input('inputadm',$_REQUEST['lv00_type']);

$sortid= strip_input('inputadm',$_REQUEST['sortid']);
$lv01_type = strip_input('inputadm',$_REQUEST['lv01_type']);
if($lv01_type == ''){
  $lv01_type = '1';
}
$lv02_type= strip_input('inputadm',$_REQUEST['lv02_type']);
if($lv02_type == ''){
  $lv02_type = '1';
}
$title01= strip_input('inputadm',$_REQUEST['title01']);
$content01= strip_input('inputadm',$_REQUEST['content01']);
$content02= strip_input('inputadm',$_REQUEST['content02']);
$weblink= strip_input('inputadm',$_REQUEST['weblink']);
$weblink_target= strip_input('inputadm',$_REQUEST['weblink_target']);
$upfile1_wh= strip_input('inputadm',$_REQUEST['upfile1_wh']);
$upfile2_wh= strip_input('inputadm',$_REQUEST['upfile2_wh']);
$news_date = strip_input('inputadm',$_REQUEST['news_date']);
$news_end_date = strip_input('inputadm',$_REQUEST['news_end_date']);
$topup= strip_input('inputadm',$_REQUEST['topup']);
$visible= strip_input('inputadm',$_REQUEST['visible']);
if($visible == ''){
  $visible = 'N';
}
if($topup == ''){
  $topup = 'N';
}
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

        //確認目錄存在
        if(!@is_dir($realpath01.$dirname)){
          @mkdir($realpath01.$dirname,0777);
        }
        //確認目錄存在

			if (is_uploaded_file($_FILES['file1']['tmp_name'])){
				$filename = basename($_FILES['file1']['name']);
				$tempsubn = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$rndstr = create_rndid(32).'_'.$NOW;
				$filenamestr1 = '1_'.$rndstr.'.'.$tempsubn;
							copy($_FILES['file1']['tmp_name'], $realpath01.$dirname.$filenamestr1);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr1 , $realpath01.$dirname.'m_'.$filenamestr1,160,160);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr1 , $realpath01.$dirname.'s_'.$filenamestr1,70,70);
			}
			if (is_uploaded_file($_FILES['file2']['tmp_name'])){
				$filename = basename($_FILES['file2']['name']);
				$tempsubn = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$rndstr = create_rndid(32).'_'.$NOW;
				$filenamestr2 = '2_'.$rndstr.'.'.$tempsubn;
							copy($_FILES['file2']['tmp_name'], $realpath01.$dirname.$filenamestr2);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr2 , $realpath01.$dirname.'m_'.$filenamestr2,160,160);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr2 , $realpath01.$dirname.'s_'.$filenamestr2,70,70);
			}
		$strSQL1  ="insert into ".$table_name."(serialid,lv01_type,lv02_type,sortid,title01,content01,content02,weblink,weblink_target,upfile1,upfile2,upfile1_type,upfile2_type,upfile1_wh,upfile2_wh,news_date,news_end_date,topup,visible,member_area,member_id,add_ipaddress,add_date,update_ipaddress,update_date) values(";
		$strSQL1 .="'". $nextval_id ."',";
		$strSQL1 .="'". $lv01_type ."',";
		$strSQL1 .="'". $lv02_type ."',";
		$strSQL1 .="'". $sortid ."',";
		$strSQL1 .="'". $title01 ."',";
		$strSQL1 .="'". $content01 ."',";
		$strSQL1 .="'". $content02 ."',";
		$strSQL1 .="'". $weblink ."',";
		$strSQL1 .="'". $weblink_target ."',";
		($filenamestr1 != "") ? $strSQL1 .="'". $filenamestr1 ."'," : $strSQL1 .="NULL," ;
		($filenamestr2 != "") ? $strSQL1 .="'". $filenamestr2 ."'," : $strSQL1 .="NULL," ;
		($filenamestr1 != "") ? $strSQL1 .="'". $_FILES['file1']['type'] ."'," : $strSQL1 .="''," ;
		($filenamestr2 != "") ? $strSQL1 .="'". $_FILES['file2']['type'] ."'," : $strSQL1 .="''," ;
	  $strSQL1 .="'". $upfile1_wh ."',";
	  $strSQL1 .="'". $upfile2_wh ."',";
	  ($news_date != "") ? $strSQL1 .="'". $news_date ."'," : $strSQL1 .="NULL," ;
	  ($news_end_date != "") ? $strSQL1 .="'". $news_end_date ."'," : $strSQL1 .="NULL," ;
		$strSQL1 .="'". $topup ."',";
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
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case 'edit':
		if($owner_array01[2] == '1'){
		  $strSQL1="select * from ".$table_name." where serialid = '". $pid ."' ";
		  //echo $strSQL1.'<br />';
	  	$res = SqlQuery1($strSQL1);
	  	$dirname = $res->fields['serialid'].'/';
	  	$upfile1 = $res->fields['upfile1'];
	  	$upfile2 = $res->fields['upfile2'];

        //確認目錄存在
        if(!@is_dir($realpath01.$dirname)){
          @mkdir($realpath01.$dirname,0777);
        }
        //確認目錄存在

			if (is_uploaded_file($_FILES['file1']['tmp_name'])){
				$filename = basename($_FILES['file1']['name']);
				$tempsubn = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$rndstr = create_rndid(32).'_'.$NOW;
				$filenamestr1 = '1_'.$rndstr.'.'.$tempsubn;
							copy($_FILES['file1']['tmp_name'], $realpath01.$dirname.$filenamestr1);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr1 , $realpath01.$dirname.'m_'.$filenamestr1,160,160);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr1 , $realpath01.$dirname.'s_'.$filenamestr1,70,70);
					if($upfile1 != ''){
					  @unlink($realpath01.$dirname.$upfile1);
					  //@unlink($realpath01.$dirname.'m_'.$upfile1);
					  //@unlink($realpath01.$dirname.'s_'.$upfile1);
				  }
			}
			if (is_uploaded_file($_FILES['file2']['tmp_name'])){
				$filename = basename($_FILES['file2']['name']);
				$tempsubn = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$rndstr = create_rndid(32).'_'.$NOW;
				$filenamestr2 = '2_'.$rndstr.'.'.$tempsubn;
							copy($_FILES['file2']['tmp_name'], $realpath01.$dirname.$filenamestr2);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr2 , $realpath01.$dirname.'m_'.$filenamestr2,160,160);
							//ImageCopyResizedTrue($realpath01.$dirname.$filenamestr2 , $realpath01.$dirname.'s_'.$filenamestr2,70,70);
					if($upfile2 != ''){
					  @unlink($realpath01.$dirname.$upfile2);
					  //@unlink($realpath01.$dirname.'m_'.$upfile2);
					  //@unlink($realpath01.$dirname.'s_'.$upfile2);
				  }
			}
		$strSQL1  ="update ".$table_name." set ";
		$strSQL1 .="sortid='". $sortid ."',";
		$strSQL1 .="lv01_type='". $lv01_type ."',";
		$strSQL1 .="lv02_type='". $lv02_type ."',";
		$strSQL1 .="title01='". $title01 ."',";
		$strSQL1 .="content01='". $content01 ."',";
		$strSQL1 .="content02='". $content02 ."',";
		$strSQL1 .="weblink='". $weblink ."',";
		$strSQL1 .="weblink_target='". $weblink_target ."',";
		if($filenamestr1 != ""){
			$strSQL1 .="upfile1='". $filenamestr1 ."',";
			$strSQL1 .="upfile1_type='". $_FILES['file1']['type'] ."',";
		}
		if($filenamestr2 != ""){
			$strSQL1 .="upfile2='". $filenamestr2 ."',";
			$strSQL1 .="upfile2_type='". $_FILES['file2']['type'] ."',";
	  } 
	  $strSQL1 .="upfile1_wh='". $upfile1_wh ."',";
	  $strSQL1 .="upfile2_wh='". $upfile2_wh ."',";
	  ($news_date != "") ? $strSQL1 .="news_date='". $news_date ."'," : $strSQL1 .="news_date=NULL,";
	  ($news_end_date != "") ? $strSQL1 .="news_end_date='". $news_end_date ."'," : $strSQL1 .="news_end_date=NULL,";
		$strSQL1 .="topup = '". $topup ."',";
		$strSQL1 .="visible = '". $visible ."',";
		$strSQL1 .="member_area = '". $_SESSION['session_user_lv01_name'] ."',";
		$strSQL1 .="member_id = '". $_SESSION['session_user_name'] ."',";
		$strSQL1 .="update_ipaddress='". $remote_ipaddress ."', ";
		$strSQL1 .="update_date='". $sys_datetime ."' ";
		$strSQL1 .=" where serialid= '". $pid ."' ";
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
		  $strSQL1="select * from ".$table_name." where serialid = '". $pid ."' ";
		  //echo $strSQL1.'<br />';
	  	$res = SqlQuery1($strSQL1);
	  	$dirname = $res->fields['serialid'].'/';
	  	$upfile1 = $res->fields['upfile1'];
	  	$upfile2 = $res->fields['upfile2'];
	  	if($upfile1 != ''){
	  	  @unlink($realpath01.$dirname.$upfile1);
	  	  //@unlink($realpath01.$dirname.'m_'.$upfile1);
		    //@unlink($realpath01.$dirname.'s_'.$upfile1);
		  }
	  	if($upfile2 != ''){
	  	  @unlink($realpath01.$dirname.$upfile2);
	  	  //@unlink($realpath01.$dirname.'m_'.$upfile2);
		    //@unlink($realpath01.$dirname.'s_'.$upfile2);
		  }
			$strSQL1="delete from ".$table_name." where serialid = '". $pid ."' ";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);

			//$strSQL1="delete from ".$table_name."_filelist where lv01_id = '". $pid ."' ";
			//echo $strSQL1.'<br />';
			//$result = SqlQuery1($strSQL1);

    //確認目錄存在
        if(is_dir($realpath01.$dirname)){
          rrmdir($realpath01.$dirname);
        }
    //確認目錄存在

		}
		redirect($tpl_name.'_mang.php?page='.$page.'&sel00_type='.$sel00_type);
	break;

	case "picdel":
		if($owner_array01[2] == '1'){
		  $strSQL1="select * from ".$table_name." where serialid= '". $pid ."' ";
		  //echo $strSQL1.'<br />';
	  	$res = SqlQuery1($strSQL1);
	  	$dirname = $res->fields['serialid'].'/';
	  	$upfile1 = $res->fields['upfile1'];
	  	$upfile2 = $res->fields['upfile2'];
		  @unlink($realpath01.$dirname.$$filesel);
		  //@unlink($realpath01.$dirname."m_".$$filesel);
		  //@unlink($realpath01.$dirname."s_".$$filesel);
		//echo $realpath . $$filesel.'<br />';
		$strSQL1="update ".$table_name." set ".$filesel." = NULL where serialid = '". $pid ."' ";
		//echo $strSQL1.'<br />';
		$result = SqlQuery1($strSQL1);
		}
		redirect($tpl_name.'_edit.php?page='.$page.'&sel00_type='.$sel00_type.'&pid='.$pid);
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

	case 'mupdbth01':
		if($owner_array01[2] == '1'){
		//取回更新陣列
			$batchranvar = '';
				if(isset($_POST['serialid'])) {
					for($i=0;$i<count($_POST['serialid']);$i++) {
						//echo $_REQUEST['list1'][$i].'_list<br />';
						if($_POST['serialid'][$i] != '' && $_POST['sortid'][$i] != ''){
							$strSQL1  ="update ".$table_name."_filelist set ";
							if($_POST['sortid'][$i] != "")
							  $strSQL1 .="sortid = '". $_POST['sortid'][$i] ."',";
							if($_POST['upfile1_title01'][$i] != "")
							  $strSQL1 .="upfile1_title01 = '". $_POST['upfile1_title01'][$i] ."',";							
							//if($_POST['upfile1_base'][$i] != "")
							//  $strSQL1 .="upfile1_base = '". $_POST['upfile1_base'][$i] ."',";
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
		redirect($tpl_name.'_subfile.php?page='.$page.'&sel00_type='.$sel00_type.'&pid='.$pid);
	break;

	case "mupd01":
		if($owner_array01[2] == '1'){
		  $xtemppath01 = 'muliti_upload/server/php/';

		  $strSQL1="select * from ".$table_name." where serialid= '". $pid ."' ";
		  //echo $strSQL1.'<br />';
	  	$res = SqlQuery1($strSQL1);
	  	$dirname = $res->fields['serialid'].'/';

		//確認最後排序
		  $strSQL1="select MAX(sortid) as sortid from ".$table_name."_filelist where lv01_id = '".$pid."' and member_id= '". $_SESSION['session_user_name'] ."' ";
		  //echo $strSQL1.'<br />';
		  $res = SqlQuery1($strSQL1);
		  $lastid = $res->fields['sortid'];
		//確認最後排序

		  $strSQL1="select * from xtemp_muplist where member_id= '". $_SESSION['session_user_name'] ."' ";
		  //echo $strSQL1.'<br />';
	  	$res = SqlQuery1($strSQL1);
		  $i=$lastid;
	  	while(!$res->EOF){
	  		$i++;
	  		copy($xtemppath01.'files/'.$res->fields['upfile1_name'], $realpath01.$dirname.$res->fields['upfile1_name']);
	  		@unlink($xtemppath01.'files/'.$res->fields['upfile1_name']);
	  		@unlink($xtemppath01.'thumbnails/'.$res->fields['upfile1_name']);

				//確認最後單號
				$rndstr = create_rndid(32);
				$nowdate = date('ymd',$NOW);
				//$strSQL1="select MAX(SUBSTRING(serialid,-4)) as MAXID from ".$table_name."_filelist where SUBSTRING(serialid,1,7) = 'F".$nowdate."' ";
				$strSQL1="select MAX(SUBSTRING( serialid,8,4)) as MAXID from ".$table_name."_filelist where SUBSTRING(serialid,1,7) = 'F".$nowdate."' ";
				//echo $strSQL1.'<br />';
				$res_getid = SqlQuery1($strSQL1);
				$nextval = $res_getid->fields['MAXID'] + 1;
				$nextval_id = 'F'.$nowdate.sprintf('%04s',$nextval).substr($rndstr,-4);
				//確認最後單號

				//過濾副檔名
				$filename = basename($res->fields['upfile1_base']);
				$tempsubn = strtolower(substr($filename, strrpos($filename, '.') + 1));
				$filenamestr1 = str_replace('.'.$tempsubn,'',$filename);
				//過濾副檔名

				$strSQL1  ="insert into ".$table_name."_filelist(serialid,lv00_type,lv01_id,sortid,upfile1_title01,upfile1_base,upfile1_name,upfile1_type,upfile1_size,member_area,member_id,add_date) values(";
				$strSQL1 .="'". $nextval_id ."',";
				$strSQL1 .="'". $lv00_type ."',";
				$strSQL1 .="'". $pid ."',";
				$strSQL1 .="'". $i ."',";
				$strSQL1 .="'". $filenamestr1 ."',";
				$strSQL1 .="'". $res->fields['upfile1_base'] ."',";
				$strSQL1 .="'". $res->fields['upfile1_name'] ."',";
				$strSQL1 .="'". $res->fields['upfile1_type'] ."',";
				$strSQL1 .="'". $res->fields['upfile1_size'] ."',";
				$strSQL1 .="'". $res->fields['member_area'] ."',";
				$strSQL1 .="'". $res->fields['member_id'] ."',";
				$strSQL1 .="'". $res->fields['add_date'] ."')";
				//echo $strSQL1.'<br />';
				$result = SqlQuery1($strSQL1);
				//file_put_contents('./001.txt',$strSQL1."\r\n",FILE_APPEND);

		  $res->MoveNext();
		  }

			$strSQL1="delete from xtemp_muplist where member_id= '". $_SESSION['session_user_name'] ."' ";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
			//file_put_contents('./001.txt',$strSQL1."\r\n",FILE_APPEND);
		  
		}
		//redirect($tpl_name.'_subfile.php?page='.$page.'&sel00_type='.$sel00_type.'&pid='.$pid);
	break;

	case 'mupd01_del01_real':
		if($owner_array01[3] == '1'){
		  $strSQL1="select * from ".$table_name."_filelist where serialid = '". $pid ."' ";
		  //echo $strSQL1.'<br />';
	  	$res = SqlQuery1($strSQL1);
	  	$dirname = $res->fields['lv01_id'].'/';
	  	$upfile1 = $res->fields['upfile1_name'];
	  	if($upfile1 != ''){
	  	  @unlink($realpath01.$dirname.$upfile1);
		  }
			$strSQL1="delete from ".$table_name."_filelist where serialid = '". $pid ."' ";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
		}
		//redirect($tpl_name.'_subfile.php?page='.$page.'&sel00_type='.$sel00_type.'&pid='.$mpid);
	break;

}
?>