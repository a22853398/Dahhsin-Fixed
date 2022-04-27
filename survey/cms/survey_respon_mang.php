<?php
include '../commonfile/config_mang.inc.php';
include $config['templatebpath'] . 'class.TemplatePower.inc.php';
include '../commonfile/pageft_mang.inc.php';
include '00security_function.php';
$tpl_str       = '問卷回覆';
$tpl_name      = 'survey_respon';
$table_name    = 'survey_respon';
$owner_type    = 'mang';
$owner_array01 = owner_check01($table_name);

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);
$search_key1 = strip_input('inputadm',$_REQUEST['search_key1']);
$search_orderdate1   = strip_input('inputadm', $_REQUEST['search_orderdate1']);
$search_orderdate2   = strip_input('inputadm', $_REQUEST['search_orderdate2']);
$search_check_status = strip_input('inputadm', $_REQUEST['search_check_status']);

if($sel00_type == ''){
    $sel00_type = 'A1';
}

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

//輸出分類
$tpl->gotoBlock('_ROOT');
$strSQL1="select lv00_type as list_id, title as list_title from survey_article where visible = 'Y' order by start_date ";
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

//Tab UI選單顏色
$tpl->gotoBlock('_ROOT');
$tpl->assign('sel00_type'.$sel00_type, 'id="tab_active"' );
$tpl->assign('lv00_current'.$sel00_type, 'id="tab_current"' );
//Tab UI選單顏色

//基本變數
$tpl->assign('sel00_type', $sel00_type );
if($sel00_type == 'S1'){
    $tpl->newBlock('block_row01_row');
    $tpl->gotoBlock('_ROOT');
}
$tpl->assign('search_key1', $search_key1 );
$tpl->assign('search_orderdate1', $search_orderdate1);
$tpl->assign('search_orderdate2', $search_orderdate2);
//基本變數

//資料列表
$tpl->gotoBlock('_ROOT');
switch ($sel00_type){    
	case 'S1':
	$searchstr = "";
	if($search_key1 != ""){
	    $searchstr .= "OR mbr_name like '%".$search_key1."%' OR mbr_id like '%".$search_key1."%' OR responid like '%".$search_key1."%'";
	}
	if($searchstr != ""){
		$searchstr = "and ( ".substr($searchstr,2,strlen($searchstr))." ) ";
	}
	if($search_orderdate1 !=""){
	    $searchstr .= "AND (add_date >= '".$search_orderdate1."' AND add_date <= '".date("Y-m-d", strtotime($search_orderdate2." +1 day"))."') ";
	}
	if($search_check_status !=""){
	    $searchstr .= "AND check_status = '".$search_check_status."'";
	}
  	$strSQL1="select serialid,responid,lv00_type,mbr_id,mbr_name,add_date,add_ipaddress,add_from from ".$table_name." WHERE 1=1 ".$searchstr." AND lv00_type IN (SELECT lv00_type FROM survey_article WHERE visible = 'Y') order by serialid desc ";
  	$strSQL2="select COUNT(serialid) from ".$table_name." WHERE 1=1 ".$searchstr." AND lv00_type IN (SELECT lv00_type FROM survey_article WHERE visible = 'Y')";
	break;

	case 'X1':
  	$strSQL1="select serialid,responid,lv00_type,mbr_id,mbr_name,add_date,add_ipaddress,add_from from ".$table_name." WHERE lv00_type IN (SELECT lv00_type FROM survey_article WHERE visible = 'N') order by serialid desc ";
  	$strSQL2="select COUNT(serialid) from ".$table_name." WHERE lv00_type IN (SELECT lv00_type FROM survey_article WHERE visible = 'N') ";
	break;

	case 'A1':
  	$strSQL1="select serialid,responid,lv00_type,mbr_id,mbr_name,add_date,add_ipaddress,add_from from ".$table_name." WHERE lv00_type IN (SELECT lv00_type FROM survey_article WHERE visible = 'Y') order by serialid desc ";
  	$strSQL2="select COUNT(serialid) from ".$table_name." WHERE lv00_type IN (SELECT lv00_type FROM survey_article WHERE visible = 'Y') ";
	break;

	default:
  	$strSQL1="select serialid,responid,lv00_type,mbr_id,mbr_name,add_date,add_ipaddress,add_from from ".$table_name." WHERE lv00_type ='".$sel00_type."' order by serialid desc ";
  	$strSQL2="select COUNT(serialid) from ".$table_name." WHERE lv00_type ='".$sel00_type."'";
	break;
}


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

    //權限及啟用狀態決定要顯示的小圖示
	if($sel00_type == 'X1'){
	    if($owner_array01[3] == '1'){
	        //$tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del_real" title="刪除" alt="刪除" />');
	    }
		if($owner_array01[2] == '1'){
		    //$tpl->assign('bt_undel', '<img src="js/icon/accept.png" class="bt_undel" title="啟用" alt="啟用" />');
		}
	}else{
	    if($owner_array01[2] == '1'){
	        //$tpl->assign('bt_undel', '<img src="js/icon/delete.png" class="bt_del" title="停用" alt="停用" />' );
	    }
	}
	$tpl->assign('bt_edit', '<img src="js/icon/page_edit.png" class="bt_edit" title="檢視" alt="檢視" />' );

    //取欄位資料
    $serialid = $res->fields['serialid'];
    $tpl->assign('serialid', $serialid);
    $responid = $res->fields['responid'];
    $tpl->assign('responid', $responid);
    $lv00_type = $res->fields['lv00_type'];
    $tpl->assign("lv00_type", $lv00_type);
    $lv00_type_str = SqlQuery1("SELECT title FROM survey_article WHERE lv00_type = '".$lv00_type."'")->fields["title"];
	$tpl->assign("lv00_type_str", $lv00_type_str);
	$mbr_id = $res->fields['mbr_id'];
	$tpl->assign("mbr_id", $mbr_id);
	$mbr_name = $res->fields['mbr_name'];
	$tpl->assign("mbr_name", $mbr_name);
	$add_date = $res->fields['add_date'];
	$tpl->assign("add_date", $add_date);
	$add_ipaddress = $res->fields['add_ipaddress'];
	$add_from = $res->fields['add_from'];
	$tpl->assign("add_from", $add_from.", ".$add_ipaddress);
	$check_status = $res->fields['check_status'];
	if($check_status === "Y"){
	    $check_status = "已分析";
	}else if($check_status === "N"){
	    $check_status = "未分析";
	}else if($check_status === ""){
	    $check_status = "新着";
	}
	$tpl->assign("check_status", $check_status);
    
    $res->MoveNext();
}

$tpl->printToScreen();
?>
