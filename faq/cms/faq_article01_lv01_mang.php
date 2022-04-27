<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('../commonfile/pageft_mang.inc.php');
include('00security_function.php');
$tpl_str = '常見問題分類';
$tpl_name = 'faq_article01_lv01';
$table_name = 'faq_article01_lv01';
$owner_type = 'mang';
$owner_array01 = owner_check01($table_name);

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
if($sel00_type == ''){
  $sel00_type = '1';
}
$search_key1 = strip_input('inputadm',$_REQUEST['search_key1']);

  $tpl = new TemplatePower( './'.$tpl_name.'_mang.htm' );
  $tpl->assignInclude( 'external', './00external.php' );
  $tpl->assignInclude( 'mainMenu', './00mainMenu.php' );
  $tpl->prepare();

  $tpl->assignglobal('tpl_str', $tpl_str );
  $tpl->assignglobal('tpl_name', $tpl_name );

if($owner_array01[1] == '1'){
	$tpl->newBlock('tablist01_add');
	$tpl->gotoBlock('_ROOT');
}
if($owner_array01[2] == '1'){
	$tpl->newBlock('tablist01_batch');
	$tpl->gotoBlock('_ROOT');
}

$tpl->gotoBlock('_ROOT');
//輸出分類
  $strSQL1="select '1' as list_id, '常見問題分類' as list_title";  
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  while(!$res->EOF){
		$tpl->newBlock('tablist01_row');
		if($sel00_type == ''){
		  $sel00_type = $res->fields['list_id'];
		}
		if($res->fields['list_id'] == $sel00_type){
			$tpl->assign('sel00_type_sel', 'id="tab_active"' );
			$tpl->assign('lv00_current_sel', 'id="tab_current"' );
		}
		$tpl->assign('list_id', $res->fields['list_id'] );
		$tpl->assign('list_title', $res->fields['list_title'] );
  $res->MoveNext();
  }
//輸出分類

$tpl->gotoBlock('_ROOT');
//Tab UI選單顏色
  $tpl->assign('sel00_type'.$sel00_type, 'id="tab_active"' );
  if($sel00_type == 'S1'){
    $tpl->newBlock('block_row01_row');
    $tpl->gotoBlock('_ROOT');
  }
  $tpl->assign('lv00_current'.$sel00_type, 'id="tab_current"' );
//Tab UI選單顏色

//基本變數
  $tpl->assign('sel00_type', $sel00_type );
  $tpl->assign('search_key1', $search_key1 );
//基本變數

//資料列表
$tpl->gotoBlock('_ROOT');
switch ($sel00_type){
	case 'S1':
	$searchstr = '';
	if($search_key1 != '')
		$searchstr .= "OR title01 like '%".$search_key1."%' ";
	if($searchstr != ''){
		$searchstr = "and ( ".substr($searchstr,2,strlen($searchstr))." ) ";
	}
  	$strSQL1="select * from ".$table_name." where visible = 'Y' ".$searchstr." order by sortid ";
  	$strSQL2="select COUNT(serialid) from ".$table_name." where visible = 'Y' ".$searchstr."  ";
	break;

	case 'X1':
  	$strSQL1="select * from ".$table_name." where visible = 'N' order by sortid";
  	$strSQL2="select COUNT(serialid) from ".$table_name." where visible = 'N' ";
	break;

	default:
	$strSQL1="select * from ".$table_name." where visible = 'Y' order by sortid";
	$strSQL2="select COUNT(serialid) from ".$table_name." where visible = 'Y' ";
	break;
}
  //echo $strSQL1.'<br />';

  $total = GetQueryValueCount1($strSQL2,'value');
  pageft($total,$page_splt01);
  $res = SqlQueryLimit1($strSQL1,$displaypg,$firstcount);

  $tpl->assign('pagenav', $pagenav );
  $tpl->assign('pagejump01', $pagejump01 );
  $tpl->assign('page_splt01_add', ($page_splt01*($page-1)) );
  $tpl->assign('page_splt01', $page_splt01 );
  $tpl->assign('page', $page );
	$i=0;
  while(!$res->EOF){
			$i++;
			$tpl->newBlock('Table1_row');
			$tpl->assign('rowid01ck', ($i % 2 == 1) ? 'class="odd"' : '' );

			//if($owner_array01[2] == '1'){
			 if($sel00_type == 'X1'){
			 	if($owner_array01[3] == '1')
			 	  $tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del_real" title="刪除" alt="刪除" />');
			 	if($owner_array01[2] == '1')
			    $tpl->assign('bt_undel', '<img src="js/icon/accept.png" class="bt_undel" title="啟用" alt="啟用" />');
			 }else{
			  if($owner_array01[2] == '1')
			    $tpl->assign('bt_undel', '<img src="js/icon/delete.png" class="bt_del" title="停用" alt="停用" />' );
			 }
			    $tpl->assign('bt_edit', '<img src="js/icon/page_edit.png" class="bt_edit" title="檢視" alt="檢視" />' );
		  //}

			$tpl->assign('serialid', $res->fields['serialid'] );
			$tpl->assign("serialidbt", '<input type="hidden" name="serialid[]" value="'.$res->fields['serialid'].'" />' );
			//$tpl->assign('sortid', $res->fields['sortid'] );
			$tpl->assign("sortid", '<input type="text" size="3" name="sortid[]" value="'.$res->fields['sortid'].'" title="'.$res->fields['sortid'].'" />' );
			$tpl->assign('title01', $res->fields['title01'] );
			//$tpl->assign('title02', $res->fields['title02'] );
			$tpl->assign('title02', $res->fields['title02'].' <a href="#" class="colorvw" style="background: '.$res->fields['title02'].';" title="'.$res->fields['title02'].'">&nbsp;&nbsp;&nbsp;&nbsp;</a>' );
			$tpl->assign("update_date", $res->fields['update_date'] );
			$tpl->assign('member_str01', $res->fields['member_area'].'/'.$res->fields['member_id'] );

  $res->MoveNext();
  }

  $tpl->printToScreen();
?>