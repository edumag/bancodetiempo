<?php 

include_once("includes/inc.global.php");

$cUser->MustBeLevel(1);
$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");
require_once "Mail.php";
require_once "Mail/mime.php";  
//
// First, we define the form
//
$form->addElement("header", null, l("Crear nou soci"));
$form->addElement("html", "<TR></TR>");


$form->addElement("text", "member_id", l("Identificador de soci"), array("size" => 10, "maxlength" => 15, 'class' => 'formulari2'));
$form->addElement("text", "password", l("Clau"), array("size" => 10, "maxlength" => 15, 'class' => 'formulari2'));

$form->addElement("select", "member_role", l("Rol"), array("0"=>l("Usuari/ària"), "1"=>l("Administrador nivell 1"), "2"=>l("Administrador nivell 2")), array('class' => 'formulari2'));
// $acct_types = array("S"=>"Normal", "J"=>"Acompañante", "H"=>"Hogar", "O"=>"Organización", "B"=>"Negocios", "F"=>"Asociación");
// $form->addElement("select", "account_type", "Tipo de cuenta", $acct_types);
$form->addElement("static", null, l("Notes de l'administrador"), null);
$form->addElement("textarea", "admin_note", null, array("cols"=>45, "rows"=>2, "wrap"=>"soft", "maxlength" => 100, 'class' => 'formulari2'));

$today = getdate();
$options = array("language"=> "ca", "format" => "dFY", "minYear"=>JOIN_YEAR_MINIMUM, "maxYear"=>$today["year"]);
$form->addElement("date", "join_date", l("Data de inscripció"), $options, array('class' => 'formulari2'));
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
$form->addElement("select", "email_updates", l("Amb quina freqüència rebrà correu-e?"), $frequency, array('class' => 'formulari2'));
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
$form->addElement('submit', 'btnSubmit', l('Crear soci'), array('class' => 'formulari2'));

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

$form->registerRule('verify_unique_member_id','function','verify_unique_member_id');
$form->addRule('member_id',l('Aquest identificador ja està sent usat'),'verify_unique_member_id');
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
    $values['status'] = "A";
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
//  $values['phone1_ext'] = $phone->ext;
    $phone = new cPhone($values['phone2']);
    $values['phone2_area'] = $phone->area;
    $values['phone2_number'] = $phone->SevenDigits();
//  $values['phone2_ext'] = $phone->ext;    
//  $phone = new cPhone($values['fax']);
//  $values['fax_area'] = $phone->area;
//  $values['fax_number'] = $phone->SevenDigits();
//  $values['fax_ext'] = $phone->ext;    


    $new_member = new cMember($values);
    $new_person = new cPerson($values);

    if($created = $new_person->SaveNewPerson()) 
        $created = $new_member->SaveNewMember();

    if($created) {
        $list .= l("Associat creat").". ".l("Clica")." <A HREF=member_create.php>".l("aquí")." </A>".l("per crear un altre").".<P>";
        $list .= l("També pots")." <b>".l("afedir una foto i generar el carnet de soci")."</b> ".l("pulsando")." <A HREF=member_edit.php?type=".$_REQUEST["type"]."&mode=admin&member_id=".$values['member_id'].">".l("aquí")."</A>. ";  
        if($values['email'] == "") {
            $list .= l("Aquesta persona haurá de ser informada de l'identificador d'asociat/da")." ('". $values["member_id"]. "') i de la clau ('". $values["password"] ."').";
        } else {

            $text = NEW_MEMBER_MESSAGE . "\n\n".l("Identificador").": <strong>". $values['member_id'] ."</strong>\n". "".l("Clau").": <strong>". $values['password']." </strong>";
            $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.nl2br($text).LOPD.AVISO_LEGAL);
            $to = $values['email'];
            $crlf = "\n";
            $headers = array ('From' => EMAIL_FROM,
            'To' => $to,
            'Subject' => NEW_MEMBER_SUBJECT);
            $mime = new Mail_mime($crlf);
            $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
            $mime->setTXTBody($text);
            $mime->setHTMLBody($html); 
            $body = $mime->get();
            $headers = $mime->headers($headers);
            
            $smtp = Mail::factory('mail');
            $mailed = $smtp->send($to, $headers, $body);

            if (PEAR::isError($mailed)) {
              $list .= "Ha fallat l'intent d'enviar un email. Probablement es degui a un problema tècnic. Contacta amb l'administrador a ". PHONE_ADMIN .". <I>El nou associat ha de ser informat de l'id ('". $values["member_id"]. "') y la contrasenya ('". $values["password"] ."').</I>";
               } else {
                 $list .= l("S'ha enviat un correu electrònic a")." '". $values["email"] ."' ".l("contenint el nou identificador i la clau").".";

                  } 
        }
    } else {
        $cErr->Error(l("Hi ha hagut un error guardant el número. Intenta-ho més tard."));
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
//    echo $date_str ."=".$today['year']."/".$today['mon']."/".$today['mday'];

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
//         http://pear.php.net/manual/en/package.mail.mail-rfc822.intro.php
function verify_valid_email ($element_name,$element_value) {
    if ($element_value=="")
        return true;        // Currently not planning to require this field
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
