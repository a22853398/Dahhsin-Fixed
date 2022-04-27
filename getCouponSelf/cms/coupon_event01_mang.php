<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('../commonfile/pageft_mang.inc.php');
include('00security_function.php');
$tpl_str = 'Coupon';
$tpl_name = 'coupon_event01';
$table_name = 'coupon_event01';
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
  $strSQL1="select '1' as list_id, 'Coupon' as list_title";  
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
  } else if ($sel00_type=='X2') {
	$tpl->newBlock('tablist02_row');
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
  	$strSQL1="select * from ".$table_name." where 1=1 ".$searchstr." order by sortid desc";
  	$strSQL2="select COUNT(serialid) from ".$table_name." where 1=1 ".$searchstr."  ";
	break;

	case 'X1':
  	$strSQL1="select * from ".$table_name." where visible = 'N' order by sortid desc";
  	$strSQL2="select COUNT(serialid) from ".$table_name." where visible = 'N' ";
  	//$strSQL1="select * from ".$table_name." where lv00_type='1' and visible = 'N' order by sortid desc";
  	//$strSQL2="select COUNT(serialid) from ".$table_name." where lv00_type='1' and visible = 'N' ";
	break;
	
	case 'X2':
		$search_key1 = strip_input('inputadm',$_REQUEST['search_key1']);
		$search_orderid = strip_input('inputadm',$_REQUEST['search_orderid']);
		$search_mbrid = strip_input('inputadm',$_REQUEST['search_mbrid']);
		$search_visible = strip_input('inputadm',$_REQUEST['search_visible']);
		$search_date1 = strip_input('inputadm',$_REQUEST['search_date1']);
		$search_date2 = strip_input('inputadm',$_REQUEST['search_date2']);
		$tpl->assignglobal('search_key1', $search_key1 );
		$tpl->assignglobal('search_orderid', $search_orderid );
		$tpl->assignglobal('search_mbrid', $search_mbrid );
		$tpl->assignglobal('search_visible'.$search_visible ,"selected");
		$tpl->assignglobal('search_date1', $search_date1 );
		$tpl->assignglobal('search_date2', $search_date2 );
		
		$searchstr = "";
		if($search_key1 != "") {
			$searchstr .= "OR a.title01 like '%".$search_key1."%' OR b.coupon like '%".$search_key1."%' ";
			if($searchstr != ""){
				$searchstr = "and ( ".substr($searchstr,2,strlen($searchstr))." ) ";
			}
		}
		if($search_orderid != "") {
			$searchstr .= " and b.var02 like '%".$search_orderid."%' ";
		}
		if($search_mbrid != "") {
			$searchstr .= " and b.var01 like '%".$search_mbrid."%' ";
		}
		if($search_visible == "Y") {
			$searchstr .= " and b.visible = '".$search_visible."' ";
			if($search_date1 != "") $searchstr .= " and b.ord_date >= '".$search_date1."' ";
			if($search_date2 != "")	$searchstr .= " and b.ord_date <= '".$search_date2."' ";
		} else if($search_visible == "N") {
			$searchstr .= " and (b.visible = '' OR b.visible is null) ";
			if($search_date1 != "") $searchstr .= " and b.add_date >= '".$search_date1."' ";
			if($search_date2 != "")	$searchstr .= " and b.add_date <= '".$search_date2."' ";		
		} else if($search_visible == "") {
			if($search_date1 != "") $searchstr .= " and b.add_date >= '".$search_date1."' ";
			if($search_date2 != "")	$searchstr .= " and b.add_date <= '".$search_date2."' ";
		}

	
		$strSQL1="select * from (select '1' lvtype,a.serialid,a.title01,a.dis_amt,b.coupon,b.visible,b.var01,b.var02,b.news_date,b.news_end_date,b.add_date,b.ord_date from coupon_event01 a, coupon_event01_sublist01 b
				where a.serialid=b.lv01_id and a.serialid='ADD' and a.visible='Y' ".$searchstr." 
			union all 
			select '2' lvtype,a.serialid,a.title01,a.dis_amt,b.coupon,b.visible,b.var01,b.var02,a.news_date,a.news_end_date,b.add_date,b.ord_date from coupon_event01 a, coupon_event01_sublist01 b
				where a.serialid=b.lv01_id and a.serialid!='ADD' and a.visible='Y' ".$searchstr." 
			) list where 1=1 order by 1,2";
			
  	
		$strSQL2="select count(*) from (select '1' lvtype,a.serialid,a.title01,a.dis_amt,b.coupon,b.visible,b.var01,b.var02,b.news_date,b.news_end_date,b.add_date,b.ord_date from coupon_event01 a, coupon_event01_sublist01 b
				where a.serialid=b.lv01_id and a.serialid='ADD' and a.visible='Y' ".$searchstr." 
			union all 
			select '2' lvtype,a.serialid,a.title01,a.dis_amt,b.coupon,b.visible,b.var01,b.var02,a.news_date,a.news_end_date,b.add_date,b.ord_date from coupon_event01 a, coupon_event01_sublist01 b
				where a.serialid=b.lv01_id and a.serialid!='ADD' and a.visible='Y' ".$searchstr." 
			) list ";
	break;

	default:
	$strSQL1="select * from ".$table_name." where visible = 'Y' order by sortid desc";
	$strSQL2="select COUNT(serialid) from ".$table_name." where visible = 'Y' ";
	//$strSQL1="select * from ".$table_name." where lv00_type='1' and visible = 'Y' order by sortid desc";
	//$strSQL2="select COUNT(serialid) from ".$table_name." where lv00_type='1' and visible = 'Y' ";
	break;
}
  //echo $strSQL1.'<br />';

