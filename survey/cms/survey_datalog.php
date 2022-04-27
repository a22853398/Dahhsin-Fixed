<?php
include('../commonfile/config_mang.inc.php');
include('../commonfile/PHPExcel/Classes/PHPExcel.php');
include('../commonfile/str_function.inc.php');
include('00security_function.php');

$search_key1 = strip_input('inputadm',$_REQUEST['search_key1']);
$search_orderid = strip_input('inputadm',$_REQUEST['search_orderid']);
$search_process_type = strip_input('inputadm',$_REQUEST['search_process_type']);
$search_pay_type = strip_input('inputadm',$_REQUEST['search_pay_type']);
$search_orderdate1 = strip_input('inputadm',$_REQUEST['search_orderdate1']);
$search_orderdate2 = strip_input('inputadm',$_REQUEST['search_orderdate2']);
$search_check_status = strip_input('inputadm',$_REQUEST['search_check_status']);

$search_lv00_type = strip_input('inputadm',$_REQUEST['sel00_type']);

$filename = 'Survey_'.date('Ymd').'.xlsx';

//資料列表
	$searchstr = "";
	if($search_key1 != ""){
	    $searchstr .= "OR mbr_name like '%".$search_key1."%' OR mbr_id like '%".$search_key1."%' OR responid like '%".$search_key1."%'";
	}
	if($searchstr != ""){
		$searchstr = "and ( ".substr($searchstr,2,strlen($searchstr))." ) ";
	}
	if($search_orderdate1 !=""){
	    $searchstr .= "AND (add_date >= '".$search_orderdate1."' AND add_date <= '".date("Y-m-d", strtotime($search_orderdate2." +1 day"))."') ";
	}
	if($search_check_status !=""){
	    $searchstr .= "AND check_status = '".$search_check_status."'";
	}
	if($search_lv00_type == "A1"){
	    
	}else if($search_lv00_type == "X1"){
	    
	}else{
	    $searchstr .= "AND lv00_type = '".$search_lv00_type."'";
	}
	$strSQL1=" SELECT * FROM survey_respon where 1=1 ".$searchstr." ORDER BY serialid DESC ";
    $res = SqlQuery1($strSQL1);
//資料列表



if($res->RecordCount() > 0){

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("SYSOP")
							 ->setLastModifiedBy("SYSOP")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

//輸出標題
$strSqlSelectTitle = "";
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '回覆編號')
            ->setCellValue('B1', '問卷名稱')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', 'ID')
            ->setCellValue('E1', '日期')
            ->setCellValue('F1', '來源')
            ->setCellValue('G1', '來源頁面')
            ->setCellValue('H1', '性別')
            ->setCellValue('I1', '年齡')
            ->setCellValue('J1', '上網時間')
            ->setCellValue('K1', '常用社群')
            ->setCellValue('L1', '日語程度')
            ->setCellValue('M1', '學習方式')
            ->setCellValue('N1', '推薦系列')
            ->setCellValue('O1', '有沒有筆')
            ->setCellValue('P1', '希望出版')
            ->setCellValue('Q1', '投入金額')
            ->setCellValue('R1', '常用平台')
            ->setCellValue('S1', '希望優惠')
            ->setCellValue('T1', '整體滿意')
            ->setCellValue('U1', '瀏覽滿意')
            ->setCellValue('V1', '資訊滿意')
            ->setCellValue('W1', '搜尋滿意')
            ->setCellValue('X1', '流程滿意')
            ->setCellValue('Y1', '音檔滿意')
            ->setCellValue('Z1', '填寫姓名')
            ->setCellValue('AA1', '國際碼')
            ->setCellValue('AB1', '手機')
            ->setCellValue('AC1', '填寫信箱')
            ->setCellValue('AD1', '其他意見')
            ;
//輸出標題

//json裡面有object檔案（有指定array編號項目會變成object）要變成array才能用
function stdObjectToArray($object){
    $array = json_decode(json_encode($object), true);
    return $array;
}
//產生複選題的str
function getTypeCheckBoxStr($array){
    $tpStr = "";
    for($i=0; $i<count($array); $i++){
        $tpStr .= $array[$i]." ";
    }
    return $tpStr;
}

//輸出內容
    $i=1;
    while(!$res->EOF){
        $i++;
        $survey_title = SqlQuery1("SELECT title FROM survey_article WHERE lv00_type = '".$res->fields['lv00_type']."'")->fields["title"];//問卷名稱
        
        //各問題解析
        $array_respon = json_decode($res->fields['content']);
        $array_q01 = stdObjectToArray($array_respon[0]);
        $array_q02 = stdObjectToArray($array_respon[1]);
        $array_q03 = stdObjectToArray($array_respon[2]);
        $array_q04 = stdObjectToArray($array_respon[3]);
        
        $objPHPExcel->getActiveSheet()
            ->setCellValueExplicit('A'.$i, (string)$res->fields['responid'],PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$i, (string)$survey_title ,PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('C'.$i, (string)$res->fields['mbr_name'],PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('D'.$i, (string)$res->fields['mbr_id'],PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('E'.$i, (string)$res->fields['add_date'],PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('F'.$i, (string)$res->fields['add_ipaddress'],PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('G'.$i, (string)$res->fields['add_from'], PHPExcel_Cell_DataType::TYPE_STRING)
            
            ->setCellValueExplicit('H'.$i, (string)$array_q01[0]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('I'.$i, (string)$array_q01[1]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('J'.$i, (string)getTypeCheckBoxStr($array_q01[2]['q_ans']), PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('K'.$i, (string)getTypeCheckBoxStr($array_q01[3]['q_ans']), PHPExcel_Cell_DataType::TYPE_STRING)
            
            ->setCellValueExplicit('L'.$i, (string)$array_q02[0]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('M'.$i, (string)getTypeCheckBoxStr($array_q02[1]['q_ans']), PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('N'.$i, (string)getTypeCheckBoxStr($array_q02[2]['q_ans']), PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('O'.$i, (string)$array_q02[3]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('P'.$i, (string)getTypeCheckBoxStr($array_q02[4]['q_ans']), PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('Q'.$i, (string)$array_q02[5]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('R'.$i, (string)getTypeCheckBoxStr($array_q02[6]['q_ans']), PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('S'.$i, (string)$array_q02[7]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            
            ->setCellValueExplicit('T'.$i, (string)$array_q03[0]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('U'.$i, (string)$array_q03[1]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('V'.$i, (string)$array_q03[2]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('W'.$i, (string)$array_q03[3]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('X'.$i, (string)$array_q03[4]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('Y'.$i, (string)$array_q03[5]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            
            ->setCellValueExplicit('Z'.$i, (string)$array_q04[1]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AA'.$i, (string)$array_q04[2]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AB'.$i, (string)$array_q04[3]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AC'.$i, (string)$array_q04[4]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('AD'.$i, (string)$array_q04[5]['q_ans'], PHPExcel_Cell_DataType::TYPE_STRING)
            ;

        $res->MoveNext();
    }
//輸出內容

$objPHPExcel->getActiveSheet()->setTitle('DATA01');

$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

}

exit;

?>