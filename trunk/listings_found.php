<?php 

include_once("includes/inc.global.php");
$p->site_section = LISTINGS;
// $p->page_title = $_REQUEST["type"] ."ed Listings";
if ($_REQUEST["type"] == "Offer") $p->page_title = l("Ofertes");
if ($_REQUEST["type"] == "Want") $p->page_title = l("Demandes");

include_once("classes/class.listing.php");

if($_REQUEST["category"] == "0")
	$category = "%";
else
	$category = $_REQUEST["category"];
	
if($_REQUEST["timeframe"] == "0")
	$since = new cDateTime(LONG_LONG_AGO);
else
	$since = new cDateTime("-". $_REQUEST["timeframe"] ." days");

if ($cUser->IsLoggedOn())
	$show_ids = true;
else
	$show_ids = false;

$listings = new cListingGroup($_GET["type"]);
$listings->LoadListingGroup(null, $category, null, $since->MySQLTime());
$output = $listings->DisplayListingGroup($show_ids);

$p->DisplayPage($output); 

include("includes/inc.events.php");

?>
