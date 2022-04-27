<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('00security_function.php');
$tpl_str = '常見問題分類';
$tpl_name = 'faq_article01_lv01';
$table_name = 'faq_article01_lv01';
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
$lv00_type = $sel00_type;
//$lv00_type = strip_input('inputadm',$_REQUEST['lv00_type']);
//if($lv00_type == 'S1' || $lv00_type == 'X1' || $lv00_type == '')
//  $lv00_type = '1';

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
  $tpl->assign('lv00_type', $lv00_type );

$tpl->gotoBlock('_ROOT');
//輸出最後排序
	  $strSQL1="select MAX(CAST(sortid AS SIGNED)) as sortid from ".$table_name." where lv00_type = ". $lv00_type ." ";
	  //echo $strSQL1.'<br />';
  	$res = SqlQuery1($strSQL1);
  	$tpl->assign('sortid', ($res->fields['sortid']+1) );
//輸出最後排序

$tpl->gotoBlock('_ROOT');
//輸出分類
  $strSQL1="select '1' as list_id, '常見問題分類' as list_title ";  
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  while(!$res->EOF){
		$tpl->newBlock('lv00_type_row');
		if($res->fields['list_id'] == $lv00_type){
			$tpl->assign('list_sel', 'selected="selected"' );
		}
		$tpl->assign('list_id', $res->fields['list_id'] );
		$tpl->assign('list_title', $res->fields['list_title'] );
  $res->MoveNext();
  }
//輸出分類

  $tpl->printToScreen();
?>