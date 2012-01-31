<?php 
include_once("includes/inc.global.php");

//
//  Todos los archivos que empiezan por 'member contact' están referidos 
//  a los 'acompañantes', figura que no estamos utilizando en esta versión
//  del programa. 
//  Por tanto, estos formularios NO ESTÁN ACTUALIZADOS
//

$p->site_section = PROFILE;
$p->page_title = l("Esborrar asociat/da acompanyant");

include("includes/inc.forms.php");

if($_REQUEST["mode"] == "admin") {
	$cUser->MustBeLevel(1);
	$form->addElement("hidden","mode","admin");
} else {
	$cUser->MustBeLoggedOn();
	$form->addElement("hidden","mode","self");
}

$person = new cPerson;
$person->LoadPerson($_REQUEST["person_id"]);

$form->addElement("hidden", "person_id", $_REQUEST["person_id"]);
$form->addElement("static", null, "".l("Estàs segur d'esborrar a")." ". $person->Name() ." ".l("permanentemente?")."", null);
$form->addElement("static",null,null);
$form->addElement('submit', 'btnSubmit', l('Esborrar'), array('class' => 'formulari2'));

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p, $person;
	
	if($person->DeletePerson())
		$output = l("Asociat/da acompanyant esborrada.");
	else
		$output = l("Hi ha hagut un error en esborrar aquesta persona.");
		
	$p->DisplayPage($output);
}

?>
