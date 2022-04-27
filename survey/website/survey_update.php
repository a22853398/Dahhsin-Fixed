<?php
include "commonfile/config.inc.php";
include "commonfile/get_serialid.inc.php";
include "03googleRecaptchaVerify.php";
include "03lineNotify.php";
######################################
$response = $_POST["g-recaptcha-response"];//取得回傳到這邊的檔案
$googleObject = new GoogleRecaptcha;

if($googleObject->googleVerify($response) === true){
    //post_message("問卷驗證成功\n來源：".$_SERVER["HTTP_REFERER"]."\nIP：".$remote_ipaddress."\n帳：".$_SESSION["session_webmbr_login_id"]);
}else{
    $verifyMsg="failed_survey_verify";//失敗時決定要跳轉的頁面的訊息(SQL的訊息)
    //post_message("問卷驗證成功\n來源：".$_SERVER["HTTP_REFERER"]."\nIP：".$remote_ipaddress."\n帳：".$_SESSION["session_webmbr_login_id"]);
    redirect($config['mainurl'].'/msg_view01.php?sendmsg='.$verifyMsg);
    exit();
}

//問券編號(有效的)
$today = date("Y-m-d");
//$lv00_type = "S21110300001";
$strSql_ableSurvey = "SELECT * FROM survey_article WHERE start_date <= '".$today."' AND end_date >= '".$today."' AND visible='Y'";
$count_ableSurvey = GetQueryValueCount1($strSql_ableSurvey, "");//計算幾筆, integer
$lv00_type = SqlQuery1($strSql_ableSurvey)->fields["lv00_type"];
//一定要接收的，登入狀態
$mbr_id = $_SESSION["session_webmbr_login_id"];
//如果沒有登入狀態表示問卷寫太久，頁面都沒有動被踢出SESSION
if($mbr_id === null || $mbr_id === ""){
    $verifyMsg = "failed_survey";//沒有ID不給寫入
    //redirect($config['mainurl'].'/msg_view01.php?sendmsg='.$verifyMsg);
    //exit();
}else{
    
}

//判斷同一張問卷是否同一帳號重複作答，返回布林，true表示有資料重複，false表示第一次寫
function checkDoubleAnswer($whichSurvey, $mbrId){
    $strSql_check = "SELECT lv00_type, mbr_id FROM survey_respon WHERE lv00_type = '".$whichSurvey."' AND mbr_id = '".$mbrId."'";
    $strSql_check_count = "SELECT COUNT(*) FROM (".$strSql_check.") AS tmp";
    $countSql = GetQueryValueCount1($strSql_check_count, "value");
    if(intval($countSql) > 0){
        return true;
    }else{
        return false;
    }
}
if(checkDoubleAnswer($lv00_type, $mbr_id)){
    $verifyMsg = "failed_survey";//重複填寫問券
    redirect($config['mainurl'].'/msg_view01.php?sendmsg='.$verifyMsg);
    exit();
}else{
    
}

#######################################
//現在時間
$currentTime = date("Y-m-d H:i:s");
$today = date("Y-m-d");
//追加IP位址
$add_ipaddress = $remote_ipaddress;
//來源自哪個頁面
$from_url = $_SERVER["HTTP_REFERER"];//https://www.dahhsin.com.tw/survey.php
$add_from = "";
$redirect_url = "";
if(strpos($from_url, "/m/survey.php") > 0){
    $add_from = "MB1";
    $redirect_url = "/m/survey.php";
}else{
    $add_from = "PC1";
    $redirect_url = "/survey.php";
}


//回覆編號流水號
function getResponid($date){
    $firstChar = "R";//第一個字
    $secondChar = date("ymd");//放日期的六個字 ex: 211010
    $sqltpStr = "SELECT COUNT(*) AS count FROM survey_respon WHERE DATE_FORMAT(add_date, '%Y-%m-%d') = '".$date."'";
    $restp_count = SqlQuery1($sqltpStr)->fields["count"];
    $thirdChar = str_pad(intval($restp_count)+1,5,"0",STR_PAD_LEFT);//放當天的第幾筆，就是取資料庫算幾筆之後數字加一
    return $firstChar.$secondChar.$thirdChar;
}
$serialid = getResponid($today);
//回覆編號
$responid = getResponid($today);

