<?php 
include_once("includes/inc.global.php");
$p->site_section = LISTINGS;
// $p->page_title = "Choose the ". $_REQUEST["type"] ." Listing to Edit";
if ($_REQUEST["type"] == "Offer") $p->page_title = l("Tria l'oferta a editar");
if ($_REQUEST["type"] == "Want") $p->page_title = l("Tria la Demanda a editar");


include("classes/class.listing.php");

$listings = new cTitleList($_GET['type']);

$member = new cMember;

if($_REQUEST["mode"] == "admin") {
	$cUser->MustBeLevel(1);
	$member->LoadMember($_REQUEST["member_id"]);
} else {
	$cUser->MustBeLoggedOn();
    $cUser->LimitesPasados();
	$member = $cUser;
}

$list = $listings->DisplayMemberListings($member);

if($list == "")
	$list = l("No hi ha cap element a editar.");

$p->DisplayPage($list);

?>
