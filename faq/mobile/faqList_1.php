<?php
include 'commonfile/config.inc.php';

//$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );
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

$tpl->display('faqList_1.shtm');
?>
