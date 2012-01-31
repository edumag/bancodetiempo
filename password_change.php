<?php 

include_once("includes/inc.global.php");

$cUser->MustBeLoggedOn();
$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");

//
// Define form elements
//
$form->addElement('header', null, l("Camviar la clau de").' '. $cUser->person[0]->first_name ." " . $cUser->person[0]->last_name);
$form->addElement('html', '<TR></TR>');  // TODO: Move this to the header
$form->addElement('static',null,l('Per raons de seguretat, les claus han de tenir un mínim de 7 caràcters i incloure al menys un número.'));
$form->addElement('html', '<TR></TR>');
$options = array('size' => 10, 'maxlength' => 15, 'class' => 'formulari2');
$form->addElement('password', 'old_passwd', l('Clau actual'),$options);
$form->addElement('password', 'new_passwd', l('Escull una nova clau'),$options);
$form->addElement('password', 'rpt_passwd', l('Repeteix la nova clau'),$options);
$form->addElement('submit', 'btnSubmit', l('Camviar la clau'), array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule('old_passwd', l('Introdueix la teva clau actual'), 'required');
$form->addRule('new_passwd', l('Introdueix una nova clau'), 'required');
$form->addRule('rpt_passwd', l('Has d\'escriure la nova clau aquí també'), 'required');
$form->addRule('new_passwd', l('La clau ha se ser més llarga'), 'minlength', 7);
$form->registerRule('verify_passwords_equal','function','verify_passwords_equal');
$form->addRule('new_passwd', 'Les claus no son iguals', 'verify_passwords_equal');
$form->registerRule('verify_old_password','function','verify_old_password');
$form->addRule('old_passwd', l('La clau no es correcta'), 'verify_old_password');
$form->registerRule('verify_good_password','function','verify_good_password');
$form->addRule('new_passwd', l('Les claus han de tenir un número com a mínim'), 'verify_good_password');

//
//	Display or process the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process('process_data', false);
} else {
   $p->DisplayPage($form->toHtml());  // just display the form
}

function process_data ($values) {
	global $p, $cUser;
	
	if($cUser->ChangePassword($values['new_passwd']))
		$list = l('La clau s\'ha camviat correctament.');
	else
		$list = l('Hi ha hagut un error al canviar la clau. Si us plau, intenta-ho més tard.');
	$p->DisplayPage($list);
}

function verify_old_password($element_name,$element_value) {
	global $cUser;
	if($cUser->ValidatePassword($element_value))
		return true;
	else
		return false;
}

function verify_good_password($element_name,$element_value) {
	$i=0;
	$length=strlen($element_value);
	
	while($i<$length) {
		if(ctype_digit($element_value{$i}))
			return true;	
		$i+=1;
	}
	
	return false;
}


function verify_passwords_equal() {
	global $form;

	if ($form->getElementValue('new_passwd') != $form->getElementValue('rpt_passwd'))
		return false;
	else
		return true;
}

?>
