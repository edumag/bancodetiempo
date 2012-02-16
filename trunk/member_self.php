<?php 

/**
 * @file member_self.php
 *
 * @todo Parece que el envio de emails da problemas con algunos servidores, en barrisdemontjuic 
 *       hemos cambiado algunos archivos quitando la funcion iconv que convierte el texto a la
 *       codificación de windows. Se debería centralizar el envio en una función y trabajar 
 *       unicamente en UTF8
 */

include_once("includes/inc.global.php");

//$cUser->MustBeLevel(1);
$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");
require_once('HTML/QuickForm/textarea.php');
require_once "Mail.php";
require_once "Mail/mime.php";
//
// First, we define the form
//
$form->addElement("header", null, l("Inscriure's al Banc de Temps"));
$form->addElement("static", null, "".l("Benvingut al Banc de Temps").". ".l("Inscriu-te aquí per poder enviar ofertes i demandes, i realitzar intercanvis amb altres persones").".<br> ".l("Després d'inscriure't aquí hauràs d'esperar que la persona que administra el Banc accepti la teva petició, i t'enviï un correu confirmant la teva participació").".<br>",null);

$form->addElement("html", "<TR></TR>");
$form->addElement("text", "member_id", l("Identificador"), array("size" => 10, "maxlength" => 15, 'class' => 'formulari2'));
$form->addElement("static", null, l("(L'identificador és un nom que utilitzaràs per connectar-te, és important que ho recordis, serà també el nom que apareixerà públicament i fins i tot, per als no membres, així que utilitza un àlies si vols anonimat)").".<br>", null);
$form->addElement("text", "password", l("Clau (mínim set caràcters, amb almenys un de numèric)"), array("size" => 10, "maxlength" => 15, 'class' => 'formulari2'));
// $form->addElement("select", "member_role", "Member Role", array("0"=>"Member", "1"=>"Administrator Level 1", "2"=>"Administrator Level 2"));
//$acct_types = array("S"=>"Single", "J"=>"Joint", "H"=>"Household", "O"=>"Organization", "B"=>"Business", "F"=>"Fund");
//$form->addElement("select", "account_type", "Account Type", $acct_types);
// $form->addElement("static", null, "Administrator Note", null);
// $form->addElement("textarea", "admin_note", null, array("cols"=>45, "rows"=>2, "wrap"=>"soft", "maxlength" => 100));

$today = getdate();
$options = array("language"=> "ca", "format" => "dFY", "minYear"=>JOIN_YEAR_MINIMUM, "maxYear"=>$today["year"]);
$form->addElement("date", "join_date", l("Data d'inscripció"), $options, array('class' => 'formulari2'));
$form->addElement("static", null, null, null);	

