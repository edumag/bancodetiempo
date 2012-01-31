<?php 

include_once("includes/inc.global.php");

$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");
require_once "Mail.php";
require_once "Mail/mime.php"; 
//
// First, we define the form
//
if($_REQUEST["mode"] == "admin") {  // Administrator is editing a member's account
	$cUser->MustBeLevel(1);
	$member = new cMember;
	$member->LoadMember($_REQUEST["member_id"]);
	$form->addElement("header", null, l("Edita usuari/ària") . $member->person[0]->first_name . " " . $member->person[0]->last_name);

	$form->addElement("html", "<TR></TR>");
	$form->addElement("hidden","mode","admin");
	$form->addElement("hidden","member_id",$_REQUEST["member_id"]);
	$form->addElement("select", "status", l("Acceptar com usuari/ària"), array("A"=>l("Si"), "X"=>l("No")), array('class' => 'formulari2'));
	if($_REQUEST["member_id"] == "ADMIN") {
		$form->addElement("hidden","member_role","9");
	} else {
		$form->addElement("select", "member_role", l("Tipus de membre"), array("0"=>l("Usuari/ària"), "1"=>l("Administrador nivell 1"), "2"=>l("Administrador nivell 2")), array('class' => 'formulari2'));
	}
	//$acct_types = array("S"=>"Single", "J"=>"Joint", "H"=>"Household", "O"=>"Organization", "B"=>"Business", "F"=>"Fund");
	//$form->addElement("select", "account_type", "Account Type", $acct_types);
	$form->addElement("static", null, l("Notas de l'administrador/a"), null);
	$form->addElement("textarea", "admin_note", null, array("cols"=>45, "rows"=>2, "wrap"=>"soft", "maxlength" => 100, 'class' => 'formulari2'));
	
	$today = getdate();
	$options = array("language"=> "ca", "format" => "dFY", "minYear"=>JOIN_YEAR_MINIMUM, "maxYear"=>$today["year"]);
	$form->addElement("date", "join_date", l("Data d'inscripció"), $options, array('class' => 'formulari2'));
	$options = array("language"=> "ca", "format" => "dFY", "maxYear"=>$today["year"], "minYear"=>"1880"); 
	$form->addElement("date", "dob", l("Data de naixament"), $options, array('class' => 'formulari2'));
	$form->addElement("static", null, null, null);	
	$update_text=l("Amb quina freqüència rebrà correu-e?");
} else {  // Member is editing own profile
	$cUser->MustBeLoggedOn();
	$form->addElement("header", null, l("Editar perfil personal"));
	$form->addElement("html", "<TR></TR>");
	$form->addElement("hidden","member_id", $cUser->member_id);
	$form->addElement("hidden","mode","self");
	$update_text=l("Amb quina freqüència rebrà correu-e?");
}	
$form->addElement("text", "first_name", l("Nom"), array("size" => 15, "maxlength" => 20, 'class' => 'formulari2'));
$form->addElement("text", "last_name", l("Cognom"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
$form->addElement("text", "mid_name", l("Segon cognom"), array("size" => 10, "maxlength" => 20, 'class' => 'formulari2'));

$form->addElement("static", null, null, null); 

// $form->addElement("text", "mother_mn", "Mother's Maiden Name", array("size" => 20, "maxlength" => 30)); 
$form->addElement("static", null, null, null);
$form->addElement("text", "email", l("Correu-e"), array("size" => 25, "maxlength" => 40, 'class' => 'formulari2'));
$form->addElement("text", "phone1", l("Telèfon"), array("size" => 20, 'class' => 'formulari2'));
$form->addElement("text", "phone2", l("Telèfon 2"), array("size" => 20, 'class' => 'formulari2'));
// $form->addElement("text", "fax", "Fax Number", array("size" => 20));
$form->addElement("static", null, null, null);
$frequency = array("0"=>l("Mai"), "1"=>l("Diàriament"), "7"=>l("Setmanalment"), "30"=>l("Mensualment"));
$form->addElement("select", "email_updates", $update_text, $frequency, array('class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement("text", "address_street1", l("Adreça"), array("size" => 25, "maxlength" => 30, 'class' => 'formulari2'));
// $form->addElement("text", "address_street2", "Address Line 2", array("size" => 25, "maxlength" => 30));

// En el caso de totbisbal seleccionamos sobre los que hay en la base de datos y quitamos código postal y provincia
if ( $config['Proyecto'] == 'totbisbal' ) { // Estamos en totbisbal
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
$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', l('Edita usuari/ària'), array('class' => 'formulari2'));
//
// Define form rules
//
$form->addRule('password', l('Contrasenya massa curta'), 'minlength', 7);
$form->addRule('first_name', l('Introdueix un nom'), 'required');
// $form->addRule('mid_name', 'Introduce un apellido', 'required');
// Por alguna razón que no conozco, si se quita la siguiente línea el programa da un error. 
$form->addRule('last_name', l('Introdueix un cognom'), 'required');
$form->addRule('address_city', l('Introdueix una població'), 'required');
// $form->addRule('address_state_code', 'Enter a state', 'required');
// $form->addRule('address_post_code', 'Enter a '.ZIP_TEXT, 'required');
// $form->addRule('address_country', 'Enter a country', 'required');

$form->registerRule('verify_not_future_date','function','verify_not_future_date');
$form->addRule('dob', l('El naixement no pot ser en el futur'), 'verify_not_future_date');
$form->registerRule('verify_reasonable_dob','function','verify_reasonable_dob');
$form->addRule('dob', l('Una mica jove, no creus?'), 'verify_reasonable_dob');
$form->registerRule('verify_valid_email','function', 'verify_valid_email');
$form->addRule('email', l('Correu-e no vàlid'), 'verify_valid_email');
$form->registerRule('verify_phone_format','function','verify_phone_format');
$form->addRule('phone1', l('El número de telèfon no és vàlid'), 'verify_phone_format');
$form->addRule('phone2', l('El número de telèfon no és vàlid'), 'verify_phone_format');
// $form->addRule('fax', 'Phone format invalid', 'verify_phone_format');

//$form->registerRule('verify_unique_member_id','function','verify_unique_member_id');
//$form->addRule('member_id',l('Aquest identificador ja està sent usat'),'verify_unique_member_id');
$form->registerRule('verify_good_member_id','function','verify_good_member_id');
$form->addRule('member_id',l('No es permeten caràcters especials'),'verify_good_member_id');
$form->registerRule('verify_good_password','function','verify_good_password');
$form->addRule('password', l('La contrasenya ha de contenir almenys un número'), 'verify_good_password');
$form->registerRule('verify_no_apostraphes_or_backslashes','function','verify_no_apostraphes_or_backslashes');
$form->addRule("password", l("És millor no utilitzar apòstrofs o cometes en contrasenyes"), "verify_no_apostraphes_or_backslashes");
$form->registerRule('verify_role_allowed','function','verify_role_allowed');
$form->addRule('member_role',l('No pots assignar un major nivell d\'accés del que tens'),'verify_role_allowed');
$form->addRule('join_date', l('La data no pot estar en el futur'), 'verify_not_future_date');


//
// Check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Otherwise we need to load the existing values
	$member = new cMember;
	if($_REQUEST["mode"] == "admin")
		$member->LoadMember($_REQUEST["member_id"]);
	else
		$member = $cUser;

	$current_values = array ( "member_id"=>$member->member_id, "first_name"=>$member->person[0]->first_name, "mid_name"=>$member->person[0]->mid_name, "last_name"=>$member->person[0]->last_name, "email"=>$member->person[0]->email, "phone1"=>$member->person[0]->DisplayPhone(1), "phone2"=>$member->person[0]->DisplayPhone(2), "fax"=>$member->person[0]->DisplayPhone("fax"), "email_updates"=>$member->email_updates, "address_street1"=>$member->person[0]->address_street1, "address_street2"=>$member->person[0]->address_street2, "address_city"=>$member->person[0]->address_city, "address_state_code"=>$member->person[0]->address_state_code, "address_post_code"=>$member->person[0]->address_post_code, "address_country"=>$member->person[0]->address_country);
	
	if($_REQUEST["mode"] == "admin") {  // Load defaults for extra fields visible by administrators
		$current_values["member_role"] = $member->member_role;
		$current_values["account_type"] = $member->account_type;
		$current_values["admin_note"] = $member->admin_note;
		$current_values["join_date"] = array ('d'=>substr($member->join_date,8,2),'F'=>date('n',strtotime($member->join_date)),'Y'=>substr($member->join_date,0,4));
		$current_values["mother_mn"] = $member->person[0]->mother_mn;
		
		if ($member->person[0]->dob) {		
			$current_values["dob"] = array ('d'=>substr($member->person[0]->dob,8,2),'F'=>date('n',strtotime($member->person[0]->dob)),'Y'=>substr($member->person[0]->dob,0,4));  // Using 'n' due to a bug in Quickform
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
	global $p, $cUser,$cErr, $today;
	$list = "";

	$member = new cMember;
	if($_REQUEST["mode"] == "admin") 
		$member->LoadMember($_REQUEST["member_id"]);
	else
		$member = $cUser;

	if($_REQUEST["mode"] == "admin")	{
		$member->member_role = $values["member_role"];
// La siguiente línea es la única que cambia respecto a member_edit.php (junto con el cambio en el form)
		$member->status = $values["status"];
		$member->account_type = $values["account_type"];
		$member->admin_note = $values["admin_note"];
		$member->person[0]->mother_mn = $values["mother_mn"];
		$date = $values['join_date'];
		$member->join_date = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
		$date = $values['dob'];
		$dob = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
//		echo $dob ."=". $today['year']."/".$today['mon']."/".$today['mday'];
		if($dob != $today['year']."/".$today['mon']."/".$today['mday']) { 
			$member->person[0]->dob = $dob; 
		} // if date left as default (today's date), we don't want to set it
	} 

// TODO: Add ability to temporarily disable an account (vacation) or to disable altogether (left 4th Corner).  Also add ability for user to add a personal note.
	$member->person[0]->first_name = $values["first_name"];
	$member->person[0]->mid_name = $values["mid_name"];
	$member->person[0]->last_name = $values["last_name"];
	$member->person[0]->email = $values["email"];
	$member->email_updates = $values["email_updates"];
	$member->person[0]->address_street1 = $values["address_street1"];
	$member->person[0]->address_street2 = $values["address_street2"];
	$member->person[0]->address_city = $values["address_city"];
	$member->person[0]->address_state_code = $values["address_state_code"];
	$member->person[0]->address_post_code = $values["address_post_code"];
	$member->person[0]->address_country = $values["address_country"];	

	$phone = new cPhone($values['phone1']);
	$member->person[0]->phone1_area = $phone->area;
	$member->person[0]->phone1_number = $phone->SevenDigits();
//	$member->person[0]->phone1_ext = $phone->ext;
	$phone = new cPhone($values['phone2']);
	$member->person[0]->phone2_area = $phone->area;
	$member->person[0]->phone2_number = $phone->SevenDigits();
//	$member->person[0]->phone2_ext = $phone->ext;	
//	$phone = new cPhone($values['fax']);
//	$member->person[0]->fax_area = $phone->area;
//	$member->person[0]->fax_number = $phone->SevenDigits();
//	$member->person[0]->fax_ext = $phone->ext;	
	
	if($member->SaveMember()) {
		$list .= l("Cambios realizados").". <b>".l("Añade una foto y genera el carnet de socio")."</b> ".l("pulsando")." <A HREF=member_edit.php?type=".$_REQUEST["type"]."&mode=".$_REQUEST["mode"]."&member_id=".$member->member_id.">".l("aquí")."</A>. ";
        if ($values['status']=="A")
        {
            if ($values ['email']=="")
             $list .= l("Esta persona tendrá que ser informada de que se ha activado su cuenta ya que no ha facilitado un correo electrónico").". "; 
            else
            {
            $text = ACTIVE_MEMBER_MESSAGE . "\n\n".l("Usuari/ària").": ". $values['member_id'];
            $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.nl2br($text).LOPD.AVISO_LEGAL); 
            $to = $values['email'];
            $crlf = "\n";
            $headers = array ('From' => EMAIL_FROM,
            'To' => $to,
            'Subject' => ACTIVE_MEMBER_SUBJECT);
            $mime = new Mail_mime($crlf);
            $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
            $mime->setTXTBody($text);
            $mime->setHTMLBody($html); 
            $body = $mime->get();
            $headers = $mime->headers($headers);
            
            $smtp = Mail::factory('mail');
            $mailed = $smtp->send($to, $headers, $body);

            if (PEAR::isError($mailed)) {
              $list .= l("Ha fallado el intento de enviar un corero-e").". ".l("Probablemente se deba a un problema técnico").". ".l("Contacta con tu administrador en")." ". PHONE_ADMIN .". <I>".l("El nuevo asociado/a debe ser informado del id")." ('". $values["member_id"]. "') ".l("y la contraseña")." ('". $values["password"] ."').</I>";
               } else {
                 $list .= l("Se ha enviado un email a")." '". $values["email"] ."' ".l("informándole de que su cuenta ha sido activada").".";
                  } 
            }
        }
        } else {
		$cErr->Error("".l("Ha habido un error. Prueba más tarde o avisa a nuestro administrador/a").".");
	}
   $p->DisplayPage($list);
}
//
// The following functions verify form data
//

// TODO: All my validation functions should go into a new cFormValidation class

function verify_good_member_id ($element_name,$element_value) {
	if(ctype_alnum($element_value)) { // it's good, so return immediately & save a little time
		return true;
	} else {
		$member_id = ereg_replace("\_","",$element_value);
		$member_id = ereg_replace("\-","",$member_id);
		$member_id = ereg_replace("\.","",$member_id);
		if(ctype_alnum($member_id))  // test again now that we've stripped the allowable special chars
			return true;		
	}
}

function verify_role_allowed($element_name,$element_value) {
	global $cUser;
	if($element_value > $cUser->member_role)
		return false;
	else
		return true;
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

function verify_good_password($element_name,$element_value) {
	$i=0; $upper=false; $lower=false; $number=false; $punct=false;
	$length=strlen($element_value);
	
	while($i<$length) {
		if(ctype_upper($element_value{$i}))
			$upper=true;
		if(ctype_lower($element_value{$i}))
			$lower=true;
		if(ctype_punct($element_value{$i}))
			$punct=true;
		if(ctype_digit($element_value{$i}))
			$number=true;	
		$i+=1;
	}
	
	if($upper and $lower and ($number or $punct))
		return true;
	else
		return false;
}

function verify_no_apostraphes_or_backslashes($element_name,$element_value) {
	if(strstr($element_value,"'") or strstr($element_value,"\\"))
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
