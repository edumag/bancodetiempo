<?php 

include_once("includes/inc.global.php");
$p->site_section = PROFILE;

$member = new cMember;
$member->LoadMember($_REQUEST["member_id"]);

$p->page_title = l("Informació general de")." ".$member->PrimaryName();

include_once("classes/class.listing.php");

$output = "<STRONG><I>".l("INFORMACIÓ DE CONTACTE")."</I></STRONG><P>";
$output .= $member->DisplayMember();

$output .= "<BR><P><STRONG><I>".l("OFERTES")."</I></STRONG><P>";
$listings = new cListingGroup(OFFER_LISTING);
$listings->LoadListingGroup(null, null, $_REQUEST["member_id"]);
$output .= $listings->DisplayListingGroup();

$output .= "<BR><P><STRONG><I>".l("DEMANDES")."</I></STRONG><P>";
$listings = new cListingGroup(WANT_LISTING);
$listings->LoadListingGroup(null, null, $_REQUEST["member_id"]);
$output .= $listings->DisplayListingGroup();

$p->DisplayPage($output); 

?>
