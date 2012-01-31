<?php 

include_once("includes/inc.global.php");

//
//  Todos los archivos que empiezan por 'member contact' están referidos 
//  a los 'acompañantes', figura que no estamos utilizando en esta versión
//  del programa. 
//  Por tanto, estos formularios NO ESTÁN ACTUALIZADOS
//   REVISA TB LO DEL TELEFONO QUE ESTA CAMBIADO, SE AÑADIO LA FUNCION DE VERIFICACION


$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");

//
// First, we define the form
//

$form->addElement("header", null, "".l("Afegir acompanyant a un/a asociat/da"));
$form->addElement("html", "<TR></TR>");

if($_REQUEST["mode"] == "admin") {  // Administrator is adding to a member's account
	$cUser->MustBeLevel(1);
	$form->addElement("hidden","mode","admin");
	if(isset($_REQUEST["member_id"])) {
		$form->addElement("hidden","member_id", $_REQUEST["member_id"]);
	} else {
		$ids = new cMemberGroup;
		$ids->LoadMemberGroup();
		$form->addElement("select", "member_id", l("Escull l'associat/da al que acompanyar"), $ids->MakeIDArray(), array('class' => 'formulari2'));
	}
} else {  // Member is adding to own account
	$cUser->MustBeLoggedOn();
	$form->addElement("hidden","member_id", $cUser->member_id);
	$form->addElement("hidden","mode","self");
}

