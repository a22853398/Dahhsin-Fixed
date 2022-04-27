<?php
include 'commonfile/config.inc.php';

$tpl->assign('mainurl', $config['mainurl']);
$tpl->assign('rewrite_url', $config['rewrite_url']);

/* 所有分類 */
$sqlStr_lv01_type = "SELECT serialid, sortid, title01, title02, visible FROM faq_article01_lv01 WHERE visible='Y' ORDER BY sortid";
$res_lv01_type = SqlQuery1($sqlStr_lv01_type);
$array_lv01_type = array();
while(!$res_lv01_type->EOF){
    array_push($array_lv01_type, 
                array(
                    "serialid"  => $res_lv01_type->fields["serialid"],
                    "title01"     => $res_lv01_type->fields["title01"],
                    "color"     => $res_lv01_type->fields["title02"],
                )
    );
    $res_lv01_type->MoveNext();
}
$tpl->assign("faq_lv01_type", $array_lv01_type);

/* 現在分類 */
$lv01_type = urldecode($_GET["lv01_type"]);
(($lv01_type===null) || ($lv01_type==="")) ? $lv01_type="運費": $lv01_type=$lv01_type;//初始化，如果是空的話預設設運費
for($i=0; $i<count($array_lv01_type); $i++){
    if($lv01_type === $array_lv01_type[$i]["title01"]){
        $title_now = $array_lv01_type[$i]["title01"];
        $serialid_now = $array_lv01_type[$i]["serialid"];
    }
}
$tpl->assign("title_now",$title_now);
$tpl->assign("serialid_now",$serialid_now);

/* 分類裡的所有文章 */
$today = date("Y-m-d");
$sqlStr_faqArticles = "SELECT serialid, sortid, lv01_type, title01, content01, 
                                content02, news_date, news_end_date, 
                                DATE_FORMAT(update_date, '%Y-%m-%d') AS update_time, topup
                                FROM faq_article01
                                WHERE lv01_type=(SELECT serialid FROM faq_article01_lv01 WHERE title01='".$title_now."') 
                                AND news_date <= '".$today."' AND news_end_date >= '".$today."'
                                AND visible='Y'
                                ORDER BY topup DESC, sortid ASC ,update_time DESC";
$res_faq_Articles = SqlQuery1($sqlStr_faqArticles);
$array_faq_Articles = array();
while(!$res_faq_Articles->EOF){
    array_push($array_faq_Articles, 
                array(
                    "serialid"      => $res_faq_Articles->fields["serialid"],
                    "title01"       => $res_faq_Articles->fields["title01"],
                    "content01"     => $res_faq_Articles->fields["content01"],
                    "content02"     => $res_faq_Articles->fields["content02"],
                    "update_time"   => $res_faq_Articles->fields["update_time"],
                )
    );
    $res_faq_Articles->MoveNext();
}
$tpl->assign("faq_Articles", $array_faq_Articles);

$tpl->display('faq.shtm');
?>
