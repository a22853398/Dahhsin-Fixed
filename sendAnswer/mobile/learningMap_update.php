<?php
include 'commonfile/config.inc.php';
require "commonfile/phpmailer/PHPMailerAutoload.php";
include 'commonfile/authimg/authimg.inc.php';
include 'commonfile/reject_otherurl_get.php';
include 'commonfile/mail_errlog.inc.php';

$pUserID = strip_input('input,cut30', $_REQUEST['pUserID']);
$pEmail = strip_input('input,cut50', $_REQUEST['pEmail']);
$mail_msgtype = 'sendAnswer';
$array_answers = $_POST["answers"];

include "../03lineNotify.php";

include "../03googleRecaptchaVerify.php";
$respon = $_POST["g-recaptcha-response"];
$googleObject = new GoogleRecaptcha();
$token = "XXXXXXXXXXXXXXXXXX";
$answerStr = "";
for($i=0; $i<count($array_answers); $i++){
    $answerStr.= $array_answers[$i]." ";
}
$linemsg = "\n來源：".$_SERVER["HTTP_REFERER"]."\n名：".$pUserID."\n信：".$pEmail."\n解答：".$answerStr."\nIP：".$remote_ipaddress;
if($googleObject->googleVerify($respon) === true){
    post_message_token("驗證成功".$linemsg, $token);
}else{
    post_message_token("驗證失敗".$linemsg, $token);
    redirect($config['mainurl'] . '/msg_view01.php?sendmsg=' . 'm_learningMap_failed');
    exit();
}
#============================================================================
# 寄送信件
#============================================================================

//寄送信件
$tmpsc1 = explode ('/', $_SERVER['SCRIPT_NAME']);
$tmpsc1_sub = $tmpsc1[count($tmpsc1)-1];

//輸出系統發信設定
  $strSQL1="select sortid,var01,var02,var03 from xsys_defset where lv00_type = '2' order by sortid ";
  //echo $strSQL1.'<br>';
  $res = SqlQuery1($strSQL1);
  while(!$res->EOF){
    $var01 = 'smtp_'.$res->fields['var02'];
    $$var01 = $res->fields['var03'];
    //echo $var01 .'='. $$var01 .'<br>';
  $res->MoveNext();
  }
//輸出系統設定參數


  //取出信件內文
    $strSQL1="select lv01_type,var01,var02,var03,var04,var05 from mail_content01 where lv01_type = '".$mail_msgtype."' ";  
		//echo $strSQL1.'<br />';
		$res = SqlQuery1($strSQL1);
		$lv01_type = $res->fields['lv01_type'];
		$mail_var01 = $res->fields['var01'];
		$mail_var02 = $res->fields['var02'];
		$mail_var03 = $res->fields['var03'];
		$mail_var04 = $res->fields['var04'];
		$mail_var05 = $res->fields['var05'];

		$mailtmp = $mail_var05;
		$mailtmp = str_replace('{mbr_id}',$mbr_id,$mailtmp);
		$mailtmp = str_replace('{mbr_name}',$mbr_name,$mailtmp);
		$mailtmp = str_replace('{mbr_email}',$pEmail,$mailtmp);//
		
		$mailtmp = str_replace('{mainurl}',$config['mainurl'],$mailtmp);
		$mailtmp = str_replace('{main_webname}',$config['main_webname'],$mailtmp);

		$mailtmp = str_replace('{mailtitle}',$mail_var03,$mailtmp);
		$mailtmp = str_replace('{mailvar01}',$mail_var04,$mailtmp);
		
		$mailtmp = str_replace('{pUserID}',$pUserID,$mailtmp);
  //取出信件內文
  
  /*
  //自定義內容
    $set01 = $mbr_pass_new;
    $mailtmp = str_replace('{mailset01}',$set01,$mailtmp);
  //自定義內容
  */


$mail = new PHPMailer();
$mail->Encoding = '8bit';
$mail->CharSet = 'utf-8';

$mail->IsSMTP();

if($smtp_SMTPSecure != '' ){
  $mail->SMTPSecure = $smtp_SMTPSecure;
}
$mail->Host     = $smtp_Host;
if($smtp_Port != '' ){
  $mail->Port = $smtp_Port;
}
if($smtp_SMTPAuth == 'true' ){
  $mail->SMTPAuth = true;
  $mail->Username = $smtp_Username;
  $mail->Password = $smtp_Password;
}

$mail->From     = $smtp_Admmail;
$mail->FromName = $mail_var01;
$mail->AddAddress($pEmail);
$mail->AddReplyTo($smtp_Admmail,$smtp_Admmail);

$mail->WordWrap = 50;
for($i=0; $i<count($array_answers); $i++){
    $pAnswer = $array_answers[$i];
    $mail->AddAttachment('../uploadfile/answer/'.$pAnswer.'.pdf');
}

$mail->IsHTML(true);

$mail->Subject  =  $mail_var02;
$mail->Body     =  $mailtmp;

$mail->AltBody  =  '您的瀏覽器不支援HTML格式';

//寫入資料庫記錄
if(!$mail->Send()){
    mail_errlog('索取解答信件_learningMap_failed',$mail_msgtype,$tmpsc1_sub,$mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
    redirect($config['mainurl']."/"."msg_view01.php?sendmsg=m_learningMap_success");
}else{
    mail_errlog('索取解答信件_learningMap_success',$mail_msgtype,$tmpsc1_sub,'Final', $sys_datetime, $remote_ipaddress);
    redirect($config['mainurl']."/"."msg_view01.php?sendmsg=m_learningMap_success");
}

//寄送信件
//redirect($config['mainurl'] . '/msg_view01.php?sendmsg=' . 'm_learningMap_success');
?>