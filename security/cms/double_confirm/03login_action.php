<?php
include('../commonfile/config_mang.inc.php');
include("../03googleRecaptchaVerify.php");
include("../03lineNotify.php");
require "../commonfile/phpmailer/PHPMailerAutoload.php";
include '../commonfile/mail_errlog.inc.php';
$token = "cXybzDPuXH09qrx1cqq8SAQQnC1w7QedTEe9kJSRHz2";

//帳號密碼和單位
$pUserUnit = strip_input('input,cut50',$_POST['pUserUnit']);
$pUserUnit = htmlspecialchars(chop($pUserUnit));
$pUserID = strip_input('input,cut30',$_POST['pUserID']);
$pUserID = htmlspecialchars(chop($pUserID));

//Google驗證碼
require_once("PHPGangsta/GoogleAuthenticator.php");
$ga = new PHPGangsta_GoogleAuthenticator();
$secret_sql = "SELECT AES_DECRYPT(temp_secret, '".$AESKEY01.'x'.$AESKEY02."') AS login_secret FROM web_account WHERE mbr_id = '".$pUserID."'";
$secret = SqlQuery1($secret_sql)->fields["login_secret"];
$oneCode = htmlspecialchars(($_POST["pCode"]));
######這邊改可以接收驗證碼的時間######
$checkCodeResult = $ga->verifyCode($secret, $oneCode, 16);//secret, code, 1 crew = 30 seconds，返回true/false
######上面是改Google驗證碼的時間的地方######
//echo "<br>Unit ".$pUserUnit."<br>ID ".$pUserID."<br>secret ".$secret."<br>oneCode ".$oneCode."<br>Check ".$checkCodeResult;

//先進行google驗證
if($checkCodeResult){
    //驗證單位和帳號
    $strSQL_checkUnitAndId = "SELECT *,AES_DECRYPT(temp_secret, '".$AESKEY01.'x'.$AESKEY02."') 
                                FROM web_account 
                                WHERE mbr_id = '".$pUserID."'
                                AND mbr_lv01 = '".$pUserUnit."'";
    $res_checkUnitAndId = SqlQuery1($strSQL_checkUnitAndId);
}else{
    //更新登入紀錄
    $strSQL_updateLoginLog = "UPDATE web_account_history 
                                SET add_date = '".$sys_datetime."', 
                                verify_code = AES_ENCRYPT('".$oneCode."','".$AESKEY01.'x'.$AESKEY02."'),
                                logincheck = 'VERIFYFAIL'
                                WHERE mbr_id = '".$pUserID."' 
                                AND google_secret = AES_ENCRYPT('".$secret."','".$AESKEY01.'x'.$AESKEY02."')";
    $result_updateLoginLog = SqlQuery1($strSQL_updateLoginLog);
    post_message_token("\n狀態：登入第一階段成功但第二階段失敗\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress"."\nSecret：".$secret."\nVerify：".$oneCode, $token);
    errback("Google驗證碼不正確");
    exit();
}


