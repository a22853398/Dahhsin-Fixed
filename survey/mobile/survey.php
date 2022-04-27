<?php
include 'commonfile/config.inc.php';

//$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );
$tpl->assign('mainurl', $config['mainurl']);
$tpl->assign('rewrite_url', $config['rewrite_url']);



$bool_loginStatus = (isset($_SESSION['session_webmbr_login_id']) && $_SESSION['session_webmbr_login_id'] != '');//登入狀態
$tpl->assign("loginStatus", $bool_loginStatus);
$mbr_email = $_SESSION["session_webmbr_login_id"];
$tpl->assign("mbr_email", $mbr_email);

//計算有效問卷數是不是一個而已
    $today = date("Y-m-d");
    $strSql_ableSurvey = "SELECT * FROM survey_article WHERE start_date <= '".$today."' AND end_date >= '".$today."' AND visible = 'Y'";
    $count_ableSurvey = GetQueryValueCount1($strSql_ableSurvey, "");//計算幾筆, integer
    $tpl->assign("count_ableSurvey", $count_ableSurvey);
    if($count_ableSurvey === 1){
        $survey_no = SqlQuery1($strSql_ableSurvey)->fields["lv00_type"];//$survey_no = "S21110300001";
    }else{
        $noAbleSurvey = "<style>
                            .surveyThankyou{
                                text-align: center;
                                font-size: 1.2rem;
                                font-weight: bold;
                                margin: 5% 0% 2% 0%;
                            }
                        </style>
                        <div class='surveyThankyou'>
                            目前沒有問卷！<br>
                        </div>
                        <button class='btn btn-pink' id='goToMember'>會員專區</button><br>
                        <script>
                            document.getElementById('goToMember').addEventListener('click', function(){
                                window.location = '".$config['mainurl']."/member_loginOk.php';
                            });
                        </script>";
        $tpl->assign("noAbleSurvey", $noAbleSurvey);                    
    }

/* 沒有登入狀態的話，顯示按鈕請客人登入，登入的話取資料 */
    if($bool_loginStatus){
        /* 填寫過的客人不給他填寫第二次，在html那邊判斷 */
        $strSQL_hadRespon = "SELECT * FROM survey_respon WHERE mbr_id = '".$mbr_email."' AND lv00_type= '".$survey_no."'";
        $count_hadRespon = GetQueryValueCount1($strSQL_hadRespon, "");//計算幾筆, integer
        $tpl->assign('count_hadRespon', $count_hadRespon);
        $thankyou_respon = "<style>
                                .surveyThankyou{
                                    text-align: center;
                                    font-size: 1.2rem;
                                    font-weight: bold;
                                    margin: 5% 0% 2% 0%;
                                }
                            </style>
                            <div class='surveyThankyou'>
                                感謝您抽空填寫問卷！<br>
                            </div>
                            <button class='btn btn-pink' id='goToMember'>會員專區</button><br>
                            <script>
                                document.getElementById('goToMember').addEventListener('click', function(){
                                    window.location = '".$config['mainurl']."/member_loginOk.php';
                                });
                            </script>
                            ";
            $tpl->assign('thankyou_respon', $thankyou_respon);
    }else{
        $login_button = "<style>
                            .surveyLogin{
                                text-align: center;
                                font-size: 1.2rem;
                                font-weight: bold;
                                margin: 5% 0% -3% 0%;
                            }
                        </style>
                        <div class='surveyLogin'>
                            填寫問卷之前，請您先登入<br>
                        </div>
                        <br>
                        <button class='btn btn-pink' id='memberLogin'>會員登入</button><br>
                        <script>
                            document.getElementById('memberLogin').addEventListener('click', function(){
                                window.location = '".$config['mainurl']."/member_login.php';
                            });
                        </script>
                        ";
        $tpl->assign('login_button', $login_button);
    }



/**************** 取得有效問卷內容 ***************/
$strSql_question = "SELECT *, 
                    DATE_FORMAT(start_date,'%Y-%m-%d') AS start_date_format, 
                    DATE_FORMAT(end_date,'%Y-%m-%d') AS end_date_format 
                    FROM survey_article 
                    WHERE lv00_type='".$survey_no."'";
