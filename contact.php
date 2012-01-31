<?php 
include_once("includes/inc.global.php");
$p->site_section = SECTION_EMAIL;
$p->page_title = l("Contactar");

include("includes/inc.forms.php");
require_once "Mail.php";  
require_once "Mail/mime.php"; 

//
// First, we define the form
//
$form->addElement("static", null, l("Per participar del Banc de Temps o demanar qualsevol informació, omple el següent qüestionari, i contestarem en breu.")." ".l("Els camps amb asterisc son obligatoris"), null);
$form->addElement("static", null, null, null);
$form->addElement("text", "name", l("Nom "), array('class' => 'formulari2'));
$form->addElement("text", "email", l("Correu-e "), array('class' => 'formulari2'));
$form->addElement("text", "phone", l("Telèfon "), array('class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement("textarea", "message", l("Missatge: "), array("cols"=>50, "rows"=>10, "wrap"=>"soft", 'class' => 'formulari2'));
$form->addElement("static", null, null, null);
$heard_from = array ("0"=>l("(Seleccionar un)"), "1"=>l("Buscant a internet"), "2"=>l("Per un amic/a"), "3"=>l("Per")." ".SERVER_DOMAIN."", "4"=>l("Altres"));
$form->addElement("select", "how_heard", "Com ens has conegut?", $heard_from, array('class' => 'formulari2'));

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", "Enviar", array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule("name", l("Introdueix el teu nom"), "required");
$form->addRule("email", l("Introdueix el teu email"), "required");
//$form->addRule("phone", l("Enter your phone number"), "required");

$form->registerRule('verify_valid_email','function', 'verify_valid_email');
$form->addRule('email', l('Correu-e no vàlid'), 'verify_valid_email');
$form->registerRule('verify_phone_format','function','verify_phone_format');
$form->addRule('phone', l('El número de telèfon no es vàlid'), 'verify_phone_format');




if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
     $form->process("process_data", false);
} else {  // Display the form
    $p->DisplayPage($form->toHtml());
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
    global $p, $heard_from;
    
    $text = l("De: "). $values["name"]. "\n". l("Telèfon: "). $values["phone"] ."\n". l("Ens ha conegut: "). $heard_from[$values["how_heard"]] ."\n\n".l("Diu: ")."\n". wordwrap($values["message"], 64);
    $crlf = "\n"; 
    $to = EMAIL_ADMIN;
    $headers = array ('From' => $values["email"],
    'To' => $to,
    'Subject' => SITE_SHORT_TITLE . " ".l("Formulari de contacte"));
    
    $mime = new Mail_mime($crlf);
    $mime->setTXTBody($text);
    $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
    $body = $mime->get();
    $headers = $mime->headers($headers);
    
    $smtp = Mail::factory('mail');
    $mailed = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mailed)) {
        $output = l("Hi ha hagut un problema enviant el teu email").". ".l("Prem 'Enrere' en el teu navegador i comprova que has escrit l'adreça de correu electrònic correctament.");
    } else {
        $output = l("Gràcies, el teu correu s'ha enviat correctament."); 
    } 
    $p->DisplayPage($output); 
    
    
}
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
    
    if(substr($phone->area,0,1)== "9" or substr($phone->area,0,1)=="6") 
        return true;
    else
        return false;
}

?>
