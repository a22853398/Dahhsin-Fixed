<?php
include 'commonfile/config.inc.php';

$lv01_type = strip_input('input,cut50', $_REQUEST['lv01_type']);
$prd_id    = strip_input('input,cut50', $_REQUEST['prd_id']);
$opt       = strip_input('input,cut50', $_REQUEST['opt']);

// if ($opt == 'revadd') {

//     include 'commonfile/reject_otherurl_get.php';

//     $pid     = strip_input('input,cut50', $_REQUEST['pid']);
//     $var01   = strip_input('input,cut100', $_REQUEST['var01']);
//     $var02   = strip_input('input,htmlspec,cut500', $_REQUEST['var02']);
//     $captcha = strip_input('input,cut10', $_POST['captcha']);

//     if (($_SESSION['session_login_checknum'] != strtoupper($captcha) || $_SESSION['session_login_checknum'] == '' || $captcha == '') && $opt == 'add') {
//         echo '驗證碼輸入錯誤!!';
//         exit;
//     }
//     $table_name = 'product_list_reviews';
//     //鎖定表列
//     //    $strSQL1="LOCK TABLES ".$table_name." WRITE; ";
//     //    $res = SqlQuery1($strSQL1);
//     //鎖定表列
//     //確認最後單號起始
//     $strSQL1 = "select SUBSTRING( serialid,1,1) as key01 from " . $table_name . " order by serialid desc ";
//     //echo $strSQL1.'<br />';
//     $res_getid = SqlQuery1($strSQL1);
//     if ($res_getid->RecordCount() == 0) {
//         $nextkey01 = 'A';
//     } else {
//         $nextkey01 = $res_getid->fields['key01'];
//     }

//     //確認最後單號
//     //確認最後單號
//     $rndstr  = create_rndid(32);
//     $nowdate = date('ymd', $NOW);
//     $strSQL1 = "select MAX(SUBSTRING( serialid,8,4)) as MAXID from " . $table_name . " where SUBSTRING(serialid,1,7) = '" . $nextkey01 . $nowdate . "' ";
//     //echo $strSQL1.'<br />';
//     $res_getid = SqlQuery1($strSQL1);
//     if ($res_getid->fields['MAXID'] >= 9999) {
//         $nextkey01 = chr(ord($nextkey01) + 1);
//         $strSQL1   = "select MAX(SUBSTRING( serialid,8,4)) as MAXID from " . $table_name . " where SUBSTRING(serialid,1,7) = '" . $nextkey01 . $nowdate . "' ";
//         //echo $strSQL1.'<br />';
//         $res_getid = SqlQuery1($strSQL1);
//     }
//     $nextval    = $res_getid->fields['MAXID'] + 1;
//     $nextval_id = '' . $nextkey01 . $nowdate . sprintf('%04s', $nextval) . substr($rndstr, -4);
//     $dirname    = $nextval_id . '/';
//     //確認最後單號
//     //解除鎖定表列
//     //    $strSQL1="UNLOCK TABLES; ";
//     //    $res = SqlQuery1($strSQL1);
//     //解除鎖定表列

//     $strSQL1 = "insert into " . $table_name . " (serialid,lv00_type,lv01_id,var01,var02,visible,visible_ipaddress,visible_date,visible_id,member_area,member_id,add_ipaddress,add_date) values(";
//     $strSQL1 .= "'" . $nextval_id . "',";
//     $strSQL1 .= "'REVIEWS',";
//     $strSQL1 .= "'" . $pid . "',";
//     $strSQL1 .= "'" . $var01 . "',";
//     $strSQL1 .= "'" . $var02 . "',";
//     $strSQL1 .= "'A',";
//     $strSQL1 .= "'',";
//     $strSQL1 .= "NULL,";
//     $strSQL1 .= "'',";
//     $strSQL1 .= "'" . $_SESSION['session_user_lv01_name'] . "',";
//     $strSQL1 .= "'" . $_SESSION['session_user_name'] . "',";
//     $strSQL1 .= "'" . $remote_ipaddress . "',";
//     $strSQL1 .= "'" . $sys_datetime . "')";
//     //echo $strSQL1.'<br />';
//     $result = SqlQuery1($strSQL1);

