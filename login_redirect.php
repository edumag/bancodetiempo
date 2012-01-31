<?php 
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

$output = l("Per poder accedir a aquesta secció, has de tenir un compte amb el teu nom i una clau").". <br><br>".l("Si ja la tens, accedeix aquí mateix").":<BR /><BR />
<CENTER>
<FORM ACTION=login.php METHOD=POST>
<INPUT TYPE=HIDDEN NAME=action VALUE=login>
<INPUT class=formulari2 TYPE=HIDDEN NAME=location VALUE='".$_SESSION["REQUEST_URI"]."'>
	<TABLE class=NoBorder>
		<TR>
			<TD ALIGN=RIGHT>".l("Identificador").":</TD>
			<TD ALIGN=LEFT><INPUT TYPE=TEXT SIZE=12 NAME=user class=formulari2></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT>".l("Clau").":</TD>
			<TD ALIGN=LEFT><INPUT class=formulari2 TYPE=PASSWORD SIZE=12 NAME=pass></TD>
		</TR>
		<TR>
			<TD></TD>
			<TD ALIGN=LEFT><INPUT class=formulari2 TYPE=SUBMIT VALUE=".('Entra')."></TD>
		</TR>
	</TABLE>
</FORM>
</CENTER><BR />".l("Si no tens un compte").", <A HREF=member_self.php>".l("apunta't")."</A> ".l("al Banc de Temps").".<BR />";

$p->DisplayPage($output);
// NOTA: Cambiado el inicio del form:  <FORM ACTION=".SERVER_PATH_URL."
?>
