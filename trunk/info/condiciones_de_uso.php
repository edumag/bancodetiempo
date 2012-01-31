<?php 

$DIR_BASE='../';

include_once("../includes/inc.global.php");
$p->site_section = SECTION_INFO;
$p->page_title = l("Condicions d'ús");

print $p->MakePageHeader();
print $p->MakePageMenu();
print $p->MakePageTitle();

?>
<ul><li><strong><a href="que_es.php"><?php echo l('Què és el banc del temps?');?></a><br />
      <br />
</strong></li>
<li><strong><a href="como_funciona.php"><?php echo l('Com funciona el banc del temps?');?></a><br />
      <br />
</strong></li>
<li><strong><a href="tipos_de_intercambio.php"><?php echo l('Tipus d\'intercanvi');?></a><br />
      <br />
</strong></li>
</ul>

<br />

<?php include(fl('info/condiciones_de_uso.php')); ?>

<?php 

print $p->MakePageFooter();

?>

