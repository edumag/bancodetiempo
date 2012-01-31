<?php 
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

$cUser->MustBeLevel(1);
include("includes/inc.forms.php");

$form->addElement("header", null, l("Tria soci a editar"));
$form->addElement("html", "<TR></TR>");

$ids = new cMemberGroup;
$ids->LoadMemberGroup(null,true);

$form->addElement("select", "member_id", l("Usuari/ària")." ", $ids->MakeIDArray(), array('class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', l('Editar'), array('class' => 'formulari2'));

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $cUser;
	header("location:http://".HTTP_BASE."/member_edit.php?mode=admin&member_id=".$values["member_id"]);
	exit;	
}

?>
