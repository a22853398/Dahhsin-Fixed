<?php
include('../commonfile/config_mang.inc.php');
include($config['templatebpath'].'class.TemplatePower.inc.php');
require_once("PHPGangsta/GoogleAuthenticator.php");

  $tpl = new TemplatePower( './01login.htm' );
  $tpl->prepare();

  $tpl->assign('main_webname', $config['main_webname'] );

$tpl->gotoBlock('_ROOT');
//輸出管理單位
  $strSQL1="select serialid,var01 from web_group_lv01 where lv00_type = 1 and visible = 'Y' order by sortid ";
  //echo $strSQL1.'<br />';
  $res = SqlQuery1($strSQL1);
  while(!$res->EOF){
			$tpl->newBlock('MANGUNIT_LIST_row');
			$tpl->assign('cd30_id', $res->fields['serialid'] );
			$tpl->assign('cd30_name', $res->fields['var01'] );
	$res->MoveNext();
	}
//輸出管理單位

  $tpl->printToScreen();
?>