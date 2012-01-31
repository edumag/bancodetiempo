<?php 

include_once("includes/inc.global.php");

$p->site_section = LISTINGS;
$p->page_title = l("Crear una nueva categoría");

include("includes/inc.forms.php");
include_once("classes/class.category.php");

//
// Define form elements
//
$cUser->MustBeLevel(2);

$form->addElement("text", "category", l("Descripción de la categoría"), array("size" => 30, "maxlength" => 30, 'class' => 'formulari2'));
$form->addElement("static", null, null, null);

$form->addElement('submit', 'btnSubmit', l('Enviar'), array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule('category', l('Introduce la descripción'), 'required');

//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {
   $p->DisplayPage($form->toHtml());  // just display the form
}

function process_data ($values) {
	global $p, $cErr;
	
	$category = new cCategory($values["category"]);
	
	if ($category->SaveNewCategory()) {
		$output = l("Se ha creado la categoría.");
	} else {
		$output = l("No se ha podido guardar la categoría. Prueba más tarde.");
	}
	
	$p->DisplayPage($output);
}

//
// Form rule validation functions
//


?>
