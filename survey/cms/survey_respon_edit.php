<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
include('00security_function.php');

$tpl_str = '問卷回覆';
$tpl_name = 'survey_respon';
$table_name = 'survey_respon';
$owner_type = 'edit';
$owner_array01 = owner_check01($table_name);

$dirname = '';

$opt = strip_input('inputadm',$_REQUEST['opt']);
$pid = strip_input('inputadm',$_REQUEST['pid']);
$page = strip_input('inputadm',$_REQUEST['page']);
$sel00_type = strip_input('inputadm',$_REQUEST['sel00_type']);

$tpl = new TemplatePower( './'.$tpl_name.'_edit.htm' );
$tpl->assignInclude( 'external', './00external.php' );
$tpl->assignInclude( 'mainMenu', './00mainMenu.php' );
$tpl->prepare();

$tpl->assignglobal('tpl_str', $tpl_str );
$tpl->assignglobal('tpl_name', $tpl_name );
$tpl->assignglobal('WYSIWYG_path', $config['WYSIWYG_path'] );
$tpl->assign('pid', $pid );
$tpl->assign('page', $page );
$tpl->assign('sel00_type', $sel00_type);

if($owner_array01[2] == '1'){
	$tpl->newBlock('tablist01_doPost');
	$tpl->gotoBlock('_ROOT');
}
//$memberArea = $_SESSION['session_user_lv01_name'];//後台人員群組
//$memberId = $_SESSION['session_user_name'];//後台人員帳號名稱
//$memberIpaddress = $remote_ipaddress;//後台人員操作IP位址


/*************** 輸出問題 ********************/
$tpl->gotoBlock('_ROOT');
$strSql_question = "SELECT *, 
                    DATE_FORMAT(start_date,'%Y-%m-%d') AS start_date_format, 
                    DATE_FORMAT(end_date,'%Y-%m-%d') AS end_date_format 
                    FROM survey_article 
                    WHERE lv00_type='".(SqlQuery1("SELECT * FROM survey_respon WHERE responid = '".$pid."'")->fields["lv00_type"])."'";//選客人填的那張問卷
$res_question = SqlQuery1($strSql_question);
$survey_serialid = $res_question->fields["serialid"];
$survey_lv00_type = $res_question->fields["lv00_type"];
$survey_start_date = $res_question->fields["start_date_format"];
$survey_end_date = $res_question->fields["end_date_format"];
$survey_title = $res_question->fields["title"];
$survey_subtitle = $res_question->fields["subtitle"];
$survey_subscribe = $res_question->fields["subscribe"];
$survey_question_content = $res_question->fields["question_content"];
$survey_memberarea = $res_question->fields["memberarea"];
$survey_memberid = $res_question->fields["memberid"];
$survey_update_date = $res_question->fields["update_date"];
$survey_update_ipaddress = $res_question->fields["update_ipaddress"];
$survey_adddate = $res_question->fields["adddate"];
$survey_addipaddress = $res_question->fields["addipaddress"];

$tpl->assign("survey_lv00_type", $survey_lv00_type);//問卷編號
$tpl->assign("survey_start_date", $survey_start_date);//起始日期
$tpl->assign("survey_end_date", $survey_end_date);//終止日期
$tpl->assign("survey_title", $survey_title);//問卷標題

/*************** 輸出回答 ********************/
$strSql_respon = "SELECT *
                    FROM survey_respon 
                    WHERE responid = '".$pid."'";
$res_respon = SqlQuery1($strSql_respon);
$respon_serialid = $res_respon->fields["serialid"];
$respon_responid = $res_respon->fields["responid"];
$respon_lv00_type = $res_respon->fields["lv00_type"];
$respon_mbr_id = $res_respon->fields["mbr_id"];
$respon_mbr_name = $res_respon->fields["mbr_name"];
$respon_add_date = $res_respon->fields["add_date"];
$respon_add_ipaddress = $res_respon->fields["add_ipaddress"];
$respon_content = $res_respon->fields["content"];
$respon_add_from = $res_respon->fields["add_from"];
$respon_memberarea = $res_respon->fields["member_area"];
$respon_memberid = $res_respon->fields["member_id"];
$respon_update_date = $res_respon->fields["update_date"];
$respon_update_ipaddress = $res_respon->fields["update_ipaddress"];
$respon_check_status = ($res_respon->fields["check_status"] === "" || $res_respon->fields["check_status"] === "N") ? "N" : "Y";

$tpl->assign("responid", $respon_responid);
$tpl->assign("respon_mbr_id" , $respon_mbr_id);//會員ID
$tpl->assign("respon_add_date" , $respon_add_date);//新增日期
$tpl->assign("respon_add_ipaddress", $respon_add_from.", ".$respon_add_ipaddress);//新增位址
$tpl->assign("check_status", $respon_check_status);//後台人員觀看結果
$tpl->assign("memberArea", $respon_memberarea);//後台人員群組
$tpl->assign("memberID", $respon_memberid);//後台人員姓名
$tpl->assign("update_date", $respon_update_date);//後台人員更新日期
$tpl->assign("update_ipaddress", $respon_update_ipaddress);//後台人員更新位置


