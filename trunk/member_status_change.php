<?php 
include_once("includes/inc.global.php");
include_once("classes/class.listing.php");

$cUser->MustBeLevel(2);
$p->site_section = ADMINISTRATION;

$member = new cMember;
$member->LoadMember($_REQUEST["member_id"]);
if($member->status == 'A')
	$p->page_title = l("Desactivar")." ";
else
	$p->page_title = l("Reactivar")." " ;
	
$p->page_title .= $member->PrimaryName() ." (". $member->member_id .")";

include("includes/inc.forms.php");
include_once("classes/class.news.php");

$form->addElement("hidden", "member_id", $_REQUEST["member_id"]);

if($member->status == 'A') {
	$form->addElement("static", null, "".l("¿Estás seguro/a de que quieres desactivar la cuenta de esta persona?")." ".l("No podrá volver a usar este Banco de Tiempo, y sus ofertas y demandas serán también desactivadas").". ", null);
	$form->addElement("static", null, null, null);
	$form->addElement('submit', 'btnSubmit', l('Desactivar'), array('class' => 'formulari2'));
} else {
	$form->addElement("static", null, "".l("¿Estás seguro/a de que quieres reactivar la cuenta de esta persona?")." ".l("Sus ofertas y demandas deberán ser reactivadas individualmente, o crear otras nuevas").". ", null);
	$form->addElement("static", null, null, null);
	$form->addElement('submit', 'btnSubmit', l('Reactivar'), array('class' => 'formulari2'));
}

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p, $member;
	
	if($member->status == 'A') {
		$success = $member->DeactivateMember();
		$listings = new cListingGroup(OFFER_LISTING);
		$listings->LoadListingGroup(null,null,$member->member_id);
		$date = new cDateTime("yesterday");
		if($success)
			$success = $listings->ExpireAll($date);
		if($success) {
			$listings = new cListingGroup(WANT_LISTING);
			$listings->LoadListingGroup(null,null,$member->member_id);
			$success = $listings->ExpireAll($date);
		}
	} else {
		$success = $member->ReactivateMember();
	}

	if($success)
		$output = l("Cambios guardados.");
	else
		$output = l("Ha habido un error guardando los cambios. Prueba más tarde.");	
			
	$p->DisplayPage($output);
}

?>
