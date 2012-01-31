<?php 
include_once("includes/inc.global.php");

$cUser->MustBeLevel(2);
$p->site_section = ADMINISTRATION;
$p->page_title = l("Exportar a hoja de cálculo");

include("classes/class.backup.php");
include("includes/inc.forms.php");

$form->addElement("static", 'contact', l("Esto exportará todas las tablas de la base de datos en una hoja de cálculo Excel para copias de seguridad y otros usos. Aprieta en el botón 'Descargar' y te llegará el archivo. Recuerda que esta información es confidencial, y debes guardarle en un lugar privado y seguro."), null);
$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", l("Descargar"));

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p;

	$backup = new cBackup();
	$backup->BackupAll();

	$list = l("Envío completado.");
	$p->DisplayPage($list);
}
?>