/**************** 用迴圈繪製DIV到HTML ****************/
//把問題json解析成各大題
$array_question_content = json_decode($survey_question_content);//變成各個array
//print_r($array_question_content);
//但json裡面有object檔案（有指定array編號項目會變成object）要變成array才能用
function stdObjectToArray($object){
    $array = json_decode(json_encode($object), true);
    return $array;
}
for($i=0; $i< count($array_question_content); $i++){
    $tpl->newBlock("QuestionGroup");//問題區塊起始
    
    $question_group_title = $array_question_content[$i][0];//各群組大標題
    $tpl->assign("question_group_title", $question_group_title);
    
    $question_group = $array_question_content[$i];//每個陣列存成一個變數
    for($j=1; $j< count($question_group); $j++){
        $question_question = $question_group[$j];//每個物件存成一個變數
        $array_question = stdObjectToArray($question_question);//每個物件轉成陣列，每一題的內容
        
        $tpl->newBlock("QuestionContent");
        $str_question_id = $array_question[0];
        $tpl->assign("question_id",$str_question_id);//題目編號
        $str_question_title = $array_question["title"];
        $tpl->assign("question_content", $str_question_title);//題目內容
        $str_question_name = $array_question["name"];
        $tpl->assign("question_name", $str_question_name);//題目名字
        
        //echo "題目選項:".$array_question["options"];//array
        //echo "題目選項值:".$array_question["values"];//array
        
        $str_question_type = $array_question["type"];
        $str_question = "";
        for($m = 0; $m< count($array_question["options"]); $m++){
            if($str_question_type === "radio"){
                $str_question .= "&emsp;<input type='radio' name='".$str_question_id."' value='".$array_question["values"][$m]."'>".$array_question["options"][$m]."";
            }else if($str_question_type === "checkbox"){
                $str_question .= "&emsp;<input type='checkbox' name='".$str_question_id."' value='".$array_question["values"][$m]."'>".$array_question["options"][$m]."<br>";
            }else if($str_question_type === "select"){
                $str_question0 .= "<option value='".$array_question["values"][$m]."'>".$array_question["options"][$m]."</option>";
                $str_question = "&emsp;<select name='".$str_question_id."'>.$str_question0.</select>";
            }else if($str_question_type === "text"){
                $str_question .= "&emsp;<input type='text' name='".$str_question_id."' style='width:60%;'>";
            }else{
                $str_question .= "&emsp;<textarea name='".$str_question_id."' ".$array_question["options"]."></textarea>";
            }
        }
        $tpl->assign("question",$str_question);
        $tpl->gotoBlock("_ROOT");
    }
    $tpl->gotoBlock("_ROOT");
}

/*************** 用迴圈把答案寫成js，再丟到HTML讓js去比對選擇選項和填資料 *******************/
//檢驗函式寫在HTML，所以這邊抓題目名字和答案陣列和類型
//要傳入的參數有 題目編號(name) 和題目答案(ans)
$array_respon = json_decode($respon_content);//變成各陣列
for($i=0; $i< count($array_respon); $i++){
    //print_r($array_respon[$i]);//每一個都是 stdClass Object
    $array_respon_typeAry = stdObjectToArray($array_respon[$i]);
    for($j=0; $j<count($array_respon_typeAry); $j++){
        $tpl->newBlock("QuestionAnswerGroup");//答案區塊起始
        $respon_qNum = $array_respon_typeAry[$j]["q_num"];//題目編號
        $respon_qAns = $array_respon_typeAry[$j]["q_ans"];//題目答案
        
        $tmp_aryStr = "";//要先把之前的洗乾淨，再重新加字串變成陣列所需文字
        if(gettype($respon_qAns) === "array"){
            for($k=0; $k<count($respon_qAns); $k++){
                $tmp_aryStr .= "'".$respon_qAns[$k]."', ";
            }
        }else{
            if($respon_qNum == "q0404"){
                if(strlen($respon_qAns)>15){
                    include("../survey_encode.php");
                    $decode = new EncodeAndDecode;
                    $decode_str = $decode->responDecode($respon_qAns, $respon_responid);
                    $tmp_aryStr = "'$decode_str'";
                }else{
                    $tmp_aryStr = "'".$respon_qAns."'";
                }
            }else{
                $tmp_aryStr = "'".$respon_qAns."'";    
            }
        }
        $functionStr = "checkValue('".$respon_qNum."', [".$tmp_aryStr."]);";
        $tpl->assign("answer_value_function", $functionStr);
    }
}

$tpl->gotoBlock("_ROOT");

$tpl->printToScreen();
?>