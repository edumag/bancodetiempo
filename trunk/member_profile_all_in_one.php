<?php 
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

$cUser->MustBeLoggedOn();

$list = "<H2>Bienvenido/a a ". SITE_SHORT_TITLE .", ". $cUser->person[0]->first_name ."!</H2>";
$list .= "Escoge una de las siguientes opciones:<P>";

$list .= "<STRONG>Opciones de asociados/as:</STRONG><P>";
$list .= "<A HREF=password_change.php><FONT SIZE=2>Cambiar mi contraseña</FONT></A><BR>";
$list .= "<A HREF=member_edit.php?mode=self><FONT SIZE=2>Editar mi información personal</FONT></A><BR>";
// Se ha eliminado la posibilidad de personas asociadas
// $list .= "<A HREF=member_contact_create.php?mode=self><FONT SIZE=2>Add a Joint Member to My Account</FONT></A><BR>";
// $list .= "<A HREF=member_contact_choose.php><FONT SIZE=2>Edit a Joint Member</FONT></A><P>";

$list .= "<STRONG>Ofertas</STRONG><P>";
$list .= "<A HREF=listings.php?type=Offer><FONT SIZE=2>Ver ofertas</FONT></A><BR>";
$list .= "<A HREF=listing_create.php?type=Offer><FONT SIZE=2>Crear nueva oferta</FONT></A><BR>";
$list .= "<A HREF=listing_to_edit.php?type=Offer><FONT SIZE=2>Editar oferta</FONT></A><BR>";
$list .= "<A HREF=listing_delete.php?type=Offer><FONT SIZE=2>Borrar oferta</FONT></A><P>";

$list .= "<STRONG>Demandas</STRONG><P>";
$list .= "<A HREF=listings.php?type=Want><FONT SIZE=2>Ver demandas</FONT></A><BR>";
$list .= "<A HREF=listing_create.php?type=Want><FONT SIZE=2>Crear nueva demanda</FONT></A><BR>";
$list .= "<A HREF=listing_to_edit.php?type=Want><FONT SIZE=2>Editar demanda</FONT></A><BR>";
$list .= "<A HREF=listing_delete.php?type=Want><FONT SIZE=2>Borrar demanda</FONT></A><P>";

$list .= "<STRONG>Intercambios</STRONG><P>";
$list .= "<A HREF=trade.php><FONT SIZE=2>Guardar un intercambio</FONT></A><BR>";
$list .= "<A HREF=trade_history.php?mode=self><FONT SIZE=2>Ver mi balance e Historia de intercambios</FONT></A><BR>";
$list .= "<A HREF=trades_to_view.php><FONT SIZE=2>Ver la Historia de intercambios de otro/a asociado/a</FONT></A><P>";

if ($cUser->member_role > 0) {
	$list .= "<STRONG>Administracion</STRONG><P>";
	$list .= "<A HREF=member_create.php><FONT SIZE=2>Crear un/a nuevo/a asociado/a</FONT></A><BR>";
	$list .= "<A HREF=member_to_edit.php><FONT SIZE=2>Editar una cuenta de asociado/a</FONT></A><BR>";
// 	$list .= "<A HREF=member_contact_create.php?mode=admin><FONT SIZE=2>Add a Joint Member to an Existing Account</FONT></A><BR>";
// 	$list .= "<A HREF=member_contact_to_edit.php><FONT SIZE=2>Edit a Joint Member</FONT></A><BR>";
}
if ($cUser->member_role > 1) {
	$list .= "<A HREF=trade_reverse.php><FONT SIZE=2>Deshacer un intercambio que fue hecho por error</FONT></A><BR>";
}

$p->DisplayPage($list);

?>