//寫入問卷內容
//客人姓名
$mbr_name = $_POST["mbr_name"];
$mbr_name = htmlspecialchars(chop(mb_substr( $mbr_name, 0, 50, "UTF-8")));
//性別
$mbr_gender = $_POST["mbr_gender"];
$mbr_gender = htmlspecialchars(chop(mb_substr(strtolower($mbr_gender), 0, 50, "UTF-8")));
//年齡
$mbr_years = $_POST["mbr_years"];
$mbr_years = htmlspecialchars(chop(mb_substr(strtolower($mbr_years), 0, 50, "UTF-8")));
//教育
$mbr_education = $_POST["mbr_education"];
$mbr_education = htmlspecialchars(chop(mb_substr(strtolower($mbr_education), 0, 50, "UTF-8")));
//客人Email
$mbr_email = $_SESSION["session_webmbr_login_id"];
$mbr_email = htmlspecialchars(chop(mb_substr(strtolower($mbr_email), 0, 50, "UTF-8")));
//日語學習
$jpn_level = $_POST["jpn_level"];
$jpn_level = htmlspecialchars(chop(mb_substr(strtolower($jpn_level), 0, 50, "UTF-8")));
//日語學習時間
$jpn_learn_time = $_POST["jpn_learn_time"];
$jpn_learn_time = htmlspecialchars(chop(mb_substr(strtolower($jpn_learn_time), 0, 50, "UTF-8")));
//日語花費
$jpn_learn_money = $_POST["jpn_learn_money"];
$jpn_learn_money = htmlspecialchars(chop(mb_substr(strtolower($jpn_learn_money), 0, 50, "UTF-8")));
//筆
$pen_yes = $_POST["pen_yes"];
$pen_yes = htmlspecialchars(chop(mb_substr(strtolower($pen_yes), 0, 50, "UTF-8")));
//學習方式
$jpn_learn_type = $_POST["jpn_learn_type"];
//電子書使用
$ebook_use = $_POST["ebook_use"];
$ebook_use = htmlspecialchars(chop(mb_substr(strtolower($ebook_use), 0, 50, "UTF-8")));
//購買日語學習書
$ebook_jpbook = $_POST["ebook_jpbook"];
$ebook_jpbook = htmlspecialchars(chop(mb_substr(strtolower($ebook_jpbook), 0, 50, "UTF-8")));
//買幾本日語
$ebook_jpbook_howmany = $_POST["ebook_jpbook_howmany"];
$ebook_jpbook_howmany = htmlspecialchars(chop(mb_substr(strtolower($ebook_jpbook_howmany), 0, 50, "UTF-8")));
//最喜歡日語書
$ebook_jpbook_fav = $_POST["ebook_jpbook_fav"];
$ebook_jpbook_fav = htmlspecialchars(chop(mb_substr(strtolower($ebook_jpbook_fav), 0, 50, "UTF-8")));
//每月花多少時間
$ebook_time = $_POST["ebook_time"];
$ebook_time = htmlspecialchars(chop(mb_substr(strtolower($ebook_time), 0, 50, "UTF-8")));
//每月花多少錢
$ebook_spend = $_POST["ebook_spend"];
$ebook_spend = htmlspecialchars(chop(mb_substr(strtolower($ebook_spend), 0, 50, "UTF-8")));
//電子書平台
$ebook_platform = $_POST["ebook_platform"];
//電子書端末
$ebook_device = $_POST["ebook_device"];
//電子書理由
$ebook_reason = $_POST["ebook_reason"];
//願意嘗試電子書
$ebook_try = $_POST["ebook_try"];
$ebook_try = htmlspecialchars(chop(mb_substr(strtolower($ebook_try), 0, 50, "UTF-8")));
//希望推出電子書
$ebook_publish = $_POST["ebook_publish"];
//其他
$ebook_publish_other = $_POST["ebook_publish_other"];
$ebook_publish_other = htmlspecialchars(chop(mb_substr(strtolower($ebook_publish_other), 0, 50, "UTF-8")));
//未來出版
$ebook_puslish_type = $_POST["ebook_publish_type"];

$array_q1group = array(
        array("q_num" => "q0101", "q_ans" => $mbr_name),
        array("q_num" => "q0102", "q_ans" => $mbr_gender),
        array("q_num" => "q0103", "q_ans" => $mbr_years),
        array("q_num" => "q0104", "q_ans" => $mbr_education),
        array("q_num" => "q0105", "q_ans" => $mbr_email),
    );
$array_q2group = array(
        array("q_num" => "q0201", "q_ans" => $jpn_level),
        array("q_num" => "q0202", "q_ans" => $jpn_learn_time),
        array("q_num" => "q0203", "q_ans" => $jpn_learn_money),
        array("q_num" => "q0204", "q_ans" => $pen_yes),
        array("q_num" => "q0205", "q_ans" => $jpn_learn_type),
    );
