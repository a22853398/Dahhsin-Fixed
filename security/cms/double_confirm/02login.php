<?php
include('../commonfile/config_mang.inc.php');
include_once($config['templatebpath'].'class.TemplatePower.inc.php');
include("../03googleRecaptchaVerify.php");
include("../03lineNotify.php");

//初始化物件
$tpl = new TemplatePower( './02login.htm' );
$tpl->prepare();
$tpl->assign('main_webname', $config['main_webname'] );

//帳號密碼和單位
$pUserUnit = strip_input('input,cut50',$_POST['pUserUnit']);
$pUserID = strip_input('input,cut30',$_POST['pUserID']);
$pPassword = strip_input('input,cut30',$_POST['pPassword']);
$pUserUnit = htmlspecialchars(chop($pUserUnit));
$pUserID = htmlspecialchars(chop($pUserID));
$pPassword = htmlspecialchars(chop($pPassword));


$googleObject = new GoogleRecaptcha();
$response = $_POST["g-recaptcha-response"];
$token = "cXybzDPuXH09qrx1cqq8SAQQnC1w7QedTEe9kJSRHz2";
if($googleObject->googleVerify($response) === true){
    post_message_token("\n狀態：第一階段認證成功\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress", $token);
}else{
    post_message_token("\n狀態：第一階段認證失敗\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress", $token);
    errback("我不是機器人認證失敗");
    exit();    
}


//核對帳號密碼，如果成功給他一個secret
$strSQL1="select e.* from web_account as e where e.mbr_pass = AES_ENCRYPT('".$pPassword."','".$AESKEY01.'x'.$AESKEY02."') and e.mbr_lv01 = '". $pUserUnit ."' and e.mbr_id = '". $pUserID ."' ";
$res = SqlQuery1($strSQL1);
if ($res->RecordCount() == 1){
    $mbr_id = $res->fields["mbr_id"];
    $mbr_email = $res->fields["mbr_email"];
    $mbr_unit = $res->fields["mbr_lv01"];
    $mbr_unit2 = $res->fields["mbr_lv02"];
    $mbr_pass = $pPassword;
    $tpl->assign('mbr_id', $mbr_id);
    $tpl->assign('mbr_email', $mbr_email);
    $tpl->assign('mbr_unit', $mbr_unit);
    
    //判斷上一次進入到頁面時間，要不要給他一個secret
    $strSql_checkLogin = "SELECT temp_login_date FROM web_account WHERE mbr_id = '".$mbr_id."'";
    $res_checkLogin = SqlQuery1($strSql_checkLogin);
    $last_login = $res_checkLogin->fields["temp_login_date"];
    
    //現在系統時間和上一次登入時間差300秒的話
    if((strtotime($sys_datetime) - strtotime($last_login)) > 300){
        //記錄新的登入時間
        //產生Secret和寫入資料庫
        require_once("PHPGangsta/GoogleAuthenticator.php");
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();//金鑰
        $tpl->assign('secret', $secret);
        $strSql_insertSecret = "UPDATE web_account SET temp_secret = AES_ENCRYPT('".$secret."', '".$AESKEY01.'x'.$AESKEY02."'), 
                                        temp_login_date = '".$sys_datetime."'
                                        WHERE mbr_id = '".$mbr_id."'";
        $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
        $res_insertSecret = SqlQuery1($strSql_insertSecret);
        $config['sql_link1']->CompleteTrans();//關閉transaction
        //次數丟到HTML
        $sendCount = 0;
        $tpl->assign('sendCount', $sendCount);
    }else{//如果沒有超過300秒，沿用上一個secret並抓送信紀錄的同一個secret的數量，count也沿用
        $strSql_oldSecret = "SELECT *,AES_DECRYPT( temp_secret, '".$AESKEY01.'x'.$AESKEY02."') AS old_secret FROM web_account WHERE mbr_id = '".$mbr_id."'";
        $oldSecret = SqlQuery1($strSql_oldSecret)->fields["old_secret"];
        $strSql_checkSendMailCount = "SELECT * FROM 00verifyEmailLog 
                                        WHERE secret = AES_ENCRYPT( '".$oldSecret."', '".$AESKEY01.'x'.$AESKEY02."') 
                                        AND request_email = '".$mbr_email."'";
        $res_checkSendMailCount = SqlQuery1($strSql_checkSendMailCount);
        //次數丟到HTML
        $sendCount = intval($res_checkSendMailCount->RecordCount());
        $tpl->assign('sendCount', $sendCount);
    }
    
    //插入資料庫LOG
    $strSql_insertLog = "INSERT INTO web_account_history (mbr_id,mbr_lv01,mbr_lv02,add_date,add_ipaddress,logincheck,google_secret) 
                        VALUES ('".$mbr_id."','".$mbr_unit."','".$mbr_unit2."','".$sys_datetime."','".$remote_ipaddress."','NOTYET',AES_ENCRYPT('".$secret."', '".$AESKEY01.'x'.$AESKEY02."') )
                        ";
    $config['sql_link1']->StartTrans();
    $res_insertLog = SqlQuery1($strSql_insertLog);
    $config['sql_link1']->CompleteTrans();
}else{
    //post_message_token("\n狀態：登入失敗+認證成功\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress"."\n安全ID：".$_SESSION['session_security_id'], $token);
    errback("帳號密碼錯誤");
    exit();
}

//印到螢幕
$tpl->printToScreen();
?>