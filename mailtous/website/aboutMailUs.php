<?php
include('commonfile/config.inc.php');

/* 偵測手機轉頁 */
//include("03mobileDetect.php");
//$redirectURL = "/aboutMailUs.php";
//redirectToMobile($redirectURL);

/* 問題類型資料列表 */
$strSql_questionType = "SELECT * FROM mailtous_type01 WHERE lv_type = '0' AND visible='Y' ORDER BY sortid";
$res_questionType = SqlQuery1($strSql_questionType);
$ary_questionType = array();
for($i=0; $i<$res_questionType->RecordCount(); $i++){
    $type_title = $res_questionType->fields["title01"];
    $type_value = $res_questionType->fields["value01"];
    $serialid = $res_questionType->fields["serialid"];
    $list_tmp = [ "title"=>$type_title, "serialid"=>$serialid, "value"=>$type_value ];
    array_push($ary_questionType, $list_tmp);
    $res_questionType->MoveNext();
}
$tpl->assign('question_type', $ary_questionType);

/* 子問題類型資料列表 */
$ajaxQuestion = $_GET['qvalue'];
if($ajaxQuestion !== null){
    $strSql_question = "SELECT * FROM mailtous_type01 WHERE lv_type = '1' AND visible='Y' AND parent_id = '".$ajaxQuestion."' ORDER BY sortid ";
    $res_question = SqlQuery1($strSql_question);
    $str_questionOption = "";
    for($i=0; $i<$res_question->RecordCount(); $i++){
        $type_title = $res_question->fields["title01"];
        $type_value = $res_question->fields["value01"];
        $serialid = $res_question->fields["serialid"];
        $str_questionOption .= "<option value='".$type_value."'>".$type_title."</option>";
        $res_question->MoveNext();
    }
    echo $str_questionOption;
    exit;
}




$tpl->assign('mainurl', $config['mainurl']);
$tpl->assign('rewrite_url', $config['rewrite_url']);

$tpl->display('aboutMailUs.shtm');
?>