if ($sel00_type!='X2') {



	$total = GetQueryValueCount1($strSQL2,'value');
	pageft($total,$page_splt01);
	$res = SqlQueryLimit1($strSQL1,$displaypg,$firstcount);
	
	$tpl->assign('page_splt01_add', ($page_splt01*($page-1)) );
	$tpl->assign('page_splt01', $page_splt01 );
	
	$tpl->newBlock('panel1');
	$tpl->assign('pagenav', $pagenav );
	$tpl->assign('pagejump01', $pagejump01 );
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
		} else {
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
			
		//Coupon 總數
		$strSQL1="select lv01_id from coupon_event01_sublist01 where lv01_id='". $res->fields['serialid']."'";
		$result1 = SqlQuery1($strSQL1);
		//Coupon 總數
		$tpl->assign('total', $result1->RecordCount() );
		//Coupon 已使用數
		$strSQL1="select lv01_id from coupon_event01_sublist01 where visible='Y' and lv01_id='". $res->fields['serialid']."'";
		$result2 = SqlQuery1($strSQL1);
		//Coupon 已使用數
		$tpl->assign('used', $result2->RecordCount() );

		$tpl->assign('dis_amt', $res->fields['dis_amt'] );
		$tpl->assign("news_date", $res->fields['news_date'] );
		$tpl->assign("news_end_date", $res->fields['news_end_date'] );
		$tpl->assign("visible", $res->fields['visible'] );
		$tpl->assign("update_date", $res->fields['update_date'] );
		$tpl->assign('member_str01', $res->fields['member_area'].'/'.$res->fields['member_id'] );

		$res->MoveNext();
	}
} else if ($sel00_type=='X2') {

	$total = GetQueryValueCount1($strSQL2,'value');
	pageft($total,$page_splt01);
	$res = SqlQueryLimit1($strSQL1,$displaypg,$firstcount);
	
	$tpl->assign('page_splt01_add', ($page_splt01*($page-1)) );
	$tpl->assign('page_splt01', $page_splt01 );
	
	$tpl->newBlock('panel2');
	$tpl->assign('pagenav', $pagenav );
	$tpl->assign('pagejump01', $pagejump01 );
	$tpl->assign('page', $page );
	

	$i=0;
	while(!$res->EOF){
		$i++;
		$tpl->newBlock('Table2_row');
		$tpl->assign('rowid01ck', ($i % 2 == 1) ? 'class="odd"' : '' );	
		$tpl->assign('serialid', $res->fields['serialid'] );
		
		$tpl->assign('mbrid', $res->fields['var01'] );
		$tpl->assign('title01', $res->fields['title01'] );
		$tpl->assign('coupon', $res->fields['coupon'] );
		$tpl->assign('dis_amt', $res->fields['dis_amt'] );
		$tpl->assign("eff_date", $res->fields['news_date'] ."~". $res->fields['news_end_date'] );
		$tpl->assign("visible", ($res->fields['visible']=="Y") ? "<font color=blue>已使用</font>" : "<font color=red>未使用</font>" );
		$tpl->assign("orderid", ($res->fields['visible']=="Y" && $res->fields['var02']!="") ? $res->fields['var02'] : "" );		
		$res->MoveNext();
	}		
}
$tpl->printToScreen();
?>