<?php
include_once("../03lineNotify.php");
require_once("../commonfile/config_mang.inc.php");
require_once("PHPGangsta/GoogleAuthenticator.php");
include("00security_function.php");
include('PHPGangsta/00decryptFunction.php');
$token = "ZzoslRAaQlTX0Qt1mXgVv6BS6zHEUxTi57YEZ6pt0bf";//專門寄送驗證碼的群組

//產生google金鑰
function genarateGoogleSecret(){
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->createSecret();
}
//產生google驗證碼
function getVerifyCode($secret){
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->getCode($secret);
}
//驗證碼，回傳True False
function isVerifyCorrect($secret, $verify){
    $ga = new PHPGangsta_GoogleAuthenticator();
    return $ga->verifyCode($secret, $verify, 1);
}
$replaceStringList =array("select", "where", "update", "insert", "into", "delete", "drop", "from", "use", "--", "#", "$", "set");
$id = str_replace($replaceStringList, "", strip_tags($_GET["id"]));//後台帳號
$orderid = str_replace($replaceStringList, "", strip_tags($_GET["orderid"]));//訂單編號
$verifyCode = str_replace($replaceStringList, "", strip_tags($_GET["code"]));//驗證碼

//允許ID清單
$strSqlGetWebAccount = "SELECT mbr_id FROM web_account WHERE visible = 'Y'";
$resGetWebAccount = SqlQuery1($strSqlGetWebAccount);
$idAllowList = array();
while(!$resGetWebAccount->EOF){
    array_push($idAllowList, $resGetWebAccount->fields["mbr_id"]);
    $resGetWebAccount->MoveNext();
}
if(in_array($id, $idAllowList) === false){
    post_message_token("\n不在名單內帳號".
                        "\n請求IP：".$remote_ipaddress.
                        "\n要求帳號：".$id.
                        "\n訂單編號：".$orderid
            , $token);
    exit();        
}
//允許IP清單
$ipaddressAllowList = array();


//如果只有後台帳號和訂單編號代表第一次，要求發一個 verifycode
if($id != "" && $orderid != "" && $verifyCode == ""){
    $secret = genarateGoogleSecret();
    $oneCode = getVerifyCode($secret);
    //要寫入資料庫存起來
    $strSqlInsert = "INSERT INTO 00order_list_decode_request (request_account, request_ipaddress, request_orderid, add_date, google_secret, one_code)
                    VALUES('".$id."', '".$remote_ipaddress."', '".$orderid."', '".date("Y-m-d H:i:s")."', '".$secret."', '".$oneCode."')";
    $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
    $resInsert = SqlQuery1($strSqlInsert);
    $config['sql_link1']->CompleteTrans();//關閉transaction
    //發送訊息
    post_message_token("\n後台訂單要求看完整個資".
                    "\n請求IP：".$remote_ipaddress.
                    "\n要求帳號：".$id.
                    "\n訂單編號：".$orderid.
                    "\n驗證碼：".$oneCode
                    , $token);
}
else{
    //post_message_token("\n惡意請求！請求失敗".
    //                "\n請求IP：".$remote_ipaddress
    //                , $token);
}

//如果有verifyCode代表要進行驗證，要回傳 true / false
if($verifyCode != "" && $id != "" && $orderid != ""){
    $strSqlGetRequestLog = "SELECT google_secret, one_code FROM 00order_list_decode_request 
                            WHERE request_account = '".$id."'
                            AND request_orderid = '".$orderid."'
                            ORDER BY add_date DESC
                            LIMIT 1";
    $resGetRequestLog = SqlQuery1($strSqlGetRequestLog);
    $verifySecret = $resGetRequestLog->fields["google_secret"];
    if(isVerifyCorrect($verifySecret, $verifyCode)){
        $strSqlUpdateVerify = "UPDATE 00order_list_decode_request SET verify_result = 'Y'
                                WHERE request_account = '".$id."'
                                AND request_orderid = '".$orderid."'
                                AND google_secret = '".$verifySecret."'
                                AND one_code = '".$verifyCode."'";
        $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
        $resUpdateVerify = SqlQuery1($strSqlUpdateVerify);
        $config['sql_link1']->CompleteTrans();//關閉transaction
        post_message_token("驗證成功" ,$token);
        //要傳到前面的資料
        $sqlStrGetOrderInfo = "SELECT * FROM order_list WHERE orderid= '".$orderid."'";
        $resGetOrderInfo = SqlQuery1($sqlStrGetOrderInfo);
        //解密各欄位
        $gemail = $resGetOrderInfo->fields['gemail'];
        $gemailDecode = getDecodeData($gemail, $orderid, "gemail");
        $gtel = $resGetOrderInfo->fields['gtel'];
        $gtelDecode = getDecodeData($gtel, $orderid, "gtel");
        $gmobile = $resGetOrderInfo->fields['gmobile'];
        $gmobileDecode = getDecodeData($gmobile, $orderid, "gmobile");
        $gaddress2 = $resGetOrderInfo->fields['gaddress2'];
        $gaddress2Decode = getDecodeData($gaddress2, $orderid, "gaddress2");
        $gaddress3 = $resGetOrderInfo->fields['gaddress3'];
        $gaddress3Decode = getDecodeData($gaddress3, $orderid, "gaddress3");
        $resultArray = array(
                "verifyResult"=>true,
                "realMail" => base64_decode($gemailDecode),
                "realTel" => $gtelDecode,
                "realMobile" => $gmobileDecode,
                "realAddress2" => base64_decode($gaddress2Decode),
                "realAddress3" => base64_decode($gaddress3Decode)
            );
        echo json_encode($resultArray);
    }else{
        $strSqlUpdateVerify = "UPDATE 00order_list_decode_request SET verify_result = 'N'
                                WHERE request_account = '".$id."'
                                AND request_orderid = '".$orderid."'
                                AND google_secret = '".$verifySecret."'
                                AND one_code = '".$verifyCode."'";
        $config['sql_link1']->StartTrans();//更動SQL前必需要開啟transaction
        $resUpdateVerify = SqlQuery1($strSqlUpdateVerify);
        $config['sql_link1']->CompleteTrans();//關閉transaction
        post_message_token("驗證失敗" ,$token);
        echo false;
    }
}





?>