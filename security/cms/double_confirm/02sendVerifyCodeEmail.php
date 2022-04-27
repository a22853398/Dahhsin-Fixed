<?php
require_once("../commonfile/config_mang.inc.php");
include_once("../03lineNotify.php");

function createSecret(){
    //產生Google Secret
    require_once("PHPGangsta/GoogleAuthenticator.php");
    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = $ga->createSecret();//金鑰
    return $secret;
}
function getOneCode($secret){
    //依Secret產生Google驗證碼
    require_once("PHPGangsta/GoogleAuthenticator.php");
    $ga = new PHPGangsta_GoogleAuthenticator();
    $oneCode = $ga->getCode($secret);//驗證碼
    return $oneCode;
}

function sendMail($email, $accountName, $secret, $oneCode){
    //require_once("PHPGangsta/GoogleAuthenticator.php");
    //$ga = new PHPGangsta_GoogleAuthenticator();
    //$secret = $ga->createSecret();//金鑰
    //$oneCode = $ga->getCode($secret);//驗證碼
    
    //寄信
    header('Content-Type: text/html; charset=utf-8');
    //error_reporting(E_ALL);
    ini_set('display_errors','On');
    require_once("../commonfile/phpmailer/PHPMailerAutoload.php");
    $mail = new PHPMailer();
    $mail->Encoding = '8bit';
    $mail->CharSet = 'utf-8';
    $mail->SMTPDebug = false;
    $mail->IsSMTP();    // send via SMTP
    $mail->Host     = "smtp.exmail.qq.com"; // SMTP servers
    $mail->Port = 465;
    $mail->SMTPSecure = "ssl";
    $mail->SMTPAuth = true;     // turn on SMTP authentication
    $mail->Username = "service@dahhsin.com.tw";  // SMTP username
    $mail->Password = "bill19530710"; // SMTP password
    $mail->From     = "service@dahhsin.com.tw";
    $mail->FromName = "大新書局";
    $mail->AddAddress($email, $accountName);
    $mail->AddReplyTo("service@dahhsin.com.tw","service@dahhsin.com.tw");
    $mail->WordWrap = 50;                              // set word wrap
    $mail->IsHTML(true);                               // send as HTML
    $mail->Subject  =  "【大新書局】大新書局後台登入驗證信";
    $mail->Body     =  "<h1>大新書局後台登入雙重驗證信件</h1><br>
                        <p>登入帳號：<font color='red'><strong>".$accountName."</strong></font></p>
                        <h2>6位數字驗證碼</h2><p><span style='font-size:32px; font-weight:bold; color:red; background:yellow;'>".$oneCode."</span></p>
                        <br>
                        <br>
                        <p>金鑰和驗證碼有效時間八分鐘，八分鐘內沒有登入的話，上述驗證碼作廢</p>
                        <br>==========================
                        <p>此信件為系統自動發信，直接回覆此信件恕無法回覆。</p>
                        <p>若有後台系統登入問題，請洽(02)2707-3232分機245或217，或寄信至 it@dahhsin.com.tw</p>
                        <br>==========================
                        <p>提醒您，開啟信件時，不要亂點信件內的連結、或下載附件。</p>
                        <p>木馬和病毒，會在你的電腦安裝後門，駭客隨時都可以連上你的電腦</p>
                        <p>個資外洩人人有責，中毒的手機傳LINE訊息也會被駭客竊取對話紀錄！</p>
                        <p>個資法法院判決企業要負七成責任，且依洩漏筆數還會再開罰！</p>
                        <p>個資法企業賠償法案研讀：https://www.ithome.com.tw/news/133473</p>
                        ";
    $mail->AltBody  =  "16位英數字金鑰：".$secret."／6位數字驗證碼：".$oneCode;

    if(!$mail->Send())
    {
        //echo "Message was not sent01 <p>";
        //echo "Mailer Error: " . $mail->ErrorInfo;
        //exit;
    }else{
        //echo "Message has been sent OK1";
    }
}

//抓帳號和信箱，重新寄信，主要執行程式的地方
$url_query = $_SERVER["QUERY_STRING"];//email=u9908028@dahhsin.com.tw&id=u9908028_weihung&count=6

if($url_query != ""){
    $array_query = explode("&", $url_query);
    $email = str_replace( "email=", "", $array_query[0]);
    $id = str_replace( "id=", "", $array_query[1]);
    $count = str_replace( "count=", "", $array_query[2]);
    //$loginSecret = SqlQuery1("SELECT AES_DECRYPT(temp_secret, '".$AESKEY01.'x'.$AESKEY02."') FROM web_account WHERE mbr_id = '".$id."'")->fields["temp_secret"];
    $loginSecret = "SELECT AES_DECRYPT(temp_secret, '".$AESKEY01.'x'.$AESKEY02."') AS login_secret FROM web_account WHERE mbr_id = '".$id."'";//每次登入會更新secret的SQL句
    $res_loginSecret = SqlQuery1($loginSecret)->fields["login_secret"];//取得登入的secret
    $loginVerifyCode = getOneCode($res_loginSecret);
    sendMail($email, $id, $res_loginSecret, $loginVerifyCode);
    //送信紀錄寫到資料庫，secret和驗證碼要加密
    $strSql = "INSERT INTO 
                00verifyEmailLog (request_account, request_ipaddress, request_count, request_email, add_date, secret, one_code) 
                VALUES( '".$id."', '".$remote_ipaddress."', ".$count.", '".$email."', '".date("Y-m-d H:i:s")."' , 
                    AES_ENCRYPT('".$res_loginSecret."', '".$AESKEY01.'x'.$AESKEY02."'), 
                    AES_ENCRYPT('".$loginVerifyCode."', '".$AESKEY01.'x'.$AESKEY02."'))";
    $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
    $res = SqlQuery1($strSql);
    $config['sql_link1']->CompleteTrans();//關閉transaction
    
    if($count > 5){
        $lock_shuffle = str_shuffle("01234");
        $token = "cXybzDPuXH09qrx1cqq8SAQQnC1w7QedTEe9kJSRHz2";//後台登入紀錄的LINE TOKEN
        post_message_curl_token("後台登入雙重驗證信重送超過5次".
                                "\nRequest帳號：".$id.
                                "\nRequest信箱：".$email.
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
}

?>