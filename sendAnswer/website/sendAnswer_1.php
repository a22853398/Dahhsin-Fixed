<?php
include('commonfile/config.inc.php');

  //$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );  
  $tpl->assign('mainurl', $config['mainurl'] );
  $tpl->assign('rewrite_url', $config['rewrite_url'] );

    $sql_answer_array = array('B808','B818','B881','B882','E154','E158','E174','E178','B908','B938','E621','B550','B447');

/*
 * 回傳 Array
 * 取得書名、書編、圖片路徑
 */    
function getProductInfo($array_prod_num){
    $list = array();
    for($i=0; $i<count($array_prod_num); $i++){
        //取得產品流水號
        $sql_prod_serialid = 
            "SELECT serialid, prod_num, prod_name
            FROM product_list
            WHERE prod_num = '".$array_prod_num[$i]."' ";
            //echo $sql_prod_serialid.'<br>';
        $res01 = sqlQuery1($sql_prod_serialid);
        //echo $res01.'<br>';serialid,prod_num,prod_name P1509040001dce2,E151,大家的日本語 初級Ⅰ 改訂版
        $prod_num = $res01->fields['prod_num'];
        //echo $prod_num;
        $serialid = $res01->fields['serialid'];
        $prod_name = $res01->fields['prod_name'];
            
        //取圖片檔名
        $sql_pic=
            "SELECT lv01_id, upfile1_name 
            FROM product_list_filelist 
            WHERE lv01_id = '" .$serialid. "' and dl01_type = '01' order by sortid limit 1";
        $res02 = sqlQuery1($sql_pic);
            $pic_name = $res02->fields['upfile1_name'];
            //echo $pic_name.'<br>';
            
            //圖片路徑
            $dirName = 'uploadfile/fileproduct_list';
            $path = $dirName.'/'.$serialid.'/'.'PRDPIC/'.$pic_name;
            //echo $path.'<br>';
            
        array_push($list,
            array(
                'prod_num' => $prod_num,
                'prod_name' => $prod_name,
                'prod_pic' => $path
            )
        );
    }
    return $list;
}
    
    $list_answer = getProductInfo($sql_answer_array);
    $tpl->assign('answer',$list_answer);


  $tpl->display('sendAnswer_1.shtm');
?>
