<?php 
include_once("includes/inc.global.php");
$p->site_section = LISTINGS;
$p->page_title = l("Les meves demandes");

$cUser->MustBeLoggedOn();
$cUser->LimitesPasados("Demanda");

// $list .= "<STRONG>Demandas</STRONG><P>";
$list .= "<A HREF=listing_create.php?type=Want&mode=self><FONT SIZE=2>".l("Crear demandes")."</FONT></A><BR>";
$list .= "<A HREF=listing_to_edit.php?type=Want&mode=self><FONT SIZE=2>".l("Editar demandes")."</FONT></A><BR>";
$list .= "<A HREF=listing_delete.php?type=Want&mode=self><FONT SIZE=2>".l("Esborrar demandes")."</FONT></A><P>";

$p->DisplayPage($list);

?>
