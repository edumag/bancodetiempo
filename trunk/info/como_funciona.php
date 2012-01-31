<?php 

$DIR_BASE='../';

include_once("../includes/inc.global.php");
$p->site_section = SECTION_INFO;
$p->page_title = l("Com funciona el banc del temps?");

print $p->MakePageHeader();
print $p->MakePageMenu();
print $p->MakePageTitle();

?>
<ul>
<li><strong><a href='que_es.php'><?php echo l('Què és el banc del temps?');?></a></strong><br><br></li>
<li><strong><a href='tipos_de_intercambio.php'><?php echo l('Tipus d\'intercanvi');?></a></strong><br><br></li>
<li><strong><a href='condiciones_de_uso.php'><?php echo l('Condicions d\'ús');?></a></strong></li>
</ul>


<?php include(fl('info/como_funciona.php')); ?>

<?php 

print $p->MakePageFooter();

?>