//     echo '謝謝您的心得分享!! 審核通過後，您的推薦內容將刊登於該書籍頁面。';

//     exit;
// }

//$tpl->assign('baseurl_href', '<base href= "'.$config['mainurl'].'/" ></base>' );
$tpl->assign('mainurl', $config['mainurl']);
$tpl->assign('rewrite_url', $config['rewrite_url']);
$tpl->assign('main_webname', $config['main_webname']);

$tpl->assign('lv01_type', $lv01_type);
$tpl->assign('prd_id', $prd_id);

$product_listpath = '../uploadfile/fileproduct_list/';
$subfolder01      = 'PRDPIC';

//確認節點 lv01_type
$strSQL1 = "select serialid,title01,title01_urlpath,parent_id from product_class_lv01 where title01_urlpath = '" . $lv01_type . "' and visible = 'Y' ";
//echo $strSQL1.'<br />';
$res = SqlQuery1($strSQL1);
$tpl->assign('parent_top_name', $res->fields['title01_urlpath']);
$lv01_id        = $res->fields['serialid'];
$lv01_parent_id = $res->fields['parent_id'];
$ck_path01      = $res->RecordCount();
//確認節點 lv01_type

//輸出相關書籍

if ($ck_path01 == 1) {

//取出所有路徑代碼
    $parent_idtemp = $lv01_id;
    $path_subdir   = '';
    $prd_idlist    = '';
    $prd_idold     = '';
    $deepnum       = 0;
    while ($parent_idtemp != '0') {
        $deepnum++;
        $strSQL1 = "select serialid,title01,title01_urlpath,parent_id from product_class_lv01 where serialid = '" . $parent_idtemp . "' and visible = 'Y' ";
        //echo $strSQL1.'<br />';
        $res = SqlQuery1($strSQL1);
        if ($res->RecordCount() == 1) {

            $parent_idtemp = $res->fields['parent_id'];
            if ($res->fields['parent_id'] == '0' || $deepnum == '2') {
                $path_subdir = $res->fields['title01'] . ' > ' . $path_subdir;
            } else {
                $path_subdir = '<a href="bookList.php?lv01_type=' . $res->fields['title01_urlpath'] . '">' . $res->fields['title01'] . '</a> > ' . $path_subdir;
            }
            $prd_idlist .= $res->fields['serialid'] . ',';
            if ($lv01_id == $res->fields['serialid']) {
                $prd_idold = $res->fields['serialid'];
            }
            if ($res->fields['parent_id'] == '0') {
                $tpl->assign('parent_top_name', $res->fields['title01_urlpath']);
                $parent_top_urlpath = $res->fields['title01_urlpath'];
                $parent_top         = $res->fields['serialid'];
            }

        } else {
            $parent_idtemp = 0;
        }
    }
//取出所有路徑代碼
    $tpl->assign('path_subdir', substr($path_subdir, 0, -3));
    $tpl->assign('parent_top', $parent_top);
    $config['parent_top_urlpath'] = $parent_top_urlpath;

//輸出產品分類
    $list    = array();
    $strSQL1 = "select serialid,title01,title01_urlpath,parent_id from product_class_lv01 where serialid = '" . $lv01_id . "' and visible = 'Y' order by sortid ";
    //echo $strSQL1.'<br />';
    $res = SqlQuery1($strSQL1);
    //echo $res->RecordCount();
    //while(!$res->EOF){
    //輸出輸出產品內容
    $lv02_array = array();
    $strSQL1    = "select b.serialid,b.prod_num,b.prod_name,b.prod_name_origin,b.prod_desc01,b.prod_price,b.prod_priceoffer
				 	from product_list_class01 as a left join product_list as b on a.lv01_id = b.serialid
				 	where 1=1 and a.var01 in ( select serialid from product_class_lv01 where serialid = '" . $res->fields['serialid'] . "' and visible = 'Y' )
				 	and b.visible = 'Y' order by b.sortid limit 12 ";
    //echo $strSQL1.'<br />';
    $reshot = SqlQuery1($strSQL1);
    //echo $reshot->RecordCount();
    while (!$reshot->EOF) {

        //輸出產品圖片
        $dirname = $reshot->fields['serialid'] . '/' . $subfolder01 . '/';
        $strSQL1 = "select upfile1_name from product_list_filelist where lv01_id = '" . $reshot->fields['serialid'] . "' and dl01_type = '01' order by sortid limit 1 ";
        //echo $strSQL1.'<br />';
        $resfile = SqlQuery1($strSQL1);
        //輸出產品圖片

        array_push($lv02_array,
            array(
                'lv02_serialid'         => $reshot->fields['serialid'],
                'lv02_prod_num'         => $reshot->fields['prod_num'],
                'lv02_prod_name'        => $reshot->fields['prod_name'],
                'lv02_prod_name_origin' => $reshot->fields['prod_name_origin'],
                'lv02_prod_urlpath'     => $reshot->fields['prod_urlpath'],
                'lv02_prod_desc01'      => $reshot->fields['prod_desc01'],
                'lv02_prod_price'       => $reshot->fields['prod_price'],
                'lv02_prod_priceoffer'  => $reshot->fields['prod_priceoffer'],
                'lv02_dir'              => $product_listpath . $dirname,
                'lv02_color'            => $color_tmp01,
                'lv02_upfile1'          => $resfile->fields['upfile1_name'],
            )
        );

        $reshot->MoveNext();
    }
    //輸出產品內容

    $list[] = array(
        'lv01_serialid'        => $res->fields['serialid'],
        'lv01_title01'         => $res->fields['title01'],
        'lv01_title01_urlpath' => $res->fields['title01_urlpath'],
        'lv01_parent_id'       => $res->fields['parent_id'],
        'lv02data'             => $lv02_array,
    );

    //$res->MoveNext();
    //}

    $tpl->assign('lv01data', $list);
    //print_r($list);
    unset($list);

//輸出產品分類

}

