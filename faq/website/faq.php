<?php
include('commonfile/config.inc.php');
$today = date("Y-m-d");


$sqlStrLv01_type = "SELECT serialid, sortid, title01, title02, visible FROM faq_article01_lv01 WHERE visible='Y' ORDER BY sortid";
$sqlStrCount = "SELECT COUNT(*) FROM (".$sqlStrLv01_type.") AS tmp";
$resCount = GetQueryValueCount1($sqlStrCount, 'value');
$res = SqlQuery1($sqlStrLv01_type);

$faqArray = array();
for($i = 0; $i<intval($resCount); $i++){
    array_push($faqArray, array(
        "title" => $res->fields["title01"],
        "id" => $res-> fields["serialid"],
        "id_encode" => urlencode($res->fields["title01"]),
        "color" => $res->fields["title02"],
    ));
    $res->MoveNext();
}

$boolean_lv01_type_inArray = false;
$lv01_type = urldecode($_GET["lv01_type"]);//網址問號後面的值
for($i=0; $i<count($faqArray); $i++){
    if($lv01_type === $faqArray[$i]["title"]){
        $boolean_lv01_type_inArray = true;//如果有在陣列裡
    }else{
        //如果沒在在陣列裡
    }
}
//lv01_type 的 初始值
if($boolean_lv01_type_inArray === false || $lv01_type === null){ $lv01_type = $faqArray[0]["title"];}



$sqlStrSelectArticles = "SELECT serialid, sortid, lv01_type, title01, content01, 
                                content02, news_date, news_end_date, 
                                DATE_FORMAT(update_date, '%Y-%m-%d') AS update_time, topup
                                FROM faq_article01
                                WHERE lv01_type=(SELECT serialid FROM faq_article01_lv01 WHERE title01='".$lv01_type."') 
                                AND news_date <= '".$today."' AND news_end_date >= '".$today."'
                                AND visible='Y'
                                ORDER BY topup DESC, sortid ASC ,update_time DESC";
$sqlStrCountArticles = "SELECT COUNT(*) FROM (".$sqlStrSelectArticles.") AS tmp";
$resCountArticles = GetQueryValueCount1($sqlStrCountArticles, 'value');
$resArticles = SqlQuery1($sqlStrSelectArticles);
$articlesArray = array();
for($i=0; $i<intval($resCountArticles); $i++){
    $articlesArray[] = array(
        "serialid" => $resArticles->fields["serialid"],
        "sortid" => $resArticles->fields["sortid"],
        "lv01_type" => $resArticles->fields["lv01_type"],
        "title01" => $resArticles->fields["title01"],
        "content01" => $resArticles->fields["content01"],
        "content02" => $resArticles->fields["content02"],
        "news_date" => $resArticles->fields["news_date"],
        "news_end_date" => $resArticles->fields["news_end_date"],
        "update_time" => $resArticles->fields["update_time"],
        "topup" => $resArticles->fields["topup"],
    );
    $resArticles->MoveNext();
}

$tpl->assign('articlesArray',$articlesArray);//文章們的陣列
$tpl->assign('lv01_type', $lv01_type);//分類標題

$tpl->assign('mainurl', $config['mainurl'] );
$tpl->assign('rewrite_url', $config['rewrite_url'] );

$tpl->display('faq.shtm');
?>
