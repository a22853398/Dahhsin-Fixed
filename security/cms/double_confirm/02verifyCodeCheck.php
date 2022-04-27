<?php
include('../commonfile/config_mang.inc.php');
include("../03lineNotify.php");

//抓AJAX傳過來的變數，主要是送了幾次request和傳送過來的驗證碼
$count = strip_input('input,cut2',$_GET['count']);
$onecode = strip_input('input,cut6',$_GET['onecode']);//要用GET才抓的到，用POST抓不到，因為直接把變數寫在網址裡面
$mbr_id = strip_input('input,cut50',$_GET['id']);

//驗證google驗證碼區塊
require_once("PHPGangsta/GoogleAuthenticator.php");
$ga = new PHPGangsta_GoogleAuthenticator();
$strSql_getLoginSecret = "SELECT AES_DECRYPT(temp_secret, '".$AESKEY01.'x'.$AESKEY02."') AS login_secret FROM web_account WHERE mbr_id ='".$mbr_id."'";
$res_getLoginSecret = SqlQuery1($strSql_getLoginSecret);
$secret = $res_getLoginSecret->fields["login_secret"];
######這邊改驗證碼可以接收的時間######
$checkCodeResult = $ga->verifyCode($secret, $onecode, 16);//secret, code, 1 crew = 30 seconds，返回true/false
######上面改驗證碼可以接收的時間######
$checkCodeResult_str = $checkCodeResult ? "Y" : "N";
//不管驗證結果如何，全部都寫到資料庫LOG
$strSql_verifyLog = "INSERT INTO 00verifyCodeLog 
                    (mbr_id, add_date, add_ipaddress, google_secret, verify_code, result, count)
                    VALUES( '".$mbr_id."','".$sys_datetime."', '".$remote_ipaddress."', '".$secret."', '".$onecode."', '".$checkCodeResult_str."', ".intval($count).")";
$config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction                    
$res_verifyLog = SqlQuery1($strSql_verifyLog);
$config['sql_link1']->CompleteTrans();//關閉transaction


if($checkCodeResult){
    echo true;
}else{
    //如果驗證碼打錯超過5次，帳號密碼hashed
    if(intval($count) > 5){
        $id = $mbr_id;
        $loginSecret = $secret;
        $loginVerifyCode = $onecode;
        $lock_shuffle = str_shuffle("01234");
        $token = "cXybzDPuXH09qrx1cqq8SAQQnC1w7QedTEe9kJSRHz2";//後台登入紀錄的LINE TOKEN
        post_message_curl_token("後台登入雙重驗證碼錯誤超過5次".
                                "\nRequest帳號：".$id.
                                "\nRequest次數：".$count.
                                "\nSecret：".$loginSecret.
                                "\nVerifyCode：".$loginVerifyCode.
                                "\n帳號密碼加密處理：".$lock_shuffle.
                                "\n帳號密碼即將鎖定", $token);
        
        //$lock_query = "id=".$id."&email=".$email."&count=".$count."&shuffle".$lock_shuffle."&secret=".$loginSecret."&verify=".$loginVerifyCode;
        //$lock_array = array("id"=>$id, "email"=>$email, "count"=>$count, "shuffle"=>$lock_shuffle, "secret"=>$loginSecret, "verify"=>$loginVerifyCode);
        
        $strSql_oldInfo = "SELECT *, AES_DECRYPT( mbr_pass, '".$AESKEY01.'x'.$AESKEY02."') AS decode_pass FROM web_account WHERE mbr_id='".$id."'";
        $res_oldInfo = SqlQuery1($strSql_oldInfo);
        $oldAccount = $res_oldInfo->fields["mbr_id"];
        $oldPassword = $res_oldInfo->fields["decode_pass"];
        
        $array_shuffle = str_split($lock_shuffle);//依字元切割字串
        $shuffledString = "";
        for($i=0; $i<count($array_shuffle); $i++){
            switch($array_shuffle[$i]){
                case '0':
                    $shuffledString .= $oldAccount;
                    break;
                case '1':
                    $shuffledString .= $oldPassword;
                    break;
                case '2':
                    $shuffledString .= $loginSecret;
                    break;
                case '3':
                    $shuffledString .= $loginVerifyCode;
                    break;
                case '4':
                    $shuffledString .= $token;
                    break;
            }
        }
        $lock_hash = base64_encode($shuffledString);
        $newAccount = substr($lock_hash, 0, 20);//擷取20字長度，HTML可輸入最長限制，從第幾位開始，取多長
        $newPassword = substr($lock_hash, 20, 20);//擷取20字長度
        $lock_strSQL = "UPDATE web_account 
                        SET lock_shuffle = '".$lock_shuffle."',
                            lock_secret = AES_ENCRYPT( '".$loginSecret."', '".$AESKEY01.'x'.$AESKEY02."'),
                            lock_onecode = AES_ENCRYPT( '".$loginVerifyCode."', '".$AESKEY01.'x'.$AESKEY02."'),
                            mbr_id = '".$newAccount."',
                            mbr_pass = AES_ENCRYPT( '".$newPassword."', '".$AESKEY01.'x'.$AESKEY02."')
                        WHERE mbr_id = '".$id."'    
                        ";
        $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
        $res_lockAccount = SqlQuery1($lock_strSQL);
        $config['sql_link1']->CompleteTrans();//關閉transaction
        post_message_curl_token("帳密變更結果".
                                "\n舊帳號：".$oldAccount.
                                "\n舊密碼：".$oldPassword.
                                "\n新帳號：".$newAccount.
                                "\n新密碼：".$newPassword
                                , $token);
    }

    echo false;
}

?>