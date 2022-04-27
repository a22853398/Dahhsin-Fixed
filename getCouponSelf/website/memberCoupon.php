<?php
include('commonfile/config.inc.php');

  //$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );  
  $tpl->assign('mainurl', $config['mainurl'] );
  $tpl->assign('rewrite_url', $config['rewrite_url'] );

    /* 沒登入的話轉向首頁，保險用 */
    /*if (isset($_SESSION['session_webmbr_login_id']) && $_SESSION['session_webmbr_login_id'] != ''){
        
    }else{
        redirect($config['mainurl']);
        exit();
    }
    */
    //改成不管怎麼樣都可以進到頁面，但是只會看到按鈕
    $bool_loginStatus = (isset($_SESSION['session_webmbr_login_id']) && $_SESSION['session_webmbr_login_id'] != '');
    $tpl->assign("loginStatus", $bool_loginStatus);
    /* 沒有登入狀態的話，顯示按鈕請客人登入，登入的話取資料 */
    if($bool_loginStatus == false){
        $login_button = "<style>
                            .surveyLogin{
                                text-align: center;
                                font-size: 1.3rem;
                                font-weight: bold;
                            }
                            .surveyLogin button{
                                font-size: 1.3rem;
                                font-weight: bold;
                                background: #E64784;
                                color: white;
                                border: none;
                                padding: 1% 3%;
                                margin: 2% 0%;
                                cursor: pointer;
                                border-radius: 0.5vw;
                            }
                        </style>
                        <div class='surveyLogin'>
                            領取優惠之前，請您先<br>
                            <button class='loginbt01'>會員登入</button>
                        </div>
                        ";
        $tpl->assign('login_button', $login_button);
    }
    
$mbr_id = $_SESSION['session_webmbr_login_id'];//帳號
$today = date("Y-m-d");//今天日期
$tpl->assign("today",$today);

/*
* 大概邏輯： memberCoupon.php 
1. 找出帳號裡已有 & 可以使用的
2. 找出現在進行的，和已有比對沒有的，另存成另一陣列
3. 沒有的變成領取優惠
4. 送出資料到 memberCoupon_update.php，寫入資料庫
5. redirect 到這裡，所以又會執行撈一次全部的coupon & 篩選可以用的
6. 畫出畫面
*
*/

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
                                order_list.process_type AS order_process_type
                                FROM coupon_event01_sublist01
                                LEFT JOIN coupon_event01 ON coupon_event01_sublist01.lv01_id = coupon_event01.serialid
                                LEFT JOIN order_list ON  coupon_event01_sublist01.var02 = order_list.orderid
                                WHERE var01 = '".$mbr_id."'
                                ORDER BY coupon_end_date, serialid
                            ";
$res_couponAccountHave = SqlQuery1($sqlStr_couponAccountHave);
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
                "coupon_id" => $coupon_id
            )
    );
    $res_couponAccountHave->MoveNext();
}
$tpl->assign("account_coupon", $array_couponAccountHave);


/* 現行 Coupon */
$sqlStr_couponActive = "SELECT serialid, title01, dis_amt, news_date, news_end_date, visible 
            FROM coupon_event01 
            WHERE news_date <='".$today."' 
            AND news_end_date >= '".$today."'
            AND visible = 'Y' 
            AND lv00_type = '1'";
$res_couponActive = SqlQuery1($sqlStr_couponActive);
$array_couponActive = array();
while(!$res_couponActive->EOF){
    $activeCouponTitle = $res_couponActive->fields["title01"];
    $activeCouponId = $res_couponActive->fields["serialid"];
    $activeCouponStart = $res_couponActive->fields["news_date"];
    $activeCouponEnd = $res_couponActive->fields["news_end_date"];
    $activeCouponDisAmount = $res_couponActive->fields["dis_amt"];
    $activeCouponMinAmount = (intval($activeCouponDisAmount) > 500)? $activeCouponDisAmount : "500";
    array_push($array_couponActive, array(
                "active_coupon_id" => $activeCouponId,
                "active_coupon_title" => $activeCouponTitle,
                "active_coupon_start" => $activeCouponStart,
                "active_coupon_end" => $activeCouponEnd,
                "active_coupon_disAmount" => $activeCouponDisAmount,
                "active_coupon_minAmount" => $activeCouponMinAmount
            )
    );
    $res_couponActive->MoveNext();
}
$tpl->assign("couponActive", $array_couponActive);


