<?php 

include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

if($cUser->IsLoggedOn())
{
	$list = l("Benvingut al")." ". SITE_LONG_TITLEMI .", ". $cUser->PrimaryName() ."!";
}
else 
{
	$list = $cUser->UserLoginPage();
}

$p->DisplayPage($list);

?>