//輸出相關書籍

//取出產品資料
$strSQL1 = "select serialid,prod_num,prod_name,prod_name_origin,
	prod_author,prod_translator,press_author,press,press_date,re_press_date,barcode01,barcode02,barcode03,
	prod_lang,prod_spec,prod_degree,prod_desc01,prod_desc02,prod_price,prod_priceoffer,prod_weight,prod_stock,prod_stocklimit,visible,download_pwd
	from product_list where prod_num = '" . $prd_id . "' and visible in ('Y','N1','N2') and ((news_end_date >= '" . date("Y/m/d") . "') and (news_date <= '" . date("Y/m/d") . "') ) ";
//echo $strSQL1.'<br />';
$res         = SqlQuery1($strSQL1);
$ck_detail01 = $res->RecordCount();

//確認折扣館
$dis_num   = 1;
$dis_nck01 = 'N';
$strSQL1   = "select dis_num
						 	  from discount_event01_sublist01 as a left join discount_event01 as b on b.serialid = a.lv01_id
						 	  where b.lv00_type = 1 and ((b.news_end_date >= '" . date("Y/m/d") . "') and (b.news_date <= '" . date("Y/m/d") . "') ) and b.visible = 'Y'
						 	  and a.var01 = '" . $res->fields['prod_num'] . "' ";
//echo $strSQL1.'<br />';
$resdis = SqlQuery1($strSQL1);
if ($resdis->RecordCount() == 1) {
    $dis_num   = $resdis->fields['dis_num'];
    $dis_nck01 = 'Y';
}
//確認折扣館