$form->addElement("text", "first_name", l("Nom"), array("size" => 15, "maxlength" => 20, 'class' => 'formulari2'));
$form->addElement("text", "last_name", l("Cognom"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
$form->addElement("text", "mid_name", l("Segon cognom"), array("size" => 10, "maxlength" => 20, 'class' => 'formulari2'));

$form->addElement("static", null, null, null); 

$options = array("language"=> "ca", "format" => "dFY", "maxYear"=>$today["year"], "minYear"=>"1880"); 
$form->addElement("date", "dob", l("Data de naixement"), $options, array('class' => 'formulari2'));
// $form->addElement("text", "mother_mn", "Mother's Maiden Name", array("size" => 20, "maxlength" => 30)); 
$form->addElement("static", null, null, null);
$form->addElement("text", "email", l("Correu-e"), array("size" => 25, "maxlength" => 40, 'class' => 'formulari2'));
$form->addElement("text", "phone1", l("Telèfon"), array("size" => 20, 'class' => 'formulari2'));
$form->addElement("text", "phone2", l("Telèfon 2"), array("size" => 20, 'class' => 'formulari2'));
// $form->addElement("text", "fax", "Fax Number", array("size" => 20));
$form->addElement("static", null, null, null);
$frequency = array("0"=>l("Mai"), "1"=>l("Diàriament"), "7"=>l("Setmanalment"), "30"=>l("Mensualment"));
$form->addElement("select", "email_updates", l("Amb quina freqüència vols rebre e-mails?"), $frequency, array('class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement("text", "address_street1", l("Adreça"), array("size" => 25, "maxlength" => 30, 'class' => 'formulari2'));
// $form->addElement("text", "address_street2", "Address Line 2", array("size" => 25, "maxlength" => 30));

// TODO: The State and Country codes should be Select Menus, and choices should be built
// dynamically using an internet database (if such exists).
// $form->addElement("text", "address_state_code", STATE_TEXT, array("size" => 2, "maxlength" => 2));

// En el caso de totbisbal seleccionamos sobre los que hay en la base de datos y quitamos código postal y provincia
if ( DATABASE_NAME == 'totbisbalcom33' ) { // Estamos en totbisbal
   $sql = "SELECT p.nom FROM pobles p LEFT JOIN rPoblesLocalitzacio rpl ON rpl.pobleId=p.id WHERE rpl.localitzacioId=1";
   $aPoblacions = sql2array($sql);
   $form->addElement("select", "address_city", l("Població"), $aPoblacions, array('class' => 'formulari2'));
   $form->setDefaults(array('address_city' => '1')); 
} else {
   $form->addElement("text", "address_city", l("Població"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
   $form->addElement("text", "address_post_code", l("Codi Postal"), array("size" => 5, "maxlength" => 6, 'class' => 'formulari2'));
   $form->addElement("text", "address_country", l("Província"), array("size" => 20, "maxlength" => 30, 'class' => 'formulari2'));
   }

$form->addElement("static", null, null, null);
$agreement = new HTML_QuickForm_textarea('agreement', 'agreement', array('cols'=>60,'rows'=>10,'readonly'=>'readonly', 'class' => 'formulari2'));

$parrafada = get_include_contents(fl('clausula.php'));

$agreement->setValue($parrafada);  
$form->addElement("static", null, l("Condicions legals i d'ús"),null);
$form->addElement('static', 'valid',null, $agreement->toHtml()); 

$form->addElement('checkbox','acepto',null,l('Accepto les condicions'), array('class' => 'formulari2'));

$form->addElement("static", null, null, null);
$form->addElement('submit', 'btnSubmit', l('Crear soci'), array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule('password', l('Clau massa curta'), 'minlength', 7);
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

$form->registerRule('verify_unique_member_id','function','verify_unique_member_id');
$form->addRule('member_id',l('Aquest identificador ja està sent usat'),'verify_unique_member_id');
$form->registerRule('verify_good_member_id','function','verify_good_member_id');
$form->addRule('member_id',l('No es permeten caràcters especials'),'verify_good_member_id');
$form->registerRule('verify_good_password','function','verify_good_password');
$form->addRule('password', l('La contrasenya ha de contenir almenys un número'), 'verify_good_password');
$form->registerRule('verify_no_apostraphes_or_backslashes','function','verify_no_apostraphes_or_backslashes');
$form->addRule("password", l("És millor no utilitzar apòstrofs o cometes en contrasenyes"), "verify_no_apostraphes_or_backslashes");
$form->registerRule('verify_role_allowed','function','verify_role_allowed');
$form->addRule('member_role',l('No pots assignar un major nivell d´accés del que tens'),'verify_role_allowed');
$form->addRule('join_date', l('La data no pot ser futura'), 'verify_not_future_date');



//
// Check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {
	$today = getdate();
	$current_date = array("Y"=>$today["year"], "F"=>$today["mon"], "d"=>$today["mday"]);
	$defaults = array("password"=>$cUser->GeneratePassword(), "dob"=>$current_date, "join_date"=>$current_date, "account_type"=>"S", "member_role"=>"0", "email_updates"=>DEFAULT_UPDATE_INTERVAL, "address_state_code"=>DEFAULT_STATE, "address_city"=>DEFAULT_CITY, "address_country"=>DEFAULT_COUNTRY);
	$form->setDefaults($defaults);
   $p->DisplayPage($form->toHtml());  // just display the form
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
	global $p, $cUser,$cErr, $today;
	$list = "";

	// Following are default values for which this form doesn't allow input
	$values['security_q'] = "";
	$values['security_a'] = "";
	$values['status'] = "X";
	$values['member_note'] = "";
	$values['expire_date'] = "";
	$values['away_date'] = "";
	$values['balance'] = 3;
	$values['primary_member'] = "Y";
	$values['directory_list'] = "Y";

	$date = $values['join_date'];
	$values['join_date'] = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
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
//	$values['fax_area'] = $phone->area;
//	$values['fax_number'] = $phone->SevenDigits();
//	$values['fax_ext'] = $phone->ext;	

	$new_member = new cMember($values);
	$new_person = new cPerson($values);

	if($created = $new_person->SaveNewPerson()) 
		$created = $new_member->SaveNewMember();

	  if($created) {
        $list .= l("Ja has estat creat, ara hauràs d'esperar una mica a que et responguem").".<br>\n".l("Recorda que el teu nom d'associat es")." <strong>". $values["member_id"]. "</strong> ".l("i la teva clau")." <strong>". $values["password"] . "</strong><br><br>".l("Prem")." <A HREF=member_create.php>".l("aquí")."</A> ".l("si vols crear un altre compte").".";
        if($values['email'] == "") {
        $list .= "<br>".l("Normalment avisem per email, però no has donat cap").". ".l("Espera uns pocs dies i fes la prova per veure si et pots donar d'alta").". ".l("Si tens pressa, posa't en contacte amb el Banc de Temps per telèfon").". ";
        } else {
            //$text = NEW_MEMBER_PENDING . "\n\nIdentificador: ". $values['member_id'] ."\n".; "Contrasenya: ". ['password'];
//            $text = NEW_MEMBER_PENDING . "\n\nIdentificador: ". $values['member_id'] ."\n". "Contrasenya: ". $values['password'];
            $text = NEW_MEMBER_PENDING . "\n\n".l("Identificador").": <strong>". $values['member_id'] ."</strong>\n". "".l("Clau").": <strong>". $values['password']." </strong>";
            // $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.nl2br($text).AVISO_LEGAL);   
            $html = ROTULO_MAIL.nl2br($text).AVISO_LEGAL;   
//*****modificado por pablo 07-12-2010 para que me envie un correo a una dirección fija y asi saber que hay un usuario nuevo esperando
//mal: que se ve en enviado que el adm recibe un correo donde está la password del usuario****
//mejorar: ponerlo como copia certificada****

//            $to = $values['email']; 
$to  = $values['email'] . ', '; // ojo con la comma *********
$to .= EMAIL_ADMIN;

//$to  = 'aidan@example.com' . ', '; 
//$to .= 'wez@example.com';
//----------------------------------------------------------
// To send HTML mail, the Content-type header must be set
// $headers  = 'MIME-Version: 1.0' . "\r\n";
// $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//  Additional headers
// $headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
// $headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
// $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
// $headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
// Mail it
// mail($to, $subject, $message, $headers);// 
	
		
            $crlf = "\n";
            $headers = array ('From' => EMAIL_FROM,
            'To' => $to,
            'Subject' => NEW_MEMBER_SUBJECT);
            $mime = new Mail_mime($crlf);
            $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
            $mime->setTXTBody(strip_tags($text));
            $mime->setHTMLBody($html);
            $body = $mime->get();
            $headers = $mime->headers($headers);
            
            $smtp = Mail::factory('mail');
            $mailed = $smtp->send($to, $headers, $body);

            if (PEAR::isError($mailed)) {
               $list .= "<br><br>".l("Ha fallat l'intent d'enviar un correu, probablement per problemes tècnics").". ".l("En tot cas, el sistema ha recollit la teva inscripció i en uns pocs dies et donarem d'alta").". ";
               } else {
               $list .= "<br>".l("Hem enviat un correu-e a la teva adreça")." '". $values["email"] ."'.<br />".l("Si tens pressa, posa't en contacte amb nosaltres per telèfon")." ".PHONE_ADMIN.".";
                  } 
        }
    } else {
		$cErr->Error(l("Hi ha hagut un error gravant les teves dades").". ".l("Prova una mica més tard").".");
	}
   $p->DisplayPage($list);
}
//
// The following functions verify form data
//

// TODO: All my validation functions should go into a new cFormValidation class

function verify_unique_member_id ($element_name,$element_value) {
	$member = new cMember();
	
	return !($member->LoadMember($element_value, false));
}

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
	$i=0;
	$length=strlen($element_value);
	
	while($i<$length) {
		if(ctype_digit($element_value{$i}))
			return true;	
		$i+=1;
	}
	
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
    
    if(substr($phone->area,0,1)== "9" or substr($phone->area,0,1)=="6") 
        return true;
    else
        return false;
}

?>
