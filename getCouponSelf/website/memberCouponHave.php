<?php
include('commonfile/config.inc.php');

  //$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );  
  $tpl->assign('mainurl', $config['mainurl'] );
  $tpl->assign('rewrite_url', $config['rewrite_url'] );


    /* 沒登入的話轉向首頁，保險用 */
    if (isset($_SESSION['session_webmbr_login_id']) && $_SESSION['session_webmbr_login_id'] != ''){
        
    }else{
        redirect($config['mainurl']);
        exit();
    }
    
$mbr_id = $_SESSION['session_webmbr_login_id'];
$today = date("Y-m-d");
$tpl->assign("today",$today);

/* 帳號裡有的Coupon全部撈出來 */
$sqlStr_couponAccountHave = " SELECT 
                                coupon_event01_sublist01.serialid, 
                                coupon_event01_sublist01.lv01_id, 
                                coupon_event01_sublist01.sortid, 
                                coupon_event01_sublist01.var01, 
                                coupon_event01_sublist01.var02 AS used_orderid, 
                                coupon_event01_sublist01.coupon AS coupon_code, 
                                coupon_event01_sublist01.news_date, 
                                coupon_event01_sublist01.news_end_date,
                                DATE_FORMAT(coupon_event01_sublist01.add_date, '%Y-%m-%d') AS add_time,
                                coupon_event01.title01 AS coupon_title,
                                coupon_event01.news_end_date AS coupon_end_date,
                                coupon_event01.dis_amt AS discount_amount,
                                order_list.process_type AS order_process_type,
                                coupon_event01.visible AS coupon_active
                                FROM coupon_event01_sublist01
                                LEFT JOIN coupon_event01 ON coupon_event01_sublist01.lv01_id = coupon_event01.serialid
                                LEFT JOIN order_list ON  coupon_event01_sublist01.var02 = order_list.orderid
                                WHERE var01 = '".$mbr_id."'
                                ORDER BY coupon_end_date
                            ";
//$res_couponAccountHave = SqlQuery1($sqlStr_couponAccountHave);//這個是不會分頁的取SQL結果

//////分頁處理//////
include('commonfile/pageft.inc.php');
###計算SQL結果###
$sqlRes_count = "SELECT COUNT(*) FROM (".$sqlStr_couponAccountHave.") AS tmp";
$int_sqlRes_count = GetQueryValueCount1($sqlRes_count ,"value");
###計算SQL結果END###
$page = strip_input('inputadm',$_REQUEST['page']);            
$page_splt01 = 8;
pageft($int_sqlRes_count, $page_splt01);
$res_couponAccountHave = SqlQueryLimit1($sqlStr_couponAccountHave, $displaypg, $firstcount);//取結果，執行SQL ，這個是會分頁的取SQL結果
$tpl->assign('pagenav', $pagenav );
$tpl->assign('pagejump01', $pagejump01 );
$tpl->assign('page_splt01_add', ($page_splt01*($page-1)) );
$tpl->assign('page_splt01', $page_splt01 );
$tpl->assign('page', $page );
//////分頁處理END//////

$array_couponAccountHave = array();
$i=0;
while(!$res_couponAccountHave->EOF){
    $i++;
    $coupon_id = $res_couponAccountHave->fields["lv01_id"];
    $coupon_title = $res_couponAccountHave->fields["coupon_title"];
    $used_orderid = ($res_couponAccountHave->fields["used_orderid"] === "" || $res_couponAccountHave->fields["used_orderid"] === null) 
                    ? "尚未使用" : $res_couponAccountHave->fields["used_orderid"];
    $coupon_code = $res_couponAccountHave->fields["coupon_code"];
    $order_process_type = "";
    switch($res_couponAccountHave->fields["order_process_type"]){
                case "X1":
                    $order_process_type = "管理員取消";
                    break;
                case "X2":
                    $order_process_type = "使用者取消";
                    break;
                case "01":
                    $order_process_type = "新增";
                    break;
                case "03":
                    $order_process_type = "備貨";
                    break;
                case "04":
                    $order_process_type = "出貨";
                    break;
                case "F1":
                    $order_process_type = "完成";
                    break;
                case "":
                    $order_process_type = "尚未使用";
                    break;
                default:
                    $order_process_type = "其他";
                    break;    
            }
    $coupon_end_date = ($res_couponAccountHave->fields["coupon_end_date"] === "" || $res_couponAccountHave->fields["coupon_end_date"] === null)
                        ? $res_couponAccountHave->fields["news_end_date"] : $res_couponAccountHave->fields["coupon_end_date"];
      
    $add_time = ($res_couponAccountHave->fields["add_time"] === "" || $res_couponAccountHave->fields["add_time"] === null) 
                ? $res_couponAccountHave->fields["news_date"] : $res_couponAccountHave->fields["add_time"];
    $discount_amount = $res_couponAccountHave->fields["discount_amount"];
    $min_amount = ($res_couponAccountHave->fields["discount_amount"] >= intval(500))? $res_couponAccountHave->fields["discount_amount"] : "500";
    $coupon_active = ($res_couponAccountHave->fields["coupon_active"] === "Y")? true : false;
    if($coupon_active === false){ $coupon_title = "<del>".$coupon_title."</del>（活動已結束）";}
    array_push($array_couponAccountHave, array(
                "coupon_title" => $coupon_title,
                "used_orderid" => $used_orderid,
                "coupon_code" => $coupon_code,
                "order_process_type" => $order_process_type,
                "coupon_end_date" => $coupon_end_date,
                "add_time" => $add_time,
                "discount_amount" => $discount_amount,
                "min_amount" => $min_amount,
                "tr_color" => $i,
                "coupon_id" => $coupon_id,
                "coupon_active" => $coupon_active
            )
    );
    $res_couponAccountHave->MoveNext();
}
$tpl->assign("account_coupon", $array_couponAccountHave);




$tpl->display('memberCouponHave.shtm');      
?>