$res_question = SqlQuery1($strSql_question);
$survey_serialid = $res_question->fields["serialid"];
$survey_lv00_type = $res_question->fields["lv00_type"];
$survey_start_date = $res_question->fields["start_date_format"];
$survey_end_date = $res_question->fields["end_date_format"];
$survey_title = $res_question->fields["title"];
$survey_subtitle = $res_question->fields["subtitle"];
$survey_subscribe = $res_question->fields["subscribe"];
$tpl->assign("survey_subscribe", $survey_subscribe);
$survey_question_content = $res_question->fields["question_content"];
$survey_memberarea = $res_question->fields["memberarea"];
$survey_memberid = $res_question->fields["memberid"];
$survey_update_date = $res_question->fields["update_date"];
$survey_update_ipaddress = $res_question->fields["update_ipaddress"];
$survey_adddate = $res_question->fields["adddate"];
$survey_addipaddress = $res_question->fields["addipaddress"];

/**************** 用迴圈從到array，再繪製到HTML ****************/
//把問題json解析成各大題
$array_question_content = json_decode($survey_question_content);//變成各個array
//print_r($array_question_content);
//但json裡面有object檔案（有指定array編號項目會變成object）要變成array才能用
function stdObjectToArray($object){
    $array = json_decode(json_encode($object), true);
    return $array;
}
$all_question_ary = array();
for($i=0; $i< count($array_question_content); $i++){
    $ary_question_group = array();
    $question_group_title = $array_question_content[$i][0];//各群組大標題
    $ary_question_group += array("question_group_title" => $question_group_title);
    $ary_question_content = array();//每一群組的各個問題匯聚在一起的陣列
    $question_group = $array_question_content[$i];//每個陣列存成一個變數
    for($j=1; $j< count($question_group); $j++){
        $question_question = $question_group[$j];//每個物件存成一個變數
        $array_question = stdObjectToArray($question_question);//每個物件轉成陣列，每一題的內容
        
        $str_question_id = $array_question[0];//題目編號
        $str_question_title = $array_question["title"];//題目內容
        $str_question_name = $array_question["name"];//題目名字
        //"題目選項:".$array_question["options"];//array
        //"題目選項值:".$array_question["values"];//array
        
        $str_question_type = $array_question["type"];//題目類型
        $str_question = "";//題目選項
        for($m = 0; $m< count($array_question["options"]); $m++){
            if($str_question_type === "radio"){
                $str_question .= "<p>&emsp;<input type='radio' name='".$str_question_name."' value='".$array_question["values"][$m]."'>&emsp;".$array_question["options"][$m]."</p>";
            }else if($str_question_type === "checkbox"){
                $str_question .= "<p>&emsp;<input type='checkbox' name='".$str_question_name."' value='".$array_question["values"][$m]."'>&emsp;".$array_question["options"][$m]."</p>";
            }else if($str_question_type === "select"){
                $str_question0 .= "<option value='".$array_question["values"][$m]."'>".$array_question["options"][$m]."</option>";
                $str_question = "<select name='".$str_question_name."'>.$str_question0.</select>";
            }else if($str_question_type === "text"){
                $str_question .= "<input type='text' name='".$str_question_name."' class='form-control' maxlength='40'>";
            }else{
                $str_question .= "&emsp;<textarea name='".$str_question_name."' ".$array_question["options"]."></textarea>";
            }
        }
        $single_question_content = array(
                "question_id" => $str_question_id,
                "question_title" => $str_question_title,
                "question_name" => $str_question_name,
                "question_type" => $str_question_type,
                "question_string" => $str_question
            );//每一題存成一個陣列
        array_push($ary_question_content, $single_question_content);
    }
    $ary_question_group += array("question_group_content" => $ary_question_content);  
    array_push($all_question_ary, $ary_question_group);
}
$tpl->assign("all_question_ary", $all_question_ary);
//print_r($all_question_ary);
$tpl->display('survey.shtm');
?>
