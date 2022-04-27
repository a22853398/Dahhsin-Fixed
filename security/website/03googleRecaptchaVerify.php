<?php 
//post_message("test");//不用include直接用就好(可能是再同一層的關係)
class GoogleRecaptcha{
    
    function googleVerify($response){
        $verify_answer = false;
        $url = "https://www.google.com/recaptcha/api/siteverify";//送到google做驗證的網址
        $siteKey = "6LdGyskaAAAAAKaDo31mJolZ2-aP7ysqSkpUprRQ";//網頁端key
        $secretKey = "6LdGyskaAAAAADxBdUy2NVKAL0vR5GTtJtfZ94dl";//server端key
        
        //把結果字串和server key存成陣列
        $data = array(
	        'secret' => $secretKey,
	        'response' => $response
        );
        //把要傳到google的資料存成陣列，把上面的data產生一串http字串
        $options = array(
	        'http' => array (
		        'method' => 'POST',
		        'content' => http_build_query($data)
            )
        );
        //把options製作成一串文字
        $context  = stream_context_create($options);
        //把option丟給google做驗證
        $verify = file_get_contents($url, false, $context);
        
        //回傳回來的字串，用json_decode解析
        $captcha_success = json_decode($verify);//取出傳回的jason檔裡有success變數是成功或失敗
        
        //post_message_token("\n來源：".$_SERVER["HTTP_REFERER"]."\n".$response."\n布林：".$captcha_success->success, "4ezaDEmoJO09LDuQUV03ECpXEqtHCYwwpjS23kibCVr");
        
        if($captcha_success -> success === true){
            $verify_answer = true;
        }else if($captcha_success -> success === false){
            $verify_answer = false;
        }
        return $verify_answer;
    }
}
//include("03lineNotify.php");
function verifyProcess($response, $linemsg){
    $googleObject = new GoogleRecaptcha();
    if($googleObject->googleVerify($response) === true){
        //post_message("驗證成功".$linemsg);
    }else{
        //post_message("驗證失敗".$linemsg);
        redirect();
    }
}

?>
