<?php 
include_once("includes/inc.global.php");
$p->site_section = LISTINGS;
$p->page_title = l("Les meves ofertes");

$cUser->MustBeLoggedOn();
  
$cUser->LimitesPasados(l("Oferta"));
// $list = "<STRONG>Ofertas</STRONG><P>";
$list .= "<A HREF=listing_create.php?type=Offer&mode=self><FONT SIZE=2>".l("Crear ofertes")."</FONT></A><BR>";
$list .= "<A HREF=listing_to_edit.php?type=Offer&mode=self><FONT SIZE=2>".l("Editar ofertes")."</FONT></A><BR>";
$list .= "<A HREF=listing_delete.php?type=Offer&mode=self><FONT SIZE=2>".l("Esborrar ofertes")."</FONT></A><P>";

$p->DisplayPage($list);

?>
