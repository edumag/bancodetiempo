<?php 

$DIR_BASE='../';

include_once("../includes/inc.global.php");
$p->site_section = SECTION_INFO;
$p->page_title = l("Tipus d'intercanvi");

print $p->MakePageHeader();
print $p->MakePageMenu();
print $p->MakePageTitle();

?><strong><br>
</strong>
<ul><li><strong><a href="que_es.php"><?php echo l('Què és el banc del temps?');?></a><br />
      <br />
</strong></li>
<li><strong><a href="como_funciona.php"><?php echo l('Com funciona el banc del temps?');?></a><br />
      <br />
</strong></li>
<li><strong><a href="condiciones_de_uso.php"><?php echo l('Condicions d\'ús');?></a><br />
      <br />
</strong></li>
</ul>

<?php include(fl('info/tipos_de_intercambio.php')); ?>

<?php 

print $p->MakePageFooter();

?>

