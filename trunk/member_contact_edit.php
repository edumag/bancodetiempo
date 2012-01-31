<?php 

include_once("includes/inc.global.php");

//
//  Todos los archivos que empiezan por 'member contact' están referidos 
//  a los 'acompañantes', figura que no estamos utilizando en esta versión
//  del programa. 
//  Por tanto, estos formularios NO ESTÁN ACTUALIZADOS
//


$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");

//
// First, we define the form
//
if($_REQUEST["mode"] == "admin") {  // Administrator is editing a member's account
	$cUser->MustBeLevel(1);
	$form->addElement("hidden","mode","admin");
	$form->addElement("hidden","member_id",$_REQUEST["member_id"]);		
} else {  // Member is editing own account
	$cUser->MustBeLoggedOn();
	$cUser->VerifyPersonInAccount($_REQUEST["person_id"]); // Make sure hacker didn't change URL
	$form->addElement("hidden","member_id", $cUser->member_id);
	$form->addElement("hidden","mode","self");
}

$person = new cPerson;
$person->LoadPerson($_REQUEST["person_id"]);
$form->addElement("header", null, "".l("Editar acompañante")." " . $person->first_name . " " . $person->mid_name);
$form->addElement("html", "<TR></TR>");

$form->addElement("hidden","person_id",$_REQUEST["person_id"]);
$form->addElement("text", "first_name", l("Nom"), array("size" => 15, "maxlength" => 20, 'class' => 'formulari2'));
$form->addElement("text", "mid_name", l("Cognom"), array("size" => 10, "maxlength" => 20, 'class' => 'formulari2'));
$form->addElement("text", "last_name", l("Segon cognom"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
$form->addElement("static", null, null, null);

if ($_REQUEST["mode"] == "admin") {
	$today = getdate();
	$options = array("language"=> "ca", "format" => "dFY", "maxYear"=>$today["year"], "minYear"=>"1880");
	$form->addElement("date", "dob", l("Data de naixement"), $options);
//	$form->addElement("text", "mother_mn", "Mother's Maiden Name", array("size" => 20, "maxlength" => 30)); 
	$form->addElement("static", null, null, null);
}

$form->addElement("select","directory_list", l("Mostrar dades d'aquesta persona en el Directori?"), array("Y"=>l("Si"), "N"=>l("No"), 'class' => 'formulari2'));
$form->addElement("text", "email", l("Correu-e"), array("size" => 25, "maxlength" => 40, 'class' => 'formulari2'));
$form->addElement("text", "phone1", l("Telèfon"), array("size" => 20, 'class' => 'formulari2'));
$form->addElement("text", "phone2", l("Telèfon 2"), array("size" => 20, 'class' => 'formulari2'));
// $form->addElement("text", "fax", "Fax Number", array("size" => 20));
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

// TODO: Add the ability to make this person the primary member on the account

$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', l('Enviar'), array('class' => 'formulari2'));

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
} else {  // Otherwise we need to load the existing values			
	$current_values = array ("first_name"=>$person->first_name, "mid_name"=>$person->mid_name, "last_name"=>$person->last_name, "directory_list"=>$person->directory_list, "email"=>$person->email, "phone1"=>$person->DisplayPhone(1), "phone2"=>$person->DisplayPhone(2), "fax"=>$person->DisplayPhone("fax"), "address_street1"=>$person->address_street1, "address_street2"=>$person->address_street2, "address_city"=>$person->address_city, "address_state_code"=>$person->address_state_code, "address_post_code"=>$person->address_post_code, "address_country"=>$person->address_country);
	
	if($_REQUEST["mode"] == "admin") {  // Load defaults for extra fields visible by administrators
		$current_values["mother_mn"] = $person->mother_mn;
		
		if ($person->dob) {		
			$current_values["dob"] = array ('d'=>substr($person->dob,8,2),'F'=>date('n',strtotime($person->dob)),'Y'=>substr($person->dob,0,4));  // Using 'n' due to a bug in Quickform
		} else { // If date of birth was left empty originally, display default date
			$today = getdate();
			$current_values["dob"] = array ('d'=>$today['mday'],'F'=>$today['mon'],'Y'=>$today['year']);
		}		
	}	
		
	$form->setDefaults($current_values);
   $p->DisplayPage($form->toHtml());  // display the form
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
	global $p, $cUser, $cErr, $person, $today;
	$list = "";

	$person->first_name = $values["first_name"];
	$person->mid_name = $values["mid_name"];
	$person->last_name = $values["last_name"];
	$person->directory_list = $values["directory_list"];
	$person->email = $values["email"];
	$person->address_street1 = $values["address_street1"];
	$person->address_street2 = $values["address_street2"];
	$person->address_city = $values["address_city"];
	$person->address_state_code = $values["address_state_code"];
	$person->address_post_code = $values["address_post_code"];
	$person->address_country = $values["address_country"];	

	$phone = new cPhone($values['phone1']);
	$person->phone1_area = $phone->area;
	$person->phone1_number = $phone->SevenDigits();
//	$person->phone1_ext = $phone->ext;
	$phone = new cPhone($values['phone2']);
	$person->phone2_area = $phone->area;
	$person->phone2_number = $phone->SevenDigits();
//	$person->phone2_ext = $phone->ext;	
//	$phone = new cPhone($values['fax']);
//	$person->fax_area = $phone->area;
//	$person->fax_number = $phone->SevenDigits();
//	$person->fax_ext = $phone->ext;	
	
	if($_REQUEST["mode"] == "admin")	{
		$person->mother_mn = $values["mother_mn"];
		$date = $values['dob'];
		$dob = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
//		echo $dob ."=". $today['year']."/".$today['mon']."/".$today['mday'];
		if($dob != $today['year']."/".$today['mon']."/".$today['mday']) { 
			$person->dob = $dob; 
		} // if date left as default (today's date), we don't want to set it
	} 	
	
	if($person->SavePerson()) {
		$list .= l("Cambios guardados.");
	} else {
		$cErr->Error(l("Ha habido un error guardando los cambios. Inténtalo más tarde."));
	}
   $p->DisplayPage($list);
}
//
// The following functions verify form data
//

// TODO: All my validation functions should go into a new cFormValidation class

function verify_no_apostraphes_or_backslashes($element_name,$element_value) {
	if(strstr($element_value,"'") or strstr($element_value,"\\"))
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

?>
