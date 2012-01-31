<?php 

$DIR_BASE='../';

include_once("../includes/inc.global.php");
$p->site_section = SECTION_INFO;
$p->page_title = "Manuals";

print $p->MakePageHeader();
print $p->MakePageMenu();
print $p->MakePageTitle();

?>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p><strong><a href="../docs/comopedirunaoferta.pdf">Cómo pedir un servicio de una oferta que nos interesa</a><br />
  <br />
</strong><strong><a href="../docs/comopublicarofertas.pdf">Cómo publicar en la web nuestras ofertas</a></strong></p>
<p><strong><a href="../docs/comodarporrealizadounintercambio.pdf">Cómo dar por realizado un intercambio</a></strong></p>
<p><strong><a href="../docs/comocambiarlapassword.pdf">Cómo cambiar la contraseña</a><br />
  <br />
</strong><strong><a href="../docs/comoregenerarpassword.pdf">Cómo regenerar o recordar la contraseña</a></strong></p>
<p>&nbsp;</p>
<p><?php 

print $p->MakePageFooter();

?>
</p>