$form->addElement("text", "first_name", l("Nom"), array("size" => 15, "maxlength" => 20, 'class' => 'formulari2'));
$form->addElement("text", "mid_name", l("Cognom"), array("size" => 10, "maxlength" => 20, 'class' => 'formulari2'));
$form->addElement("text", "last_name", l("Segon cognom"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
$form->addElement("static", null, null, null); 

$today=getdate();
$options = array("language"=> "ca", "format" => "dFY", "maxYear"=>$today["year"], "minYear"=>"1880"); 
$form->addElement("date", "dob", l("Data de naixement"), $options, array('class' => 'formulari2'));
//  $form->addElement("text", "mother_mn", "Mother's Maiden Name", array("size" => 20, "maxlength" => 30)); 
$form->addElement("static", null, null, null);
$form->addElement("select","directory_list", l("Mostrar les dades d'aquesta persona en el directori?"), array("Y"=>"Si", "N"=>"No"), array('class' => 'formulari2'));
$form->addElement("text", "email", l("Correu-e"), array("size" => 25, "maxlength" => 40, 'class' => 'formulari2'));
$form->addElement("text", "phone1", l("Telèfon"), array("size" => 20, 'class' => 'formulari2'));
$form->addElement("text", "phone2", l("Telèfon 2"), array("size" => 20, 'class' => 'formulari2'));
//  $form->addElement("text", "fax", "Fax Number", array("size" => 20));
$form->addElement("static", null, null, null);
$form->addElement("text", "address_street1", l("Direcció"), array("size" => 25, "maxlength" => 30, 'class' => 'formulari2'));
// $form->addElement("text", "address_street2", "Address Line 2", array("size" => 25, "maxlength" => 30));

// En el caso de totbisbal seleccionamos sobre los que hay en la base de datos y quitamos código postal y provincia
if ( isset($DIR_BASE) ) { // Estamos en totbisbal
   $sql = "SELECT p.nom FROM pobles p LEFT JOIN rPoblesLocalitzacio rpl ON rpl.pobleId=p.id WHERE rpl.localitzacioId=1";
   $aPoblacions = sql2array($sql);
   $form->addElement("select", "address_city", l("Població"), $aPoblacions, array('class' => 'formulari2'));
   $form->setDefaults(array('address_city' => '1')); 
} else {
   $form->addElement("text", "address_city", l("Població"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
   $form->addElement("text", "address_post_code", l("Codi Postal"), array("size" => 5, "maxlength" => 6, 'class' => 'formulari2'));
   $form->addElement("text", "address_country", l("Província"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
   }


// TODO: The State and Country codes should be Select Menus, and choices should be built
// dynamically using an internet database (if such exists).
// $form->addElement("text", "address_state_code", STATE_TEXT, array("size" => 2, "maxlength" => 2));
// $form->addElement("text", "address_post_code", ZIP_TEXT, array("size" => 5, "maxlength" => 6));
// $form->addElement("text", "address_country", "Country", array("size" => 20, "maxlength" => 30));
$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', l('Crear contacte'), array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule('password', l('Contrasenya massa curta'), 'minlength', 7);
$form->addRule('first_name', l('Introdueix un nom'), 'required');
$form->addRule('mid_name', l('Introdueix un cognom'), 'required');
$form->addRule('address_city', l('Introdueix una població'), 'required');
// $form->addRule('address_state_code', 'Enter a state', 'required');
// $form->addRule('address_post_code', 'Enter a '.ZIP_TEXT, 'required');
// $form->addRule('address_country', 'Enter a country', 'required');

$form->registerRule('verify_not_future_date','function','verify_not_future_date');
$form->addRule('dob', l('La data de naixement no pot ser en el futur'), 'verify_not_future_date');
$form->registerRule('verify_reasonable_dob','function','verify_reasonable_dob');
$form->addRule('dob', l('Una mica jove, no creus?'), 'verify_reasonable_dob');
$form->registerRule('verify_valid_email','function', 'verify_valid_email');
$form->addRule('email', l('Correu-e no vàlit'), 'verify_valid_email');
$form->registerRule('verify_phone_format','function','verify_phone_format');
$form->addRule('phone1', l('El número de telèfon no és vàlid'), 'verify_phone_format');
$form->addRule('phone2', l('El número de telèfon no és vàlid'), 'verify_phone_format');
// $form->addRule('fax', 'Phone format invalid', 'verify_phone_format');


//
// Check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {
	$today = getdate();
	$current_date = array("Y"=>$today["year"], "F"=>$today["mon"], "d"=>$today["mday"]);
	$defaults = array("dob"=>$current_date, "address_state_code"=>DEFAULT_STATE, "address_country"=>DEFAULT_COUNTRY, "directory_list"=>"Y");
	$form->setDefaults($defaults);
   $p->DisplayPage($form->toHtml());  // just display the form
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
	global $p, $cUser,$cErr, $today;
	$list = "";

	$values['primary_member'] = "N"; 

	$date = $values['dob'];
	$values['dob'] = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
	if($values['dob'] == $today['year']."/".$today['mon']."/".$today['mday'])
		$values['dob'] = ""; // if birthdate was left as default, set to null
	
	$phone = new cPhone($values['phone1']);
	$values['phone1_area'] = $phone->area;
	$values['phone1_number'] = $phone->SevenDigits();
//	$values['phone1_ext'] = $phone->ext;
	$phone = new cPhone($values['phone2']);
	$values['phone2_area'] = $phone->area;
	$values['phone2_number'] = $phone->SevenDigits();
//	$values['phone2_ext'] = $phone->ext;	
//	$phone = new cPhone($values['fax']);
//  $values['fax_area'] = $phone->area;
//	$values['fax_number'] = $phone->SevenDigits();
//	$values['fax_ext'] = $phone->ext;	

	$new_person = new cPerson($values);
	$created = $new_person->SaveNewPerson();
	
	$member = new cMember();
	$member->LoadMember($_REQUEST["member_id"]);
	
	if($created and $member->account_type == "S") {
		$member->account_type = "J";  // Now it's a Joint account
		$member->SaveMember();
	}	

	if($created) {
		$list .= l("Asociado/a acompañante creado").". ".l("Vols")." <A HREF=member_contact_create.php?mode=". $_REQUEST["mode"] ."&member_id=". $values["member_id"] .">".l("afegir una altra persona")."</A>?<P>";
	} else {
		$cErr->Error(l("Hubo un error guardando los datos de la persona. Inténtalo más tarde."));
	}
   $p->DisplayPage($list);
}
//
// The following functions verify form data
//

// TODO: All my validation functions should go into a new cFormValidation class
		
function verify_reasonable_dob($element_name,$element_value) {
	global $today;
	$date = $element_value;
	$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
//	echo $date_str ."=".$today['year']."/".$today['mon']."/".$today['mday'];

	if ($date_str == $today['year']."/".$today['mon']."/".$today['mday']) 
		// date wasn't changed by user, so no need to verify it
		return true;
	elseif ($today['year'] - $date['Y'] < 17)  // A little young to be trading, presumably a mistake
		return false;
	else
		return true;
}

function verify_not_future_date ($element_name,$element_value) {
	$date = $element_value;
	$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

	if (strtotime($date_str) > strtotime("now"))
		return false;
	else
		return true;
}

// TODO: This simplistic function should ultimately be replaced by this class method on Pear:
// 		http://pear.php.net/manual/en/package.mail.mail-rfc822.intro.php
function verify_valid_email ($element_name,$element_value) {
	if ($element_value=="")
		return true;		// Currently not planning to require this field
	if (strstr($element_value,"@") and strstr($element_value,"."))
		return true;	
	else
		return false;
	
}

function verify_phone_format ($element_name,$element_value) {
    $phone = new cPhone($element_value);
    
   if(substr($phone->area,0,1)== "9" or substr($phone->area,0,1)== "8" or substr($phone->area,0,1)=="6")
        return true;
    else
        return false;
}

?>
