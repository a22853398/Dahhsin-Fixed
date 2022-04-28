<?php

//來源傳過來的資料把它接收起來
$comefrom = $_POST['comefrom'];
$message = $_POST['name'];


define('LINE_API_URL', 'https://notify-api.line.me/api/notify');
if($comefrom === "" || $comefrom === null){
    define('LINE_API_TOKEN','XXXXXXXXXXXXX');//預設其他
}else{
    define('LINE_API_TOKEN','XXXXXXXXXXXXX');//預設其他
}


function post_message($message){
    $data_line = http_build_query(['message'=>$message],'','&');
    
    $options = [
        'http'=> [
            'method'=>'POST',
            'header'=>'Authorization: Bearer ' . LINE_API_TOKEN . "\r\n"
                    . "Content-Type: application/x-www-form-urlencoded\r\n"
                    . 'Content-Length: ' . strlen($data_line)  . "\r\n" ,
            'content' => $data_line,
            ]
        ];
    
    $context_line = stream_context_create($options);
    $resultJson = file_get_contents(LINE_API_URL, true, $context_line);
    $resultArray = json_decode($resultJson, true);
    if($resultArray['status'] != 200)  {
        return false;
    }
    return true;
}

function post_message_token($message, $token){
    $data_line = http_build_query(['message'=>$message],'','&');
    
    $options = [
        'http'=> [
            'method'=>'POST',
            'header'=>'Authorization: Bearer '.$token."\r\n"
                    . "Content-Type: application/x-www-form-urlencoded\r\n"
                    . 'Content-Length: ' . strlen($data_line)  . "\r\n" ,
            'content' => $data_line,
            ]
        ];
    
    $context_line = stream_context_create($options);
    $resultJson = file_get_contents('https://notify-api.line.me/api/notify', false, $context_line);
    $resultArray = json_decode($resultJson, true);
    if($resultArray['status'] != 200)  {
        return false;
    }
    return true;
}

/* curl的方式可以避免取不到https的錯誤 */
function post_message_curl_token($message_str, $token){
    
    $headers = array(
        'Content-Type: multipart/form-data',
        'Authorization: Bearer '.$token
    );
    
    $message = array(
        'message' => $message_str
    );
    
    $ch = curl_init();
    curl_setopt($ch , CURLOPT_URL , LINE_API_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1); // 不直接出現回傳值
    $result = curl_exec($ch);
    curl_close($ch);
}

if($comefrom == "" || $comefrom == null){
    //header("Location: https://www.dahhsin.com.tw");
    //exit();
    return false;
}else{
    post_message("\n被點標題：".$message."\n來源頁面：".$comefrom);
}


?>