//折扣金額確認
$prod_priceoffer = $res->fields['prod_priceoffer'];
if ($dis_nck01 == 'Y') {
    $prod_priceoffer = round($res->fields['prod_price'] * $dis_num);
}
//折扣金額確認

$prod_serialid = $res->fields['serialid'];
$prod_num      = $res->fields['prod_num'];
$download_pwd  = $res->fields['download_pwd'];
$tpl->assign('prod_serialid', $res->fields['serialid']);

$tpl->assign('prod_num', $res->fields['prod_num']);
$tpl->assign('prod_name', $res->fields['prod_name']);
$tpl->assign('prod_name_origin', $res->fields['prod_name_origin']);
$tpl->assign('prod_author', $res->fields['prod_author']);
$tpl->assign('prod_translator', $res->fields['prod_translator']);

$tpl->assign('press_author', $res->fields['press_author']);
$tpl->assign('press', $res->fields['press']);
$tpl->assign('press_date', $res->fields['press_date']);
$tpl->assign('re_press_date', $res->fields['re_press_date']);

$tpl->assign('barcode01', $res->fields['barcode01']);
$tpl->assign('barcode02', $res->fields['barcode02']);
$tpl->assign('barcode03', $res->fields['barcode03']);

$tpl->assign('prod_lang', $res->fields['prod_lang']);
$tpl->assign('prod_spec', $res->fields['prod_spec']);
$tpl->assign('prod_degree', $res->fields['prod_degree']);
$tpl->assign('prod_desc01', $res->fields['prod_desc01']);
$tpl->assign('prod_desc02', $res->fields['prod_desc02']);
$tpl->assign('prod_price', $res->fields['prod_price']);
$tpl->assign('prod_priceoffer', $prod_priceoffer);
$tpl->assign('prod_weight', $res->fields['prod_weight']);
$tpl->assign('prod_stock', $res->fields['prod_stock']);
$tpl->assign('prod_stocklimit', $res->fields['prod_stocklimit']);
$tpl->assign('visible', $res->fields['visible']);

if ($ck_detail01 == 1) {

//輸出搜尋分類
    $strSQL1 = "select var01,var02 from product_list_search01 where lv01_id = '" . $prod_serialid . "' order by sortid ";
    //echo $strSQL1.'<br />';
    $res = SqlQuery1($strSQL1);
    while (!$res->EOF) {
        $list[] = array(
            'var01' => $res->fields['var01'],
            'var02' => $res->fields['var02'],
        );
        $res->MoveNext();
    }
    //print_r($list);
    $tpl->assign('prdlist01_search01', $list);
    unset($list);
//輸出輸出搜尋分類

//取出產品顏色圖片
    $dirname = $prod_serialid . '/' . $subfolder01 . '/';
    $strSQL1 = "select serialid,upfile1_title01,upfile1_base,upfile1_name from product_list_filelist where lv01_id = '" . $prod_serialid . "' and dl01_type = '01' order by sortid limit 1 ";
    //echo $strSQL1.'<br />';
    $res = SqlQuery1($strSQL1);
    while (!$res->EOF) {
        $list[] = array(
            'serialid'        => $res->fields['serialid'],
            'upfile1_title01' => $res->fields['upfile1_title01'],
            'upfile1_base'    => $res->fields['upfile1_base'],
            'upfile1_dir'     => $product_listpath . $dirname,
            'upfile1_name'    => $res->fields['upfile1_name'],
        );
        $res->MoveNext();
    }
    //print_r($list);
    $tpl->assign('prdlist01_pic01', $list);
    unset($list);
//取出產品顏色圖片

}
//取出產品資料

//取出產品Specs 書籍介紹
$strSQL1 = "select serialid,prod_desc02 from product_list where serialid = '" . $prod_serialid . "' ";
//echo $strSQL1.'<br />';
$res = SqlQuery1($strSQL1);
$tpl->assign('prod_desc02', $res->fields['prod_desc02']);
//取出產品Specs 書籍介紹

