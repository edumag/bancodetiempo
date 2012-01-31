<?php 
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

$cUser->Logout();

$list = l("Has sortit").".<P>";
$list .= l("Pots tornar a entrar en qualsevol moment prement l'enllaç \"Entrar\" a la part superior dreta d'aquesta pàgina.");

$p->DisplayPage($list);

?>
