<?php
include('commonfile/config.inc.php');
require("commonfile/phpmailer/PHPMailerAutoload.php");
include('commonfile/authimg/authimg.inc.php');
include('commonfile/reject_otherurl_get.php');
include('commonfile/mail_errlog.inc.php');

$mbrName = strip_input('input,cut30',$_REQUEST['mbrName']);//客人姓名
$mbrMail = strip_input('input,cut50',$_REQUEST['mbrMail']);//客人Email
$mbrPhone = strip_input('input,cut50',$_REQUEST['mbrPhone']);//客人電話
$mbrContent = $_REQUEST['mbrContent'];//客人問題內容
//內容的分行符號變成<br>
$mbrContent = str_replace(array("\n"), "<br>", $mbrContent);
$q_type01 = strip_input('input,cut30',$_REQUEST['q_type01']);//問題分類一
$q_type02 = strip_input('input,cut30',$_REQUEST['q_type02']);//問題分類二
$mbrReplyLan = strip_input('input,cut30',$_REQUEST['mbrReplyLan']);//回覆語言
$mbrOderid = strip_input('input,cut30',$_REQUEST['mbrOrderid']);//訂單編號

//echo $pUserID."<br>".$pEmail."<br>".$pAnswer."<br>".$mbrFile."<br>";
//echo "test<br>".$mbrFile."<br>";
$respon = $_POST["g-recaptcha-response"];//取得回傳到這邊的檔案

#=============================================
# reCaptcha v2
#=============================================
include("03lineNotify.php");
include("03googleRecaptchaVerify.php");
$googleObject = new GoogleRecaptcha();
//$linemsg = "\n來源".$_SERVER["HTTP_REFERER"]."\n名：".$pUserID."\n信：".$pEmail."\n解答：".$pAnswer."\nIP：".$remote_ipaddress;
$token = "XXXXXXXXXXXXXXXXXX";

//判斷是手機版還是電腦版
$site_version = '';
$mail_msgtype  = '';
if(strpos($_SERVER["HTTP_REFERER"], "/m/", 0)){
    $site_version = "/m";
    $mail_msgtype = "m_".$mail_msgtype;
}else{
    $site_version = "";
}
//手機版還是電腦版end
if($googleObject->googleVerify($respon) === true){
    $mail_msgtype .= "mailus";
    //post_message_token("驗證成功".$linemsg, $token);
}else{
    $mail_msgtype .= "mailus_failed";
    //post_message_token("驗證失敗".$linemsg, $token);
    redirect($config['mainurl'].$site_version."/"."msg_view01.php"."?sendmsg=".$mail_msgtype);
    exit();
}






