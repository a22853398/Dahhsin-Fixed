<?php
include('commonfile/config.inc.php');

  //$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );  
    $tpl->assign('mainurl', $config['mainurl'] );
    $tpl->assign('rewrite_url', $config['rewrite_url'] );



    //抓取資料庫資料
    $sqlStr = "SELECT * FROM saiyou WHERE visible = 'Y' ORDER BY sortid ASC";
    $respon = SqlQuery1($sqlStr);
    $list = array();
    while(!$respon->EOF){
        $list[] =  
            array(
                'serialid' => $respon->fields['serialid'],
                'sortid' => $respon->fields['sortid'],
                'depart01' => $respon->fields['depart01'],
                'title01' => $respon->fields['title01'],
                'content01' => $respon->fields['content01'],
                'requirement01' => $respon->fields['requirement01'],
                'method01' => $respon->fields['method01'],
                'place01' => $respon->fields['place01'],
                'worktime01' => $respon->fields['worktime01'],
                'salary01' => $respon->fields['salary01'],
                'other01' => $respon->fields['other01'],
                'add_date' => $respon->fields['add_date'],
                'update_date' => $respon->fields['update_date'],
            )
        ;
        $respon->MoveNext();
    }
    $tpl->assign('saiyouList', $list);
    //print_r($list);

    $tpl->display('saiyou.shtm');
?>
