<?php 

$DIR_BASE='../';

include_once("../includes/inc.global.php");
$p->site_section = LISTINGS;
$p->page_title = "Crèdits";

print $p->MakePageHeader();
print $p->MakePageMenu();
print $p->MakePageTitle();

?>

<?php include(fl('info/credits.php')); ?>

<?php 

print $p->MakePageFooter();

?>
