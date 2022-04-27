<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('00security_function.php');
$tpl_str = 'Coupon';
$tpl_name = 'coupon_event01';
$table_name = 'coupon_event01';
$owner_type = 'edit';
$owner_array01 = owner_check01($table_name);
$realpath01 = '../uploadfile/file'.$tpl_name.'/';
$dirname = '';


$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);

//確認傳入參數
 //$pid=init_var($pid,0);
 if(strlen($pid) != 15){
   errback("本筆資料不存在");
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
/*
 	$upfile1_wh = 'W125xH180';
	$tpl->newBlock('addpic_row');
	$tpl->assign('picsize1', ' (尺寸 '.$upfile1_wh.' px)' );
	$tpl->assign('picsize1name', '搭配商品圖' ); 
	$tpl->newBlock('content_row');	
*/
	$tpl->gotoBlock("_ROOT");
//輸出資料
  $strSQL1="select * from ".$table_name." where serialid = '".$pid."' " ;
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  $dirname = $res->fields['serialid'].'/';
  $tpl->assign('lv00_type', $res->fields['lv00_type'] );
    $lv00_type = $res->fields['lv00_type'];
  $tpl->assign('sortid', $res->fields['sortid'] );
  $tpl->assign('title01', $res->fields['title01'] );
  $tpl->assign('ord_limit', $res->fields['ord_limit'] );
  $tpl->assign('dis_amt', $res->fields['dis_amt'] );
  $tpl->assign('news_date', $res->fields['news_date'] );
  $tpl->assign('news_end_date', $res->fields['news_end_date'] );
  $tpl->assign('visible'.$res->fields['visible'], 'selected="selected"' );
  $tpl->assign('member_area', $res->fields['member_area'] );
  $tpl->assign('member_id', $res->fields['member_id'] );
  $tpl->assign('update_date', $res->fields['update_date'] );
//輸出資料

/*
$tpl->gotoBlock('addpic_row');
  $tpl->assign('addpic_row.upfile1_wh', $res->fields['upfile1_wh'] );
  $filename = $res->fields['upfile1'];
	if( $filename != ''){
		if (IsAllowedExt(strtolower(substr($filename, strrpos($filename, '.') + 1)),'Image') ){
		$filename = "125_".$filename;
  		$tpl->assign('addpic_row.upfile1', '<input type="button" value="檢視圖片" class="btfilevwhs" src01="'.$realpath01.$dirname.$filename.'" /> <input type="button" value="刪除圖片" class="btfiledel" src01="upfile1" /><br /><img src="'.$realpath01.$dirname.$filename.'" alt="" title="" >' );
		}else{
  		$tpl->assign('addpic_row.upfile1', '<input type="button" value="檢視檔案" class="btfilevw01" src01="'.$realpath01.$dirname.$filename.'" /> <input type="button" value="刪除檔案" class="btfiledel" src01="upfile1" />' );
		}
	}
$tpl->gotoBlock('content_row');	
  $tpl->assign('content_row.content01', $res->fields['content01'] ); 
*/
$tpl->gotoBlock('_ROOT');
//輸出分類
  //$strSQL1="select '1' as list_id, 'Coupon' as list_title ";  
  $strSQL1="select lv00_type as list_id, title01 as list_title FROM coupon_event01 GROUP BY lv00_type";
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