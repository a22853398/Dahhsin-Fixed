<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('../commonfile/str_function.inc.php');
include('00security_function.php');
$tpl_str = '常見問題';
$tpl_name = 'faq_article01';
$table_name = 'faq_article01';
$owner_type = 'add';
$owner_array01 = owner_check01($table_name);
if($owner_array01[1] != '1'){
  redirect1('whiteboard01_mang.php');
  exit;
}

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
if($sel00_type == 'S1' || $sel00_type == 'X1' || $sel00_type == '')
  $sel00_type = '1';
$lv01_type = $sel00_type;
//$lv01_type = strip_input('inputadm',$_REQUEST['lv01_type']);
//if($lv01_type == 'S1' || $lv01_type == 'X1' || $lv01_type == '')
//  $lv01_type = '1';

  $tpl = new TemplatePower( './'.$tpl_name.'_add.htm' );
  $tpl->assignInclude( 'external', './00external.php' );
  $tpl->assignInclude( 'mainMenu', './00mainMenu.php' );
  $tpl->prepare();

  $tpl->assignglobal('tpl_str', $tpl_str );
  $tpl->assignglobal('tpl_name', $tpl_name );
  $tpl->assignglobal('WYSIWYG_path', $config['WYSIWYG_path'] );
  $tpl->assign('pid', $pid );
  $tpl->assign('page', $page );
  $tpl->assign('sel00_type', $sel00_type );

//輸出排序
  $strSQL1="select MAX(sortid) as sortid from ".$table_name." where lv01_type = '". $lv01_type ."' ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  $tpl->assign('sortid', ($res->fields['sortid']+1) );
//輸出排序

//輸出分類
  $strSQL1="select serialid as list_id, title01 as list_title from faq_article01_lv01 where visible = 'Y' order by sortid ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  $list_title = $res->fields['list_title'];
//輸出分類

if($sel00_type == 'NONE'){
	  $upfile1_wh = 'W135xH100';
	  //$upfile2_wh = 'W725xH255';
	$tpl->assign('picname', $list_title );
	$tpl->newBlock('addlink_row');
	$tpl->newBlock('daterange_row');
  $tpl->newBlock('visible_row');
  $tpl->newBlock('content_fck_row');
  $tpl->newBlock('content_row');
}else{
	  $upfile1_wh = 'W68xH64';
	  //$upfile2_wh = 'W68xH64';
	$tpl->assign('picname', $list_title );	
	$tpl->newBlock('addlink_row');
	//$tpl->newBlock('addpic_row');
	//$tpl->assign('upfile1_wh', $upfile1_wh );
	//$tpl->assign('picsize1', ' (尺寸 '.$upfile1_wh.' px)' );
	//$tpl->assign('picsize1name', '廣告小圖' );
  //$tpl->newBlock('pic2_row');
  //$tpl->assign('upfile2_wh', $upfile1_wh );
	//$tpl->assign('picsize2', ' (尺寸 '.$upfile2_wh.' px)' );
	//$tpl->assign('picsize2name', '廣告大圖' );
	$tpl->newBlock('daterange_row');
	$tpl->newBlock('visible_row');
  $tpl->newBlock('content_fck_row');
  $tpl->newBlock('content_row');
}

$tpl->gotoBlock('daterange_row');
  $tpl->assign("news_date", date("Y-m-d") );
  $tpl->assign("news_end_date", date("Y-m-d",dateadd("m",1,date("Y-m-d"))) );

$tpl->gotoBlock('_ROOT');
//輸出分類
  $strSQL1="select serialid as list_id, title01 as list_title from faq_article01_lv01 where visible = 'Y' order by sortid ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  while(!$res->EOF){
		$tpl->newBlock('lv01_type_row');
		if($res->fields['list_id'] == $lv01_type){
			$tpl->assign('list_sel', 'selected="selected"' );
		}
		$tpl->assign('list_id', $res->fields['list_id'] );
		$tpl->assign('list_title', $res->fields['list_title'] );
  $res->MoveNext();
  }
//輸出分類

  $tpl->printToScreen();
?>