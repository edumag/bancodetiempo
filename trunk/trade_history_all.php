<?php
	include_once("includes/inc.global.php");
	
	$cUser->MustBeLoggedOn();
	$p->site_section = EXCHANGES;
	$p->page_title = l("Historial d'intercanvis");

	include("classes/class.trade.php");
	
	$from = new cDateTime($_REQUEST["from"]);
	$to = new cDateTime($_REQUEST["to"]);
	
	$output = "<B>".l("Per a un període des de")." ". $from->ShortDate() ." ".l("fins")." ". $to->ShortDate() ."</B><P>";	

	$trade_group = new cTradeGroup("%", $_REQUEST["from"], $_REQUEST["to"]);
	$trade_group->LoadTradeGroup();
    $output .= "<p> ".l("A continuació es detallen totes les transferències d'hores que s'han realitzat").". ".l("La columna 'De' indica qui ha transferit hores i la columna 'A' a qui han estat transferides").".</p>";
	$output .= $trade_group->DisplayTradeGroup();
	
	$p->DisplayPage($output);
	

	
?>
	
