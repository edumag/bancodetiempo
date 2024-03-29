<?php 
include_once("includes/inc.global.php");

$p->site_section = EXCHANGES;
$p->page_title = l("Tria el període de temps");

$cUser->MustBeLoggedOn();

include("includes/inc.forms.php");

$form->addElement("hidden", "action", $_REQUEST["action"]);
$today = getdate();
$options = array('language'=> 'ca', 'format' => 'dFY', 'minYear' => $today['year']-3,'maxYear' => $today['year']);
$form->addElement("date", "from", l("Des de quan?"), $options, array('class' => 'formulari2'));
$form->addElement("date", "to", l("Fins a quan?"), $options, array('class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', l('Enviar'), array('class' => 'formulari2'));

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$date = array("Y"=>$today["year"], "F"=>$today["mon"], "d"=>$today["mday"]);
	$form->setDefaults(array("from"=>$date, "to"=>$date));
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $cUser;
	
	$date = $values['from'];
	$from = $date['Y'] . '-' . $date['F'] . '-' . $date['d'];
	$date = $values['to'];
	$to = $date['Y'] . '-' . $date['F'] . '-' . $date['d'];
		
	header("location:http://".HTTP_BASE."/". $_REQUEST["action"] .".php?from=".$from . "&to=". $to);
	exit;	
} 

?>
