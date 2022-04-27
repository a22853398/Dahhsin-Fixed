<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('00security_function.php');
$tpl_str = '郵件內容';
$tpl_name = 'mailtous_type01';
$table_name = 'mailtous_type01';
$owner_type = 'edit';
$owner_array01 = owner_check01($table_name);
$realpath01 = '../uploadfile/file'.$tpl_name.'/';
$dirname = '';

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);

 $pid=init_var($pid,0);
 if($pid == 0){
   errback('無此分類');
   exit;
 }

  $tpl = new TemplatePower( './'.$tpl_name.'_edit.htm' );
  $tpl->assignInclude( 'external', './00external.php' );
  $tpl->assignInclude( 'mainMenu', './00mainMenu.php' );
  $tpl->prepare();

  $tpl->assignglobal('tpl_str', $tpl_str );
  $tpl->assignglobal('tpl_name', $tpl_name );
  $tpl->assignglobal('WYSIWYG_path', $config['WYSIWYG_path'] );
  $tpl->assign('pid', $pid );
  $tpl->assign('page', $page );
  $tpl->assign('sel00_type', $sel00_type );

if($owner_array01[2] == '1'){
	$tpl->newBlock('tablist01_doPost');
	$tpl->gotoBlock('_ROOT');
}

//輸出資料
  $strSQL1="select * from ".$table_name." where serialid = ". $pid ;
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);

  $tpl->assign('lv00_type', $res->fields['lv00_type'] );
    $lv00_type = $res->fields['lv00_type'];
  
  $tpl->assign('sortid', $res->fields['sortid'] );
  $tpl->assign('lv_type', $res->fields['lv_type'] );
  $tpl->assign('parent_id', $res->fields['parent_id'] );
  $tpl->assign('title01', $res->fields['title01'] );
  $tpl->assign('value01', str_replace($res->fields['parent_id']."_", "", $res->fields['value01']) );
  $tpl->assign('email01', $res->fields['email01'] );
  $tpl->assign('email02', $res->fields['email02'] );
  $tpl->assign('content01', $res->fields['content01'] );
  $tpl->assign('visible', $res->fields['visible'] );
  
  $tpl->assign('member_area', $res->fields['member_area'] );
  $tpl->assign('member_id', $res->fields['member_id'] );
  $tpl->assign('update_date', $res->fields['update_date'] );

//輸出資料

$tpl->gotoBlock('_ROOT');
//輸出分類
  $strSQL1="select '1' as list_id, '寄信給我們' as list_title ";  
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

$tpl->gotoBlock('_ROOT');
//輸出父子
$strSqlSelect = "SELECT * FROM mailtous_type01 WHERE parent_id='0' ORDER BY sortid";
$resSelect = SqlQuery1($strSqlSelect);
$strOptions = "<option value='0'>0</option>";
while(!$resSelect->EOF){
    $strOptions .= "<option value='".$resSelect->fields["value01"]."'>".$resSelect->fields["title01"]."</option>";
    $resSelect->MoveNext();
}
$tpl->assign('q_type01', $strOptions);
//輸出父子end

  $tpl->printToScreen();
?>