//簡易回覆信件內文
$strSQL1 = "SELECT * FROM mailtous_type01 WHERE value01 = '".$q_type02."' ";
//echo $strSQL1.'<br />';
$resMailContent = SqlQuery1($strSQL1);
$mail_content = "";
switch($mbrReplyLan){
    case 'eng':
        $mail_content .= "<br>
            Hi ".$mbrName.",<br>
            Thanks for contacting Dahhsin Publish Group.<br>
            This is auto-reply from Dahhsin official website.<br>
            <br>
            As follow, is the content of your inquiry.<br>
            ------------------------------------<br>
            Name: ".$mbrName."<br>
            Phone: ".$mbrPhone."<br>
            Mail: ".$mbrMail."<br>
            Inquiry Category1: ".SqlQuery1("SELECT title01 FROM mailtous_type01 WHERE value01='$q_type01'")->fields["title01"]."<br>
            Inquiry Category2: ".$resMailContent->fields["title01"]."<br>
            Response Language: English<br>
            Request IP address: ".$remote_ipaddress."<br>
            Order Number: ".$mbrOderid."<br>
            Inquiry Content: <br>".$mbrContent."<br>
            ------------------------------------<br>
            We will confirm your inquiry and reply to you as soon as possible.<br>
            Here is the FAQ list you could take a look first. Maybe these problems can be satisfactorily resolved.<br>
            <br>▲FAQ
            <br>***********************<br>
            <strong>".$resMailContent->fields["content01"]."</strong><br>
            <br>***********************<br>
            <br>
            Please do not reply this email directly because it is auto-reply mail address.<br>
            If you would like to contact us, please forward the mail to dhlin@dahhsin.com.tw.<br>
            And if you have urgent inquiry, please contact us via phone or Facebook Messanger or Line.<br>
            <br>
            Taipei Management Depart: (02)2707-3232<br>
            Wugu Sales Department: (02)2291-6855<br>
            You could find above information at the footer of https://www.dahhsin.com.tw
        ";    
        break;
    case 'jpn':
        $mail_content .= "<br>
            ".$mbrName." 様：<br>
            お問い合わせいただき、誠にありがとうございます。<br>
            こちらはシステムの自動返信となります。<br>
            <br>
            お問い合わせ頂いた内容は下記でございます。<br>
            -------------------------------------------<br>
            名前：".$mbrName." 様<br>
            電話：".$mbrPhone."<br>
            メール：".$mbrMail."<br>
            問題カテゴリ：".SqlQuery1("SELECT title01 FROM mailtous_type01 WHERE value01='$q_type01'")->fields["title01"]."<br>
            問題タイプ：".$resMailContent->fields["title01"]."<br>
            ご希望の返事言語：日本語<br>
            送信IPアドレス：".$remote_ipaddress."<br>
            注文番号：".$mbrOderid."<br>
            お問い合わせ内容：<br>".$mbrContent."<br>
            -------------------------------------------<br>
            カスタマーサービスは営業時間内に、お問い合わせ頂いた内容を確認してから、返信させていただきます。<br>
            また、下記はご質問のカテゴリとタイプに対応するよくある質問です。ご参考いただければ幸いです。<br>
            <br>▲よくある質問
            <br>***********************<br>
            <strong>".$resMailContent->fields["content01"]."</strong><br>
            <br>***********************<br>
            <br>
            このメールはシステムの自動返信なため、ご返信頂いてもお返事致しかねます。<br>
            ご返信の際は「dhlin@dahhsin.com.tw」にご返信ください。<br>
            もしお問い合わせ内容が急用でしたら、下記の電話にお問い合わせくださいませ。<br>
            台北本社：(02)2707-3232<br>
            五股倉庫：(02)2291-6855<br>
            もしくは弊社サイト上のLineかフェイスブックメッセンジャーにてお問い合わせください。<br>
        ";
        break;
    case 'zh_ch':
    default:    
        $mail_content .= "<br>
            親愛的顧客，您好：<br>
            感謝您使用大新書局官方網站寄信諮詢系統。<br>
            此郵件為留存信件，表示本公司有收到您詢問。<br>
            您此次諮詢的內容如下。<br>
            -------------------------<br>
            姓名：".$mbrName."<br>
            電話：".$mbrPhone."<br>
            信箱：".$mbrMail."<br>
            問題類型一：".SqlQuery1("SELECT title01 FROM mailtous_type01 WHERE value01='$q_type01'")->fields["title01"]."<br>
            問題類型二：".$resMailContent->fields["title01"]."<br>
            希望回覆語言：繁體中文<br>
            IP位置：".$remote_ipaddress."<br>
            訂單編號：".$mbrOderid."<br>
            內容：<br>".$mbrContent."<br>
            --------------------------<br>
            大新書局客服人員會在勤務時間內確認您的問題之後，儘速回覆您。<br>
            另外，關於您詢問的問題，下方有一些簡單的Q&A提供您參考。<br>
            <br>▲常見問題回覆
            <br>***********************<br>
            <strong>".$resMailContent->fields["content01"]."</strong><br>
            <br>***********************<br>
            <br>
            ＊請注意！此信件由系統自動發出，請勿直接回覆此信件。<br>
            　若您急著聯絡本公司，建議您電話聯絡本公司會比較快。<br>
            　台北總公司：(02)2707-3232<br>
            　五股營業部：(02)2291-6855<br>
            　或是您可以利用本公司官方網站的Line或FB洽談聯絡客服人員。<br>
            　或是您可以寄信到 dhlin@dahhsin.com.tw <br>
        ";
        break;
}
//信件內文

//上傳附檔區start
if(isset($_FILES['image']) && !empty($_FILES["image"]["name"])){
    /*檔名*/
    $file_name = date("Ymd_His",time())."_".substr(base64_encode(explode('.',$_FILES['image']['name'])[0]), 0, 10);
    $file_size =$_FILES['image']['size'];
    $file_tmp =$_FILES['image']['tmp_name'];
    $file_type=$_FILES['image']['type'];
    /*副檔名*/
    $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
    
    $extensions= array("jpeg","jpg","png","pdf");
    /*驗證副檔名*/  
    if(in_array($file_ext,$extensions)=== false){
        $errors[]="extension not allowed, please choose a JPEG or PNG file.";
    }
    /*檔案大小，單位binary*/  
    if($file_size > 10485760){
        $errors[]='File size must be excately 10 MB';
    }
      
    if(empty($errors)==true){
        move_uploaded_file($file_tmp, "uploadfile/mail_attached/".$file_name.".".$file_ext);
        //echo "Success";
        //errback("檔案上傳成功");
    }else{
        //print_r($errors);//上傳的錯誤訊息
        $errmsg = "";
        for($i=0;$i<count($errors);$i++){
            $errmsg .= "\n".$errors[$i];
        }
        //errback("上傳檔案失敗：".$errmsg);
        $mail_msgtype = "upload_failed";
        redirect($config['mainurl'].$site_version."/"."msg_view01.php?sendmsg=$mail_msgtype");
        exit();
    }
}
$file_path = "uploadfile/mail_attached/".$file_name.".".$file_ext;//檔案路徑
//上傳附檔區end


