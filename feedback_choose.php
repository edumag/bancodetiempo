<?php 

include_once("includes/inc.global.php");

$p->site_section = SECTION_FEEDBACK;
$p->page_title = l("Introducir valoración");

include("classes/class.feedback.php");

$member = new cMember;

if($_REQUEST["mode"] == "admin") {
	$cUser->MustBeLevel(2);
	$member->LoadMember($_REQUEST["member_id"]);
} else {
	$cUser->MustBeLoggedOn();
	$member = $cUser;
}

$since_date = new cDateTime("-". DAYS_REQUEST_FEEDBACK ." days");
$tradegrp = new cTradeGroup($member->member_id, $since_date->MySQLDate()); 
$tradegrp->LoadTradeGroup();

$output = "<B>".l("Per quin intercanvi?")."</B><BR>";
$output .= "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=\"100%\">";

$i=0;
if(isset($tradegrp->trade)) {
	foreach($tradegrp->trade as $trade) {	
		if($trade->type == TRADE_REVERSAL or $trade->status == TRADE_REVERSAL)
			continue;	// No reason to list reversed trades, so let's skip 'em
	
		if($i % 2)
			$bgcolor = "#EEEEEE";
		else
			$bgcolor = "#FFFFFF";
			
		// Was member the buyer or seller?
		if ($trade->member_from->member_id == $member->member_id) {
			$member_id_other = $trade->member_to->member_id;
			$feedback_member =& $trade->feedback_buyer;
		} else {
			$member_id_other = $trade->member_from->member_id;
			$feedback_member =& $trade->feedback_seller;
		}
		
		if(!$feedback_member) {	// Member hasn't left feedback yet, show link
			$date = new cDateTime($trade->trade_date);	
			$trade_date = $date->ShortDate();
					
			$output .= "<TR VALIGN=TOP BGCOLOR=". $bgcolor ."><TD><FONT SIZE=2><A HREF=feedback.php?author=". $member->member_id ."&about=". $member_id_other ."&trade_id=". $trade->trade_id ."&mode=".$_REQUEST["mode"] .">". $trade_date ."</A></FONT></TD><TD><FONT SIZE=2>". $member_id_other ."</FONT></TD><TD><FONT SIZE=2>". $trade->category->description ."</FONT></TD><TD ALIGN=RIGHT><FONT SIZE=2>". $trade->amount ."&nbsp;</FONT></TD><TD><FONT SIZE=2>". $cDB->UnEscTxt($trade->description) ."</FONT></TD></TR>";	
			$i+=1;
		} 	
	}
}

$output .= "</TABLE>";

if($i == 0)
	$output .= l("No hi ha intercanvis als que puntuar. Ja has puntuat els teus intercanvis recents.");

$p->DisplayPage($output);

?>
