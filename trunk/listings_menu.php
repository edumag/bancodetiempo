<?php 
include_once("includes/inc.global.php");
$p->site_section = LISTINGS;
$p->page_title = l("Afegir ofertes i demandes");

$cUser->MustBeLoggedOn();

$list = "<STRONG>".l("Ofertes")."</STRONG><P>";
$list .= "<A HREF=listing_create.php?type=Offer&mode=self><FONT SIZE=2>".l("Crear ofertes")."</FONT></A><BR>";
$list .= "<A HREF=listing_to_edit.php?type=Offer&mode=self><FONT SIZE=2>".l("Editar ofertes")."</FONT></A><BR>";
$list .= "<A HREF=listing_delete.php?type=Offer&mode=self><FONT SIZE=2>".l("Esborrar ofertes")."</FONT></A><P>";

$list .= "<STRONG>".l("Demandes")."</STRONG><P>";
$list .= "<A HREF=listing_create.php?type=Want&mode=self><FONT SIZE=2>".l("Crear demandes")."</FONT></A><BR>";
$list .= "<A HREF=listing_to_edit.php?type=Want&mode=self><FONT SIZE=2>".l("Editar demandes")."</FONT></A><BR>";
$list .= "<A HREF=listing_delete.php?type=Want&mode=self><FONT SIZE=2>".l("Esborrar demandes")."</FONT></A><P>";

$list .= "<STRONG>".l("Altres")."</STRONG><P>";
$list .= "<A HREF=holiday.php?mode=self><FONT SIZE=2>".l("Vacances")."</FONT></A><BR>";

$p->DisplayPage($list);

?>
