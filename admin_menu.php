<?php
include_once("includes/inc.global.php");
$p->site_section = ADMINISTRATION;
$p->page_title = l("Menú d'administració");

$cUser->MustBeLevel(1);

$list = "<STRONG>".l("Usuaris")."</STRONG><P>";
$list .= "<A HREF=member_create.php><FONT SIZE=3>".l("Crear un nou usuari o usuària")."</FONT></A><BR>";
$list .= "<A HREF=member_to_edit.php><FONT SIZE=3>".l("Editar un usuari o usuària")."</FONT></A><BR>";
$list .= "<A HREF=member_to_edit_waitings.php><FONT SIZE=3>".l("usuaris o usuàrias pendents d'acceptar")."</FONT></A><BR>";
if ($cUser->member_role > 1) {
$list .= "<A HREF=member_choose.php?action=member_status_change&inactive=Y><FONT SIZE=3>".l("Desactivar / Reactivar a un usuari o usuària")."</FONT></A><BR>";
}
//$list .= "<A HREF=member_contact_create.php?mode=admin><FONT SIZE=3>Adjuntar una persona a un usuario o usuària</FONT></A><BR>";
//$list .= "<A HREF=member_contact_to_edit.php><FONT SIZE=3>Editar/Borrar un compañero/a de un/a usuario/a</FONT></A><BR>";
$list .= "<A HREF=member_unlock.php><FONT SIZE=3>".l("Desbloquejar una conta i refer la clau")."</FONT></A><P>";
if ($cUser->member_role > 2) {
$list .= "<A HREF=directory.php><FONT SIZE=3>".l("Imprimir directori amb pdf")."</FONT></A><P>";
}
if ($cUser->member_role > 1) {
    $list .= "<STRONG>".l("Intercanvis")."</STRONG><P>";
    $list .= "<A HREF=member_choose.php?action=trade><FONT SIZE=3>".l("Crear un intercanvi")."</FONT></A><BR>";
    $list .= "<A HREF=trade_reverse.php><FONT SIZE=3>".l("Desfer un intercamvi fet per error")."</FONT></A><BR>";
    $list .= "<A HREF=member_choose.php?action=feedback_choose><FONT SIZE=3>".l("Gravar la valoració d'un usuari o usuària")."</FONT></A><P>";
}

$list .= "<STRONG>".l("Ofertes")."</STRONG><P>";
$list .= "<A HREF=member_choose.php?action=listing_to_edit&get1=type&get1val=Offer><FONT SIZE=3>".l("Editar una oferta")."</FONT></A><BR>";
$list .= "<A HREF=member_choose.php?action=listing_delete&get1=type&get1val=Offer><FONT SIZE=3>".l("Esborrar una oferta")."</FONT></A><P>";

$list .= "<STRONG>".l("Demandes")."</STRONG><P>";
$list .= "<A HREF=member_choose.php?action=listing_to_edit&get1=type&get1val=Want><FONT SIZE=3>".l("Editar una demanda")."</FONT></A><BR>";
$list .= "<A HREF=member_choose.php?action=listing_delete&get1=type&get1val=Want><FONT SIZE=3>".l("Esborrar una demanda")."</FONT></A><P>";

$list .= "<STRONG>".l("Varis")."</STRONG><P>";
$list .= "<A HREF=member_choose.php?action=holiday><FONT SIZE=3>".l("Un usuari o usuària se'n va de vacances")."</FONT></A>";
if ($cUser->member_role > 1) {
    $list .= "<BR><A HREF=category_create.php><FONT SIZE=3>".l("Crear una nova categoria")."</FONT></A><BR>";
    $list .= "<A HREF=category_choose.php><FONT SIZE=3>".l("Editar / Esborrar una categoria")."</FONT></A>";
}
$list .= "<P>";


$list .= "<STRONG>".l("Sistema")."</STRONG><P>";
if ($cUser->member_role > 1) {
    $list .= "<A HREF=export.php><FONT SIZE=3>".l("Exportar / Fer un backup de dades en una fulla de càlcul")."</FONT></A><BR>";
    $list .= "<A HREF=contact_all.php><FONT SIZE=3>".l("Enviar un correu-e a totes les persones usuàrias")."</FONT></A><BR>";
}
$list .= "<A HREF=report_no_login.php><FONT SIZE=3>".l("Veure qui no ha entrat mai")."</FONT></A><P>";

$list .= "<STRONG>".l("Notícies i Esdeveniments")."</STRONG><P>";
$list .= "<A HREF=news_create.php><FONT SIZE=3>".l("Crear una notícia")."</FONT></A><BR>";
$list .= "<A HREF=news_to_edit.php><FONT SIZE=3>".l("Editar una notícia")."</FONT></A><BR>";
//$list .= "<A HREF=newsletter_upload.php><FONT SIZE=3>".l("Subir una publicación")."</FONT></A><BR>";
//$list .= "<A HREF=newsletter_delete.php><FONT SIZE=3>".l("Borrar periódico")."</FONT></A><BR>";

$p->DisplayPage($list);



?>
