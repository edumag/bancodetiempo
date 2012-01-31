<?php 
include_once("includes/inc.global.php");
$p->site_section = EXCHANGES;
$p->page_title = l("Intercanvis");

$cUser->MustBeLoggedOn();

$list .= "<FONT SIZE=4><B></b></FONT></A><p><BR>";
$list .= "<FONT SIZE=2><B>".l("Els meus intercanvis")."</b></FONT></A><p>";
$list .= "<A HREF=trade.php?mode=self><FONT SIZE=2>".l("Donar per realizat un intercanvi->Pagar o Transferir el temps")."</FONT></A><p>";
$list .= "<A HREF=trade_history.php?mode=self><FONT SIZE=2>".l("Veure les hores i la meva història d'intercanvis")."</FONT></A><BR>";
$list .= "<A HREF=feedback_choose.php?mode=self><FONT SIZE=2>".l("Veure o canviar la valoració d'un intercanvi meu")."</FONT></A><BR>"; 
$list .= "<A HREF=feedback_all.php?mode=self><FONT SIZE=2>".l("Veure la meva valoració")."</FONT></A><BR><P><P><P><br />";
$list .= "<STRONG>".l("Intercanvis d'altres")."</STRONG><P>";
$list .= "<A HREF=trades_to_view.php><FONT SIZE=2>".l("Veure la història d'intercanvis d'una altra persona i les seves hores")."</FONT></A><br>";
$list .= "<A HREF=feedback_to_view.php><FONT SIZE=2>".l("Veure la valoració d'una altra persona")."</FONT></A><BR>";
$list .= "<A HREF=timeframe_choose.php?action=trade_history_all><FONT SIZE=2>".l("Veure tots els intercanvis d'un període determinat")."</FONT></A><p>";
$p->DisplayPage($list);

?>
