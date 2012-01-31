<?php 

$DIR_BASE='./';

include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

print $p->MakePageHeader();
print $p->MakePageMenu();

include("classes/class.news.php");
include("classes/class.uploads.php");

?>
<ul>
<li><strong><a href='info/que_es.php'><?php echo l('Què és el banc del temps?');?></a><br><br></strong></li>
<li><strong><a href='info/como_funciona.php'><?php echo l('Com funciona el banc del temps?')?></a><br><br></strong></li>
<li><strong><a href='info/tipos_de_intercambio.php'><?php echo l('Tipus d\'intercanvi')?></a><br><br></strong></li>
<li><strong><a href='info/condiciones_de_uso.php'><?php echo l('Condicions d\'ús')?></a><br><br></strong></li>
</ul>
<?php 

$output = "<h2>".l("Últimes novetats")."</h2> <p>";
$news = new cNewsGroup();
$news->LoadNewsGroup();
$newstext = $news->DisplayNewsGroup();
if($newstext != "")
    $output .= $newstext;
else
    $output .= l("Properament us explicarem novetats sobre el Banc de Temps.")."</p>
";

$newsletters = new cUploadGroup("N");

if($newsletters->LoadUploadGroup()) {
    $output .= "<I>".l("Para leer las últimas novedades de ")."". SITE_SHORT_TITLE . ", ".l("pulsa")." <A HREF=newsletters.php>".l("aquí")."</A>.</I>";
}

echo $output;

print $p->MakePageFooter(); 
//  Cabecera original

?>
