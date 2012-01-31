<?php 
include_once("includes/inc.global.php");
include("classes/class.news.php");
include("classes/class.uploads.php");

$p->site_section = EVENTS;
$p->page_title = l("Noticias y eventos");

$output = "<P><BR>";

$news = new cNewsGroup();
$news->LoadNewsGroup();
$newstext = $news->DisplayNewsGroup();
if($newstext != "")
	$output .= $newstext;
else
	$output .= l("Próximamente os contaremos novedades sobre el Banco de Tiempo").".<P>";

$newsletters = new cUploadGroup("N");

if($newsletters->LoadUploadGroup()) {
	$output .= "<I>".l("Para leer las últimas novedades de")."  ". SITE_SHORT_TITLE . ", ".l("pulsa")." <A HREF=newsletters.php>".l("aquí")."</A>.</I>";
}

$p->DisplayPage($output);


?>
