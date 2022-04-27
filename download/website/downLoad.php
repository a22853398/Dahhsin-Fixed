<?php
include('commonfile/config.inc.php');

$today = date("Y-m-d", strtotime(today));//今天日期

$searchkeydl01 = strip_input('input,cut50',$_REQUEST['searchkeydl01']);

$tpl->assign('mainurl', $config['mainurl'] );
$tpl->assign('rewrite_url', $config['rewrite_url'] );

$page      = strip_input('inputadm',$_REQUEST['page']);
$lv01_type = strip_input('input,cut50',$_REQUEST['lv01_type']);
$mu_type   = strip_input('input,cut50',$_REQUEST['mu_type']);

$tpl->assign('lv01_type', $lv01_type );

$down_listpath = "";
/* 其他類別的輸出目錄+音檔 */
if($mu_type == 'ot'){
//輸出產品音檔
    $list = array();
    if($lv01_type == 'OT01'){
        $strSQL1 = "select a.*
                    from ot_voice as a left join ot_voice_lv01 as b on a.lv01_type = b.serialid
	                where a.visible != 'N' and b.visible != 'N'
	                order by a.serialid ";
    }else{
        $strSQL1 = "select a.*
	                from ot_voice as a left join ot_voice_lv01 as b on a.lv01_type = b.serialid
	                where a.visible != 'N' and b.visible != 'N' and b.title02 = '".$lv01_type."'
	                order by a.serialid ";
    }
    //echo $strSQL1.'<br />';
    $res = SqlQuery1($strSQL1);
    //echo $res->RecordCount();
    
    $icount = 0;//計算點讀筆
    $icount_cd = 0;//計算CD音檔數量，沒用到
    while(!$res->EOF){
        /** 點讀筆音檔 START **/
        $lv02_array = array();
	    $strSQL1 = "select upfile1_title01,upfile1_name,add_date,upfile1_base
	                from ot_voice_filelist
	                where lv01_id = '".$res->fields['serialid']."' order by sortid ";
	    //echo $strSQL1.'<br />';
	    $reshot = SqlQuery1($strSQL1);
	    //echo $reshot->RecordCount();
	    $subtotal = $reshot->RecordCount();

		while(!$reshot->EOF){
			$down_listpath = 'uploadfile/otherpenvoice';
			array_push($lv02_array,
			    array(
				    'lv02_upfile1_title01'  => $reshot->fields['upfile1_title01'],
				    'lv02_upfile1_name'     => $reshot->fields['upfile1_name'],
				    'lv02_upfile1_nameVW'   => $reshot->fields['upfile1_title01'],
				    'lv02_upfile1_base'     => $reshot->fields['upfile1_base'],
				    'lv02_add_date'         => date("Y-m-d",strtotime($reshot->fields['add_date'])),
				    'lv02_file_src'         => $down_listpath,
				)
			);
			$reshot->MoveNext();
		}
		//輸出產品內容
        /** 點讀筆音檔 END **/

        /** CD音檔 START **/
        //CD音檔
        $lv02_array_cd = array();
        $strSQL4 = "select 
	                    lv01_id,
	                    upfile1_title01, 
	                    upfile1_name, 
	                    add_date,
	                    upfile1_base
	                from product_list_filelist_bookvoice
	                where 
	                    lv01_id = '".$res->fields['serialid']."' order by sortid";
	    //echo $strSQL4.'<br>';
        $rescd = SqlQuery1($strSQL4);
        $subtotal2 = $rescd->RecordCount();
        
        while(!$rescd->EOF){
            $down_listpath = 'uploadfile/xbookvoice';
            array_push($lv02_array_cd,
                array(
                    'lv02_upfile1_title01'  => $rescd->fields['upfile1_title01'],
			        'lv02_upfile1_name'     => $rescd->fields['upfile1_name'],
			        'lv02_upfile1_nameVW'   => $rescd->fields['upfile1_title01'],
			        'lv02_upfile1_base'   => $rescd->fields['upfile1_base'],
			        'lv02_add_date'         => date("Y-m-d",strtotime($rescd->fields['add_date'])),
			        'lv02_file_src'         => $down_listpath,
                )
            );
            //print_r($lv02_array_cd);
            $rescd->MoveNext();
        }
        /** CD音檔 END **/
	    
	    /** 產品圖片 START **/
        //產品圖片
        $lv02_array_pic = array();
	    $strSQL3="select lv01_id, upfile1_name from product_list_filelist where lv01_id = '" . $res->fields['serialid'] . "' and dl01_type = '01' order by sortid limit 1";
	    //echo $strSQL1.'<br />';
	    $respic = SqlQuery1($strSQL3);
	    
	    $product_listpath = 'uploadfile/fileproduct_list/';
        $subfolder01      = '/PRDPIC';
	    while(!$respic->EOF){
		    array_push($lv02_array_pic,
			    array(
			        'lv02_upfile1_name'     => $respic->fields['upfile1_name'],
			        'lv02_pic_fold'         => $product_listpath.$respic->fields['lv01_id'].$subfolder01
			    )
			);
			$respic->MoveNext();
		}
        /** 產品圖片 END **/


        /** 有檔案的話寫入陣列 **/
        if($subtotal > 0){
            $icount++;
            $tpl->assign('icount', $icount);
            $list[]= array(
                'lv01_serialid'         => $res->fields['serialid'],
  	            'lv01_prod_num'         => $res->fields['title02'],
	            'lv01_prod_name'        => $res->fields['title01'],
                'lv01_prod_name_origin' => $res->fields['title01'],
                'lv02data'              => $lv02_array,
                'lv02data_pic'          => $lv02_array_pic,
                'lv02data_cd'           => $lv02_array_cd,
	        );
	      }
        /** 有檔案的話寫入陣列 END **/
		$res->MoveNext();
	}
    
    if($icount > 0 || $icount_cd > 0){
        $tpl->assign('lv01data', $list);
    }
    //print_r($list);
    unset($list);
    
}else{
    /** 確認節點 lv01_type **/
    if($lv01_type == ''){
        $strSQL1="select serialid,title01,title01_urlpath,parent_id from product_class_lv01 where parent_id = 0 and visible != 'N' order by sortid limit 1";
    }else{
        $strSQL1="select serialid,title01,title01_urlpath,parent_id from product_class_lv01 where title01_urlpath = '".$lv01_type."' and visible != 'N' ";
    }
	//echo $strSQL1.'<br />';
	$res = SqlQuery1($strSQL1);

	$tpl->assign('lv01_type_name', $res->fields['title01']);
	$lv01_id        = $res->fields['serialid'];
	$lv01_parent_id = $res->fields['parent_id'];
	$ck_path01 = $res->RecordCount();
    /** 確認節點 lv01_type **/

    if($ck_path01 == 1){
        //取出所有路徑代碼
        $parent_idtemp = $lv01_id;
        $path_subdir = '';
        $prd_idlist = '';
        $prd_idold = '';
        $deepnum = 0;
        while($parent_idtemp != '0'){
	        $deepnum++;
	        $strSQL1="select serialid,title01,title01_urlpath,parent_id from product_class_lv01 where serialid = '".$parent_idtemp ."' and visible != 'N' ";
	        //echo $strSQL1.'<br />';
	        $res = SqlQuery1($strSQL1);
	        
	        if($res->RecordCount() == 1){
	            $parent_idtemp = $res->fields['parent_id'];
	            if($res->fields['parent_id'] == '0' || $deepnum == '2'){
                    $path_subdir = $res->fields['title01'].' > '.$path_subdir;
	            }else{
	                $path_subdir = '<a href="product/Lines/'.$res->fields['title01_urlpath'].'/">'.$res->fields['title01'].'</a> > '.$path_subdir;
	            }
	            $prd_idlist .= $res->fields['serialid'].',';
	            if($lv01_id == $res->fields['serialid']){
	                $prd_idold   = $res->fields['serialid'];
	            }
	            if($res->fields['parent_id'] == '0'){
	                $tpl->assign('parent_top_name', $res->fields['title01_urlpath']);
	                $parent_top_urlpath = $res->fields['title01_urlpath'];
	                $parent_top = $res->fields['serialid'];
	            }
            }else{
	            $parent_idtemp = 0;
	        }
        }
        //取出所有路徑代碼
        
        $tpl->assign('path_subdir', substr($path_subdir,0,-3) );
        $tpl->assign('parent_top', $parent_top );
        $config['parent_top_urlpath'] = $parent_top_urlpath;

        //取出所有子路徑代碼
        global $outstr_dl;
        $outstr_dl = '';
        // 樹狀數顯示的遞迴函數
        function dlnode_vw($table_name, $parent_id = 0,$deepnum=0) {
            $i=0;
            global $outstr_dl;

            $deepnum++;
            $strSQL1="select serialid,title01,title01_urlpath,parent_id,visible from ".$table_name." where parent_id = '".$parent_id ."' and visible != 'N' order by sortid,update_date desc";
            //echo $strSQL1.'<br />';
            $res = SqlQuery1($strSQL1);
            $count01 = $res->recordCount();
            //echo $count01.'<br />';
            while(!$res->EOF){
                //echo $res->fields['title01_urlpath'];
                $outstr_dl .= $res->fields['serialid'].',';
  	            dlnode_vw($table_name,$res->fields['serialid'],$deepnum);
	            $res->MoveNext();
	        }
        }
        dlnode_vw('product_class_lv01',$lv01_id,0);
        //echo $outstr_dl;
        // 樹狀數顯示的遞迴函數
        //取出所有子路徑代碼


    
        //輸出產品音檔
        $outstr_dl .= $lv01_id.',';
	    $list = array();
	    if($searchkeydl01 == ''){
    	    $strSQL1 = "select
                        ANY_VALUE(a.serialid) as serialid,
                        ANY_VALUE(a.prod_num) as prod_num,
                        ANY_VALUE(a.prod_name) as prod_name ,
                        ANY_VALUE(a.prod_name_origin) as prod_name_origin,
                        ANY_VALUE(a.visible) as prod_visible
    	                from product_list as a left join product_list_class01 as b on b.lv01_id = a.serialid
    	                where b.var01 in ('".str_replace(",","','",substr($outstr_dl,0,-1))."')
    	                and a.visible NOT IN ('A')
    	                and a.news_end_date >= CURDATE()
    	                and a.press_author NOT LIKE ('%アスク%')
    	                group by a.prod_num
    	                order by ANY_VALUE(a.serialid) ";
	    }else{
    	    $strSQL1 = "select
                        ANY_VALUE(a.serialid) as serialid,
                        ANY_VALUE(a.prod_num) as prod_num,
                        ANY_VALUE(a.prod_name) as prod_name ,
                        ANY_VALUE(a.prod_name_origin) as prod_name_origin,
                        ANY_VALUE(a.visible) as prod_visible
        	            from product_list as a left join product_list_class01 as b on b.lv01_id = a.serialid
        	            where 1=1
        	            and a.visible NOT IN ('A')
        	            and a.news_end_date >= CURDATE()
        	            and a.press_author NOT LIKE ('%アスク%')
        	            AND (
        	                a.prod_num like ('%".$searchkeydl01."%') 
        	                OR a.prod_name like ('%".$searchkeydl01."%') 
        	                OR a.prod_name_origin like ('%".$searchkeydl01."%')
        	                OR REPLACE(a.barcode01,'-','') like '%".str_replace('-','',$searchkeydl01)."%'
        	                OR REPLACE(a.barcode02,'-','') like '%".str_replace('-','',$searchkeydl01)."%'
        	                OR REPLACE(a.barcode03,'-','') like '%".str_replace('-','',$searchkeydl01)."%'
        	                )
        	            group by a.prod_num
        	            order by ANY_VALUE(a.serialid) ";
    	}
        //echo $strSQL1.'<br />';
	    $res = SqlQuery1($strSQL1);
	    //echo $res->RecordCount();
	    
	    
        
	    while(!$res->EOF){
        
            /** 智慧筆音檔 START **/
	        $lv02_array = array();
	        $strSQL1 = "select lv01_id,
	                    upfile1_title01, 
	                    upfile1_name, 
	                    add_date,
	                    upfile1_base
	                    from product_list_filelist_penvoice
	                    where lv01_id = '".$res->fields['serialid']."' order by sortid";
	        //echo $strSQL1.'<br />';
	        $reshot = SqlQuery1($strSQL1);
	        //echo $reshot->RecordCount();
	        $subtotal = $reshot->RecordCount();
	    
    	    while(!$reshot->EOF){
    	        $down_listpath = 'uploadfile/xpenvoice';
    		    array_push($lv02_array,
                    array(
                        'lv02_upfile1_title01' => $reshot->fields['upfile1_title01'],
    			        'lv02_upfile1_name'    => $reshot->fields['upfile1_name'],
    			        'lv02_upfile1_nameVW'  => $reshot->fields['upfile1_title01'],
    			        'lv02_upfile1_base'    => $reshot->fields['upfile1_base'],
    			        'lv02_add_date'        => date("Y-m-d",strtotime($reshot->fields['add_date'])),
    			        'lv02_file_src'         => $down_listpath,
    			    )
    			);
    			$reshot->MoveNext();
    		}
    	    /** 智慧筆音檔 END **/
	    
    	    /** CD音檔 START **/
    	    //CD音檔
            $lv02_array_cd = array();
            $strSQL4 = "select 
    	                    lv01_id,
    	                    upfile1_title01, 
    	                    upfile1_name, 
    	                    add_date,
    	                    upfile1_base
                        from product_list_filelist_bookvoice
    	                where 
    	                    lv01_id = '".$res->fields['serialid']."' order by sortid";
    	    //echo $strSQL4.'<br>';
            $rescd = SqlQuery1($strSQL4);
            $subtotal2 = $rescd->RecordCount();
            
            while(!$rescd->EOF){
                $down_listpath = 'uploadfile/xbookvoice';
                array_push($lv02_array_cd,
                    array(
                        'lv02_upfile1_title01'  => $rescd->fields['upfile1_title01'],
    			        'lv02_upfile1_name'     => $rescd->fields['upfile1_name'],
    			        'lv02_upfile1_base'     => $rescd->fields['upfile1_base'],
    			        'lv02_upfile1_nameVW'   => $rescd->fields['upfile1_title01'],
    			        'lv02_add_date'         => date("Y-m-d",strtotime($rescd->fields['add_date'])),
    			        'lv02_file_src'         => $down_listpath,
                    )
                );
                //print_r($lv02_array_cd);
                $rescd->MoveNext();
            }
    	    /** CD音檔 END **/
	    
    	    /** 產品圖片 START **/
            $lv02_array_pic = array();
    	    $strSQL3="select lv01_id, upfile1_name from product_list_filelist where lv01_id = '" . $res->fields['serialid'] . "' and dl01_type = '01' order by sortid limit 1";
    	    $respic = SqlQuery1($strSQL3);
    	    
    	    $product_listpath = 'uploadfile/fileproduct_list/';
            $subfolder01      = '/PRDPIC';
    	    
    	    while(!$respic->EOF){
    		    array_push($lv02_array_pic,
    		        array(
    			        'lv02_upfile1_name'     => $respic->fields['upfile1_name'],
    			        'lv02_pic_fold'         => $product_listpath.$respic->fields['lv01_id'].$subfolder01
    			    )
    			);
    			$respic->MoveNext();
    		}
    		/** 產品圖片 END **/
        
            /** 輸出產品內容 **/
            if( $subtotal > 0 || $subtotal2 > 0){
            $icount++;
            $tpl->assign('icount', $icount);
	        $list[]= array(
	            'lv01_serialid'         => $res->fields['serialid'],
  	            'lv01_prod_num'         => $res->fields['prod_num'],
	            'lv01_prod_name'        => $res->fields['prod_name'],
	            'lv01_prod_name_origin' => $res->fields['prod_name_origin'],
	            'lv02data'              => $lv02_array,
	            'lv02data_pic'          => $lv02_array_pic,
	            'lv02data_cd'           => $lv02_array_cd,
	            'prod_visible'          => $res->fields["prod_visible"],
	        );
        }
	        /** 輸出產品內容 END **/
	        
	        
	        $res->MoveNext();//下一筆資料
	    }

        //傳到前端
        if($icount > 0 || $icount_cd > 0){
            $tpl->assign('lv01data', $list);
        }
        //print_r($list);
        unset($list);
        //輸出產品音檔
    }
}

$tpl->display('downLoad.shtm');
?>