//寄送信件
$tmpsc1 = explode ('/', $_SERVER['SCRIPT_NAME']);
$tmpsc1_sub = $tmpsc1[count($tmpsc1)-1];

//輸出系統發信設定
$strSQL1="select sortid,var01,var02,var03 from xsys_defset where lv00_type = '2' order by sortid ";
$res = SqlQuery1($strSQL1);
while(!$res->EOF){
    $var01 = 'smtp_'.$res->fields['var02'];
    $$var01 = $res->fields['var03'];
    $res->MoveNext();
}
//輸出系統設定參數

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
$mail->FromName = "大新書局";
$mail->AddAddress($mbrMail);
$array_mailAddress = explode( ",", $resMailContent->fields["email01"]);
for($i=0; $i<count($array_mailAddress); $i++){
    $mail->AddAddress($array_mailAddress[$i]);
}
$array_mailAddressCC = explode( ",", $resMailContent->fields["email02"]); 
for($i=0; $i<count($array_mailAddressCC); $i++){
    $mail->AddCC($array_mailAddressCC[$i]);
}
$mail->AddReplyTo("dhlin@dahhsin.com.tw", "大新書局台北總公司");

$mail->WordWrap = 50;
$mail->AddAttachment($file_path);//附加檔案
//$mail->AddAttachment('test/test.png'); 
//$mail->AddEmbeddedImage('test/test.png', 'my-attach', 'test/test.png','base64', 'image/png');


$mail->IsHTML(true);

$mail->Subject  =  "【大新書局】寄信諮詢 ".SqlQuery1("SELECT title01 FROM mailtous_type01 WHERE value01='$q_type01'")->fields["title01"].$resMailContent->fields["title01"];
$mail->Body     =  $mail_content;

$mail->AltBody  =  '您的瀏覽器不支援HTML格式';

//寫入資料庫記錄
if(!$mail->Send()){
    //mail_errlog('寄信諮詢_aboutMailUs_failed',$mail_msgtype,$tmpsc1_sub,$mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
    /*刪除上傳圖片，不然會很佔空間*/
    if (!unlink($file_path)) { 
        //echo ("$file_path cannot be deleted due to an error"); 
        //errback("送信失敗：檔案也不存在");
        $mail_msgtype = "emailnotexist_filenotexist";
        mail_errlog('寄信諮詢_aboutMailUs_failed',$mail_msgtype,$tmpsc1_sub,$mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
        redirect($config['mainurl'].$site_version."/"."msg_view01.php?sendmsg=$mail_msgtype");
        exit();
    } 
    else { 
        //echo ("$file_path has been deleted");
        //errback("送信失敗：檔案刪除成功");
        $mail_msgtype = "emailnotexist_filedeleted";
        mail_errlog('寄信諮詢_aboutMailUs_failed',$mail_msgtype,$tmpsc1_sub,$mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
        redirect($config['mainurl'].$site_version."/"."msg_view01.php?sendmsg=$mail_msgtype");
        exit();
    }
    
    redirect($config['mainurl'].$site_version."/"."msg_view01.php?sendmsg=$mail_msgtype");
}else{
    //mail_errlog('寄信諮詢_aboutMailUs_success',$mail_msgtype,$tmpsc1_sub,'Final', $sys_datetime, $remote_ipaddress);
    /*刪除上傳圖片，不然會很佔空間*/
    if (!unlink($file_path)) {
        //如果寄信成功，客人沒有上傳附檔的話，會執行這邊
        //echo ("$file_path cannot be deleted due to an error"); 
        //errback("檔案不存在");
        //$mail_msgtype = "file_notexist";
        mail_errlog('寄信諮詢_aboutMailUs_success_nofile',$mail_msgtype,$tmpsc1_sub,$mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
        redirect($config['mainurl'].$site_version."/"."msg_view01.php?sendmsg=$mail_msgtype");
        exit();
    } 
    else { 
        //echo ("$file_path has been deleted");
        //errback("刪除檔案成功");
        mail_errlog('寄信諮詢_aboutMailUs_success_hasfile',$mail_msgtype,$tmpsc1_sub,$mail->ErrorInfo, $sys_datetime, $remote_ipaddress);
        redirect($config['mainurl'].$site_version."/"."msg_view01.php?sendmsg=$mail_msgtype");
        exit();
    }
}
//寄送信件
?>