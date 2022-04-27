<?php
/* 加密字串的Class */
class EncodeAndDecode{
    /* 加密函式 */
    function responEncode($str, $responid){
        $key1_aes = SqlQuery1("SELECT var03 FROM xsys_defset WHERE var02 = 'AESKEY01'")->fields["var03"];
        $key2_crypt = SqlQuery1("SELECT var03 FROM xsys_defset WHERE var02 = 'crypt_key'")->fields["var03"];
        $str_base64encode = base64_encode("ABC".$str."abc");//加密的電話號碼再把它補個字串
        $str_urlencode = urlencode($str_base64encode);//加密的電話號碼再把它用網址編碼
        
        $responid = str_replace('R', "", $responid);//回覆編號去掉R，抓純數字
        $responid = intval($responid);
        $responid_remain = $responid % 4;
        $encodeStr = "";
        switch($responid_remain){//依據回覆編號不同，有不同的編碼方式
            case 1:
                $encodeStr = urlencode($key1_aes.$key2_crypt.$str_urlencode);
                break;
            case 2:
                $encodeStr = $key2_crypt.$str_urlencode.$key1_aes;
                break;
            case 3:
                $encodeStr = base64_encode($str_urlencode.$key1_aes.$key2_crypt);
                break;
            case 0:
                $encodeStr = $str_urlencode.$key2_crypt.$key1_aes;
                break;
        }
        return $encodeStr;
    }
    /* 解密函式 */
    function responDecode($encodeStr, $responid){
        $responid_num = str_replace('R', "", $responid);
        $decodeStr = "";
        switch(intval($responid_num) % 4){
            case 1:
                $decodeStr = urldecode($encodeStr);
                break;
            case 3:
                $decodeStr = base64_decode($encodeStr);
                break;
            default:
                $decodeStr = $encodeStr;
                break;
        }
        $key1_aes = SqlQuery1("SELECT var03 FROM xsys_defset WHERE var02 = 'AESKEY01'")->fields["var03"];
        $key2_crypt = SqlQuery1("SELECT var03 FROM xsys_defset WHERE var02 = 'crypt_key'")->fields["var03"];
        $tp_str = str_replace($key1_aes, "", $decodeStr);//取代一次再傳回去
        $tp_str = str_replace($key2_crypt, "", $tp_str);//再銷掉另一個字串，再傳回去
        $tp_str = urldecode($tp_str);
        $str_base64decode = base64_decode($tp_str);
        $answer = str_replace("ABC", "", $str_base64decode);
        $answer = str_replace("abc", "", $answer);
        
        return $answer;
    }
}
?>