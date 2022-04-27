<?php
include('commonfile/config.inc.php');
require("commonfile/phpmailer/PHPMailerAutoload.php");
include('commonfile/authimg/authimg.inc.php');
include('commonfile/reject_otherurl_get.php');
include('commonfile/mail_errlog.inc.php');

include '03lineNotify.php';
include '03googleRecaptchaVerify.php';

$error_msgtype1 = "couponAdd_failed";
$response = $_POST["g-recaptcha-response"];//取得回傳到這邊的檔案
//先檢驗Google Recaptcha 有沒有過
$googleObject = new GoogleRecaptcha;
$token = "XXXXXXXXXXXXXXXXXXXX";
$pEmail = $_SESSION['session_webmbr_login_id'];
$linemsg = "\n來源：".$_SERVER["HTTP_REFERER"]."\n名：".$pUserID."\n信：".$pEmail."\nIP：".$remote_ipaddress;
if($googleObject->googleVerify($response) === true){
    //post_message_token("\n驗證成功".$linemsg, $token);
}else{
    //post_message_token("\n驗證失敗".$linemsg, $token);
    redirect($config['mainurl'] . '/msg_view01.php?sendmsg=' . $error_msgtype1);
    exit();
}


$today = date("Y-m-d");//今天，用再篩選有效coupon
$array_couponID = $_POST['choseCoupon'];//array
$mbr_id = $_SESSION['session_webmbr_login_id'];
$currentTime = date("Y-m-d H:i:s");


/* 現行 Coupon */
$sqlStr_couponActive = "SELECT serialid, title01, dis_amt, news_date, news_end_date, visible 
            FROM coupon_event01 
            WHERE news_date <='".$today."' 
            AND news_end_date >= '".$today."'
            AND visible = 'Y' ";
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

/*產生序號*/
function getSerialNumber(){
    $rndstr = create_rndid(32);
    $nowdate = date("ymd");
    $strSQL1 = "select MAX(SUBSTRING( serialid,8,4)) as MAXID from coupon_event01_sublist01 where SUBSTRING(serialid,1,7) = 'F" . $nowdate . "' ";
    $res_getid  = SqlQuery1($strSQL1);
    $nextval = $res_getid->fields['MAXID'] + 1;
    $nextval_id = 'F' . $nowdate . sprintf('%04s', $nextval) . substr($rndstr, -4);
    return $nextval_id;
}

/*SQL基礎句*/
function getSqlInsert($serialid, $lv00_type, $lv01_id, $sortid, $var01, $var02, $coupon, $member_area, $member_id, $add_date){
    $strSql_insert = "INSERT INTO coupon_event01_sublist01
                (serialid, lv00_type, lv01_id, sortid, var01, var02, coupon, member_area, member_id, add_date)
                VALUE ('".$serialid."', '".$lv00_type."', '".$lv01_id."', '".$sortid."', '".$var01."', '".$var02."', '".$coupon."', '".$member_area."','".$member_id."','".$add_date."')
                ; ";    
    return $strSql_insert;
}

/*插入執行區*/                
for($i=0; $i<count($array_couponID); $i++){
    $serialid = getSerialNumber();
    $lv00_type = "COUPON";
    $lv01_id = $array_couponID[$i];
    $sortid = $i;
    $var01 = $mbr_id;
    $var02 = '';
    $coupon = strtoupper(substr(getSerialNumber(), -6));
    $member_area = $remote_ipaddress;
    $member_id = "自行索取";
    $add_date = $currentTime;
    
    $sqlStr_exe =  getSqlInsert($serialid, $lv00_type, $lv01_id, $sortid, $var01, $var02, $coupon, $member_area, $member_id, $add_date);
    $res_exe = SqlQuery1($sqlStr_exe);
}




redirect($config['mainurl'] . '/memberCoupon.php');  
?>