/* 帳號可以選的 現行coupon */
$array_alreadyHaveCoupons = array();//帳號裡已有的
$array_notHaveCoupons = array();//帳號裡沒有的
for($i=0; $i<count($array_couponActive); $i++){
    $has_same = 0;
    for($j=0; $j<count($array_couponAccountHave); $j++){
        if($array_couponActive[$i]["active_coupon_id"] === $array_couponAccountHave[$j]["coupon_id"]){
            $has_same = 1;
            array_push($array_alreadyHaveCoupons, $array_couponActive[$i]);
        }
    }
    if($has_same === 0){
        array_push($array_notHaveCoupons, $array_couponActive[$i]);
    }else{
        $has_same = 1;
    }
}

$tpl->assign("ableAdd_activeCoupon", $array_notHaveCoupons);
$tpl->assign("already_haveCoupon",$array_alreadyHaveCoupons);


/* 已經擁有的+尚未使用的+可以使用的 */
$sqlStr_ableUseCoupon = " SELECT 
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
                            order_list.process_type AS order_process_type
                            FROM coupon_event01_sublist01
                            LEFT JOIN coupon_event01 ON coupon_event01_sublist01.lv01_id = coupon_event01.serialid
                            LEFT JOIN order_list ON  coupon_event01_sublist01.var02 = order_list.orderid
                            WHERE var01 = '".$mbr_id."'
                            AND coupon_event01_sublist01.var02 = ''
                            AND (coupon_event01_sublist01.news_end_date >='".$today."' OR coupon_event01.news_end_date >='".$today."')
                            AND coupon_event01.visible = 'Y'
                            ORDER BY coupon_end_date, serialid
                        ";
//$res_ableUseCoupon = SqlQuery1($sqlStr_ableUseCoupon);

//分頁處理
include('commonfile/pageft.inc.php');
###計算SQL結果###
$sqlRes_count = "SELECT COUNT(*) FROM (".$sqlStr_ableUseCoupon.") AS tmp";
$int_sqlRes_count = GetQueryValueCount1($sqlRes_count ,"value");
###計算SQL結果END###
$page = strip_input('inputadm',$_REQUEST['page']);            
$page_splt01 = 8;
pageft($int_sqlRes_count, $page_splt01);
$res_ableUseCoupon = SqlQueryLimit1($sqlStr_ableUseCoupon, $displaypg, $firstcount);//取結果，執行SQL ，這個是會分頁的取SQL結果
$tpl->assign('pagenav', $pagenav );
$tpl->assign('pagejump01', $pagejump01 );
$tpl->assign('page_splt01_add', ($page_splt01*($page-1)) );
$tpl->assign('page_splt01', $page_splt01 );
$tpl->assign('page', $page );
//////分頁處理END//////


$array_ableUseCoupon = array();
while(!$res_ableUseCoupon->EOF){
    $coupon_id = $res_ableUseCoupon->fields["lv01_id"];
    $coupon_title = $res_ableUseCoupon->fields["coupon_title"];
    $coupon_code = $res_ableUseCoupon->fields["coupon_code"];
    $coupon_end_date = ($res_ableUseCoupon->fields["coupon_end_date"] === "" || $res_ableUseCoupon->fields["coupon_end_date"] === null)
                        ? $res_ableUseCoupon->fields["news_end_date"] : $res_ableUseCoupon->fields["coupon_end_date"];
    $add_time = ($res_ableUseCoupon->fields["add_time"] === "" || $res_ableUseCoupon->fields["add_time"] === null) 
                ? $res_ableUseCoupon->fields["news_date"] : $res_ableUseCoupon->fields["add_time"];
    $discount_amount = $res_ableUseCoupon->fields["discount_amount"];
    $min_amount = ($res_ableUseCoupon->fields["discount_amount"] >= intval(500))? $res_ableUseCoupon->fields["discount_amount"] : "500";
    
    array_push($array_ableUseCoupon, array(
                    "able_coupon_id" => $coupon_id,
                    "able_coupon_title" => $coupon_title,
                    "able_coupon_code" => $coupon_code,
                    "able_coupon_end" => $coupon_end_date,
                    "able_coupon_start" => $add_time,
                    "able_coupon_disamt" => $discount_amount,
                    "able_coupon_minamt" => $min_amount,
                )
    );
    $res_ableUseCoupon->MoveNext();
}
$tpl->assign("able_coupon",$array_ableUseCoupon);

$tpl->display('memberCoupon.shtm');      
?>