$array_q3group = array(
        array("q_num" => "q0301", "q_ans" => $ebook_use),
        array("q_num" => "q0302", "q_ans" => $ebook_jpbook),
        array("q_num" => "q0303", "q_ans" => $ebook_jpbook_howmany),
        array("q_num" => "q0304", "q_ans" => $ebook_jpbook_fav),
        array("q_num" => "q0305", "q_ans" => $ebook_time),
        array("q_num" => "q0306", "q_ans" => $ebook_spend),
        array("q_num" => "q0307", "q_ans" => $ebook_platform),
        array("q_num" => "q0308", "q_ans" => $ebook_device),
        array("q_num" => "q0309", "q_ans" => $ebook_reason),
        array("q_num" => "q0310", "q_ans" => $ebook_try),
        array("q_num" => "q0311", "q_ans" => $ebook_publish),
        array("q_num" => "q0312", "q_ans" => $ebook_publish_other),
        array("q_num" => "q0313", "q_ans" => $ebook_puslish_type),
    );
$array_json = array($array_q1group, $array_q2group, $array_q3group);

/*
$q0101 = $_POST["first_gender"];
$q0102 = $_POST["first_age"];
$q0103 = $_POST["first_netSurfing"];
$q0104 = $_POST["first_SNS"];
$array_q1group = array(
        array( "q_num" => "q0101", "q_ans" => $q0101),
        array( "q_num" => "q0102", "q_ans" => $q0102),
        array( "q_num" => "q0103", "q_ans" => $q0103),
        array( "q_num" => "q0104", "q_ans" => $q0104),
    );
$array_json = array();
array_push($array_json, $array_q1group);
array_push($array_json, $array_q2group);
array_push($array_json, $array_q3group);
array_push($array_json, $array_mbrInfo);
*/

//寫入資料庫的最終問卷回答內容
$survey_json = json_encode($array_json, JSON_UNESCAPED_UNICODE);
//echo $survey_json;
#######################################
//開啟資料庫，讓資料可寫入
$config['sql_link1']->StartTrans();
//客人回應寫入資料庫
$sqlStr = "INSERT INTO survey_respon (serialid, responid, lv00_type, mbr_id, mbr_name, add_date, add_ipaddress, content, add_from, update_date, check_status)
            VALUE ('".$serialid."', '".$responid."', '".$lv00_type."', '".$mbr_id."', '".$mbr_name."', '".$currentTime."' , '".$add_ipaddress."', '".$survey_json."', '".$add_from."', '".$currentTime."', 'N')";
$resRespon = SqlQuery1($sqlStr);
//關閉資料庫
$config['sql_link1']->CompleteTrans();
//填寫問券可以送折價券
/*COUPON產生序號*/
function getSerialNumber(){
    $rndstr = create_rndid(32);
    $nowdate = date("ymd");
    $strSQL1 = "select MAX(SUBSTRING( serialid,8,4)) as MAXID from coupon_event01_sublist01 where SUBSTRING(serialid,1,7) = 'F" . $nowdate . "' ";
    $res_getid  = SqlQuery1($strSQL1);
    $nextval = $res_getid->fields['MAXID'] + 1;
    $nextval_id = 'F' . $nowdate . sprintf('%04s', $nextval) . substr($rndstr, -4);
    return $nextval_id;
}
/*COUPON SQL基礎句*/
function getSqlInsert($serialid, $lv00_type, $lv01_id, $sortid, $var01, $var02, $coupon, $member_area, $member_id, $add_date){
    $strSql_insert = "INSERT INTO coupon_event01_sublist01
                (serialid, lv00_type, lv01_id, sortid, var01, var02, coupon, member_area, member_id, add_date)
                VALUE ('".$serialid."', '".$lv00_type."', '".$lv01_id."', '".$sortid."', '".$var01."', '".$var02."', '".$coupon."', '".$member_area."','".$member_id."','".$add_date."')
                ; ";    
    return $strSql_insert;
}
$couponSerial = getSerialNumber();
$coupon = strtoupper(substr($couponSerial, -6));//客人看到的Coupon號碼
$str_insertCoupon = getSqlInsert($couponSerial, 
                                "COUPON", 
                                "A2201030001f02b", 
                                "0", 
                                $mbr_id, 
                                '', 
                                $coupon, 
                                $remote_ipaddress, 
                                '填問券自動發行', 
                                date("Y-m-d H:i:s"));
$resInsertCoupon = SqlQuery1($str_insertCoupon);


redirect($config['mainurl'].$redirect_url);
?>