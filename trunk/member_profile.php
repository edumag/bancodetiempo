<?php 
include_once("includes/inc.global.php");
$p->site_section = PROFILE;
$p->page_title = l("El meu Perfil");

$cUser->MustBeLoggedOn();
	
$list = "<A HREF=member_edit.php?mode=self><FONT SIZE=2>".l("Editar la meva informació personal")."</FONT></A><BR>";
$list .= "<A HREF=password_change.php><FONT SIZE=2>".l("Canviar la meva contrasenya")."</FONT></A><BR>";
$list .= "<A HREF=holiday.php?mode=self><FONT SIZE=2>".l("Desactivar el meu compte per vacances")."</FONT></A><BR>";

// Se ha eliminado la posibilidad de tener 'acompañantes'

// $list .= "<A HREF=member_contact_create.php?mode=self><FONT SIZE=2>Add a Joint Member to My Account</FONT></A><BR>";
// $list .= "<A HREF=member_contact_choose.php><FONT SIZE=2>Edit/Delete a Joint Member</FONT></A><P>";

$p->DisplayPage($list);

?>
