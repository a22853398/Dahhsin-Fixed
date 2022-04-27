<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('../commonfile/str_function.inc.php');
include('00security_function.php');
$tpl_str = '常見問題';
$tpl_name = 'faq_article01';
$table_name = 'faq_article01';
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

//輸出分類
  $strSQL1="select serialid as list_id, title01 as list_title from faq_article01_lv01 where visible = 'Y' order by sortid ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  $list_title = $res->fields['list_title'];
//輸出分類

//輸出資料
$tpl->gotoBlock("_ROOT");
  $strSQL1="select * from ".$table_name." where serialid = '". $pid ."' ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  $dirname = $res->fields['serialid'].'/';

  $tpl->assign('lv01_type', $res->fields['lv01_type'] );
    $lv01_type = $res->fields['lv01_type'];
  $tpl->assign('lv02_type', $res->fields['lv02_type'] );
    $lv02_type = $res->fields['lv02_type'];

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

$tpl->gotoBlock("_ROOT");
  $tpl->assign('sortid', $res->fields['sortid'] );
  $tpl->assign('title01', $res->fields['title01'] );
  $tpl->assign('content_row.content01', $res->fields['content01'] );
  $tpl->assign('content_row.content02', $res->fields['content02'] );
  $tpl->assign('addlink_row.weblink', $res->fields['weblink'] );
  $tpl->assign('addlink_row.weblink_target'.$res->fields['weblink_target'], 'selected="selected"' );
  $tpl->assign('visible_row.topup'.$res->fields['topup'], 'selected="selected"' );
  $tpl->assign('visible_row.visible'.$res->fields['visible'], 'selected="selected"' );
  $tpl->assign('member_area', $res->fields['member_area'] );
  $tpl->assign('member_id', $res->fields['member_id'] );
  $tpl->assign('update_date', $res->fields['update_date'] );
  $tpl->assign('daterange_row.news_date', date('Y-m-d',strtotime($res->fields['news_date'])) );
  if($res->fields['news_end_date'] != '')
  $tpl->assign('daterange_row.news_end_date', date('Y-m-d',strtotime($res->fields['news_end_date'])) );

$tpl->gotoBlock('addpic_row');
  $tpl->assign('upfile1_wh', $res->fields['upfile1_wh'] );
  $filename = $res->fields['upfile1'];
	if( $filename != ''){
		if (IsAllowedExt(strtolower(substr($filename, strrpos($filename, '.') + 1)),'Image') ){
  		$tpl->assign('upfile1', '<input type="button" value="檢視圖片" class="btfilevwhs" src01="'.$realpath01.$dirname.$filename.'" /> <input type="button" value="刪除圖片" class="btfiledel" src01="upfile1" /><br /><img src="'.$realpath01.$dirname.$filename.'" alt="" title="" >' );
	  }else{
  		$tpl->assign('upfile1', '<input type="button" value="檢視檔案" class="btfilevw01" src01="'.$realpath01.$dirname.$filename.'" /> <input type="button" value="刪除檔案" class="btfiledel" src01="upfile1" />' );
		}
	}

$tpl->gotoBlock('pic2_row');
  $tpl->assign('upfile2_wh', $res->fields['upfile2_wh'] );
  $filename = $res->fields['upfile2'];
	if( $filename != ''){
		if (IsAllowedExt(strtolower(substr($filename, strrpos($filename, '.') + 1)),'Image') ){
  		$tpl->assign('upfile2', '<input type="button" value="檢視圖片" class="btfilevwhs" src01="'.$realpath01.$dirname.$filename.'" /> <input type="button" value="刪除圖片" class="btfiledel" src01="upfile2" /><br /><img src="'.$realpath01.$dirname.$filename.'" alt="" title="" >' );
	  }else{
  		$tpl->assign('upfile2', '<input type="button" value="檢視檔案" class="btfilevw01" src01="'.$realpath01.$dirname.$filename.'" /> <input type="button" value="刪除檔案" class="btfiledel" src01="upfile2" />' );
		}
	}
//輸出資料

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
