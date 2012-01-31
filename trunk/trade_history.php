<?php
	include_once("includes/inc.global.php");
	
	$cUser->MustBeLoggedOn();
	$p->site_section = EXCHANGES;
	$p->page_title = l("Historial d'intercanvis");

	include("classes/class.trade.php");
	
	$member = new cMember;
	
	if($_REQUEST["mode"] == "self") {
		$member = $cUser;
	} else {
		$member->LoadMember($_REQUEST["member_id"]);
		$p->page_title .= " ".l("de")." ".$member->PrimaryName();
	}
	
	if ($member->balance > 0)
		$color = "#4a5fa4";
	else
		$color = "#554f4f";
		
	
	if($member->member_role<1)
	$list = "<B> ".l("Saldo actual").": </B><FONT COLOR=". $color .">". $member->balance . " ". UNITS ."</FONT><P>";	

	$trade_group = new cTradeGroup($member->member_id);
	$trade_group->LoadTradeGroup();
        $list .= "<p> ".l("A continuació es detallen totes les transferències d'hores que s'han realitzat").". ".l("La columna 'De' indica qui ha transferit hores i la columna 'A' a qui han estat transferides").".</p>";
	$list .= $trade_group->DisplayTradeGroup();
	$p->DisplayPage($list);
	

	
?>
	