//驗證完之後，上面應該會取得驗證單位和帳號的結果數量，有matched的才給session並寫入資料庫LOG
if($res_checkUnitAndId->RecordCount() === 1){
    //更新登入日期
    $strSQL_updateLoginDate = "UPDATE web_account SET update_date = '".$sys_datetime."' , update_ipaddress = '".$remote_ipaddress."' WHERE mbr_id = '".$pUserID."'";
    $result_updateLoginDate = SqlQuery1($strSQL_updateLoginDate);
    //更新登入紀錄
    $strSQL_updateLoginLog = "UPDATE web_account_history 
                                SET add_date = '".$sys_datetime."', 
                                verify_code = AES_ENCRYPT('".$oneCode."','".$AESKEY01.'x'.$AESKEY02."'),
                                logincheck = 'OK'
                                WHERE mbr_id = '".$pUserID."' 
                                AND AES_DECRYPT( google_secret, '".$AESKEY01.'x'.$AESKEY02."') = '".$secret."'
                                AND logincheck = 'NOTYET'
                                ORDER BY add_date DESC
                                LIMIT 1
                                ";
    $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction                            
    $result_updateLoginLog = SqlQuery1($strSQL_updateLoginLog);
    $config['sql_link1']->CompleteTrans();//關閉transaction
    post_message_token("\n狀態：登入第一階段第二階段都成功\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress"."\nSecret：".$secret."\nVerify：".$oneCode, $token);
    //給SESSION
    $_SESSION['session_security_id'] = $res_checkUnitAndId->fields['serialid'];
	$_SESSION['session_user_id']     = $res_checkUnitAndId->fields['mbr_id'];
    $_SESSION['session_user_name']   = $res_checkUnitAndId->fields['mbr_name'];
    $_SESSION['session_user_lv01']   = $res_checkUnitAndId->fields['mbr_lv01'];
    $_SESSION['session_user_lv02']   = $res_checkUnitAndId->fields['mbr_lv02'];
    $strSQL1="select * from web_group_lv01 where serialid = '".$_SESSION['session_user_lv01']."'";
	$result = SqlQuery1($strSQL1);
	$_SESSION['session_user_lv01_name']= $result->fields['var01'];
	
	//寄登入通知信
	$mbr_email = $res_checkUnitAndId->fields["mbr_email"];
	$mbr_name = $res_checkUnitAndId->fields["mbr_name"];
	$mbr_id = $res_checkUnitAndId->fields["mbr_id"];
	if($mbr_email != ""){
	    //寄送信件設定
        $tmpsc1       = explode('/', $_SERVER['SCRIPT_NAME']);
        $tmpsc1_sub   = $tmpsc1[count($tmpsc1) - 1];
        $mail_msgtype = 'ord_add';
        //輸出系統發信設定
        $strSQL1_mail = "select sortid,var01,var02,var03 from xsys_defset where lv00_type = '02' order by sortid ";
        $res_mail = SqlQuery1($strSQL1_mail);
        while (!$res_mail->EOF) {
            $var01  = 'smtp_' . $res_mail->fields['var02'];
            $$var01 = $res_mail->fields['var03'];
            $res_mail->MoveNext();
        }
        //信件內容
        $mail_msgtype = "backmbr_login";//信件內容的編碼
        $strSQL1_mailContent = "select lv01_type,var01,var02,var03,var04,var05 from mail_content01 where lv01_type = '" . $mail_msgtype . "' ";
        $res_mailContent        = SqlQuery1($strSQL1_mailContent);
        $lv01_type  = $res_mailContent->fields['lv01_type'];
        $mail_var01 = $res_mailContent->fields['var01'];
        $mail_var02 = $res_mailContent->fields['var02'];
        $mail_var03 = $res_mailContent->fields['var03'];
        $mail_var04 = $res_mailContent->fields['var04'];
        $mail_var05 = $res_mailContent->fields['var05'];
        //替換掉文字，寫會員的姓名
        $mailtmp = $mail_var05;
        $mailtmp = str_replace('{mbr_id}', $mbr_id, $mailtmp);
        $mailtmp = str_replace('{mbr_name}', $mbr_name, $mailtmp);
        $mailtmp = str_replace('{mbr_email}', $mbr_email, $mailtmp);
        $mailtmp = str_replace('{ipaddress}', $remote_ipaddress, $mailtmp);//IP位址
        $mailtmp = str_replace('{login_time}', date("Y-m-d H:i:s"), $mailtmp);//登入時間
        //把東西裝到發信的函式區
        $mail = new PHPMailer();
        $mail->Encoding = '8bit';
        $mail->CharSet  = 'utf-8';
        $mail->IsSMTP();
        if ($smtp_SMTPSecure != '') {
            $mail->SMTPSecure = $smtp_SMTPSecure;
        }
        $mail->Host = $smtp_Host;
        if ($smtp_Port != '') {
            $mail->Port = $smtp_Port;
        }
        if ($smtp_SMTPAuth == 'true') {
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_Username;
            $mail->Password = $smtp_Password;
        }

        $mail->Host = $smtp_Host;
        if ($smtp_SMTPAuth == 'true') {
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_Username;
            $mail->Password = $smtp_Password;
        }

        $mail->From     = $smtp_Admmail;
        $mail->FromName = $mail_var01;

        $mail->AddAddress($mbr_email);
        $mail->AddReplyTo($smtp_Admmail, $smtp_Admmail);

        $mail->WordWrap = 50;
        $mail->IsHTML(true);

        $mail->Subject = $mail_var02;
        $mail->Body    = $mailtmp;
        $mail->AltBody = '您的瀏覽器不支援HTML格式';
        
        if (!$mail->Send()) {
            //echo 'Message was not sent <br />';
            //echo 'Mailer Error: '.$mail->ErrorInfo;
            mail_errlog('後台登入通知信', $mail_msgtype, $tmpsc1_sub, $mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
            //exit;
        } else {
            //echo 'Message was sent <br />';
            mail_errlog('後台登入通知信',$mail_msgtype,$tmpsc1_sub,'Final', $sys_datetime, $remote_ipaddress);
            //exit;
        }
	}
	redirect('whiteboard01_view.php');
}else{
    //更新登入紀錄
    $strSQL_updateLoginLog = "UPDATE web_account_history 
                                SET add_date = '".$sys_datetime."', 
                                verify_code = AES_ENCRYPT('".$oneCode."','".$AESKEY01.'x'.$AESKEY02."'),
                                logincheck = 'NOID'
                                WHERE mbr_id = '".$pUserID."' 
                                AND google_secret = AES_ENCRYPT('".$secret."','".$AESKEY01.'x'.$AESKEY02."')";
    $result_updateLoginLog = SqlQuery1($strSQL_updateLoginLog);
    post_message_token("\n狀態：登入第一階段成功第二階段沒Match資料\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress"."\nSecret：".$secret."\nVerify：".$oneCode, $token);
    errback ("帳號和單位不正確");
	exit();
}




  //$strSQL1="select e.* from web_account as e where e.mbr_pass = '". md5($MD5KEY01.$pPassword.$MD5KEY02) ."' and e.mbr_lv01 = '". $pUserUnit ."' and e.mbr_id = '". $pUserID ."' ";
  //$strSQL1="select e.* from web_account as e where e.mbr_pass = AES_ENCRYPT('".$pPassword."','".$AESKEY01.'x'.$AESKEY02."') and e.mbr_lv01 = '". $pUserUnit ."' and e.mbr_id = '". $pUserID ."' ";
  //echo $strSQL1.'<br />';
  //$res = SqlQuery1($strSQL1);
/*    
  if ($res->RecordCount() == 1){
  	  //更新登入日期
			$strSQL1="update web_account set update_date = '".$sys_datetime."' , update_ipaddress = '".$remote_ipaddress."' where mbr_id = '". $pUserID ."' ";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
  	  //更新登入日期
	  	  //起始SESSION
	     	$_SESSION['session_security_id'] = $res->fields['serialid'];
	     	$_SESSION['session_user_id']     = $res->fields['mbr_id'];
	     	$_SESSION['session_user_name']   = $res->fields['mbr_name'];
	     	$_SESSION['session_user_lv01']   = $res->fields['mbr_lv01'];
	     	$_SESSION['session_user_lv02']   = $res->fields['mbr_lv02'];
	  	  //起始SESSION	
	  	//取出群組名稱
	  	  $strSQL1="select * from web_group_lv01 where serialid = ". $_SESSION['session_user_lv01'] ." ";
	  	  //echo $strSQL1.'<br />';
	  	  $result = SqlQuery1($strSQL1);
	  	  $_SESSION['session_user_lv01_name']= $result->fields['var01'];
	  	//取出群組名稱
  	  //更新登入紀錄
			$strSQL1="insert into web_account_history (mbr_id,mbr_lv01,mbr_lv02,add_date,add_ipaddress,logincheck ) values('".$pUserID."','".$res->fields["mbr_lv01"]."','".$res->fields["mbr_lv02"]."','".$sys_datetime."','".$remote_ipaddress."','OK')";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
  	  //更新登入紀錄
        post_message_token("\n狀態：登入成功+認證成功\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress"."\n安全ID：".$_SESSION['session_security_id'], $token);
	    redirect('whiteboard01_view.php');
  }else{
  	  //更新登入紀錄
			$strSQL1="insert into web_account_history (mbr_id,mbr_lv01,mbr_lv02,add_date,add_ipaddress,logincheck ) values('".$pUserID."','".$pUserUnit."','0','".$sys_datetime."','".$remote_ipaddress."','FAIL')";
			//echo $strSQL1.'<br />';
			$result = SqlQuery1($strSQL1);
  	  //更新登入紀錄
  	        post_message_token("\n狀態：登入失敗+認證成功\n單位：".$pUserUnit."\n帳號：".$pUserID."\n位置：$remote_ipaddress"."\n安全ID：".$_SESSION['session_security_id'], $token);
			errback ('帳號密碼錯誤');
			exit;
  }
*/
?>