//確認點讀筆音檔數量
$strSQL1 = "select * from product_list_filelist_penvoice where lv01_id = '" . $prod_serialid . "' ";
//echo $strSQL1.'<br />';
$respen = SqlQuery1($strSQL1);
if ($respen->RecordCount() > 0) {
    $tpl->assign('penck01', 'Y');
} else {
    $tpl->assign('penck01', 'N');
}

//確認點讀筆音檔數量

//確認書籍音檔數量
$down_listpath = 'uploadfile/xbookvoice/';
$strSQL1       = "select * from product_list_filelist_bookvoice where lv01_id = '" . $prod_serialid . "' ";
//echo $strSQL1.'<br />';
$resbook = SqlQuery1($strSQL1);
if ($resbook->RecordCount() > 0) {
    if ($download_pwd != "") {
        $tpl->assign('bookck01', 'S');
    } else {
        $tpl->assign('bookck01', 'Y');
    }
} else {
    $tpl->assign('bookck01', 'N');
}

$tpl->assign('down_listpath', $down_listpath);
$tpl->assign('book_prod_num', $prod_num);
$tpl->assign('book_upfile1_title01', $resbook->fields['upfile1_title01']);
$tpl->assign('book_upfile1_name', $resbook->fields['upfile1_name']);
//確認書籍音檔

//確認書籍音檔數量

/*
//輸出瀏覽紀錄
if (isset($_COOKIE['vwbook_list']) && substr($_COOKIE['vwbook_list'], 0, -1) != '') {

$list = array();
//輸出輸出產品內容
$lv02_array = array();
$strSQL1    = "select serialid,prod_num,prod_name,prod_desc01,prod_price,prod_priceoffer
from product_list
where prod_num in ('" . str_replace(",", "','", substr($_COOKIE['vwbook_list'], 0, -1)) . "') and visible = 'Y' ";
//echo $strSQL1.'<br />';
$reshot = SqlQuery1($strSQL1);
//echo $reshot->RecordCount();
while (!$reshot->EOF) {

//輸出節點
$strSQL1 = "select var01 from product_list_class01 where lv01_id = '" . $reshot->fields['serialid'] . "' order by sortid limit 1 ";
//echo $strSQL1.'<br />';
$res01   = SqlQuery1($strSQL1);
$strSQL1 = "select title01_urlpath from product_class_lv01 where serialid = '" . $res01->fields['var01'] . "' order by sortid limit 1 ";
//echo $strSQL1.'<br />';
$res02 = SqlQuery1($strSQL1);
//輸出節點

//輸出產品圖片
$dirname = $reshot->fields['serialid'] . '/' . $subfolder01 . '/';
$strSQL1 = "select upfile1_name from product_list_filelist where lv01_id = '" . $reshot->fields['serialid'] . "' and dl01_type = '01' order by sortid limit 1 ";
//echo $strSQL1.'<br />';
$resfile = SqlQuery1($strSQL1);
//輸出產品圖片

$list[] = array(
'lv01_serialid'     => $reshot->fields['serialid'],
'lv01_urlpath'      => $res02->fields['title01_urlpath'],
'lv01_prod_num'     => $reshot->fields['prod_num'],
'lv01_prod_name'    => $reshot->fields['prod_name'],
'lv01_prod_urlpath' => $reshot->fields['prod_urlpath'],
'lv01_prod_desc01'  => $reshot->fields['prod_desc01'],
'lv01_dir'          => $product_listpath . $dirname,
'lv01_upfile1'      => $resfile->fields['upfile1_name'],
);

$reshot->MoveNext();
}
//輸出產品內容

$tpl->assign('vwlogdata', $list);
//print_r($list);
unset($list);

}
//輸出瀏覽紀錄
 */

$tpl->display('bookIntro.shtm');
?>