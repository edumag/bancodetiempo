<?php 
include_once("includes/inc.global.php");
$p->site_section = SITE_SECTION_OFFER_LIST;

include("includes/inc.forms.php");
require_once "Mail.php";
require_once "Mail/mime.php";

$form->addElement("header", null, l("Restablir la clau"));
$form->addElement("html", "<TR></TR>");

$form->addElement("text", "member_id", l("Introdueix el teu identificador d'associat"), array('class' => 'formulari2'));
$form->addElement("text", "email", l("Introdueix el correu-e del teu compte"), array('class' => 'formulari2'));

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", l("Restablir la clau"), array('class' => 'formulari2'));

$form->registerRule('verify_email','function','verify_email');
$form->addRule('email',l('L\'adreça de correu o l\'identificador de membre no és correcte'),'verify_email');
$form->addElement("static", null, null, null);
$form->addElement("static", 'contact', l("Si no recordes el teu identificador d'associat o el teu correu electrònic").", <A HREF=contact.php>".l("contacta")."</A> ".l("amb nosaltres").".", null);

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p;
	
	$member = new cMember;
	$member->LoadMember($values["member_id"]);

	$password = $member->GeneratePassword();
	$member->ChangePassword($password); // This will bomb out if the password change fails
	$member->UnlockAccount();
	
	$list = l("La teva contrasenya ha estat restablerta").". ".l("Pots canviar la teva nova contrasenya després que entris al sistema, anant a la secció Perfil").".<P>";
	
            $text = PASSWORD_RESET_MESSAGE . "\n\n".l("Nova clau").": ". $password;
            $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.nl2br($text).AVISO_LEGAL); 
            $to = $values['email'];
            $crlf = "\n";
            $headers = array ('From' => EMAIL_FROM,
            'To' => $to,
            'Subject' => PASSWORD_RESET_SUBJECT);
            $mime = new Mail_mime($crlf);
            $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
            $mime->setTXTBody($text);
            $mime->setHTMLBody($html);
            $body = $mime->get();
            $headers = $mime->headers($headers);
            
            $smtp = Mail::factory('mail');
            $mailed = $smtp->send($to, $headers, $body);

            if (PEAR::isError($mailed)) {
               $list  .= "<I>".l("No obstant això, no s'ha pogut enviar la nova contrasenya al teu email").". ".l("És possible que es degui a un problema tècnic. Contacta amb la persona encarregada del sistema")." ". PHONE_ADMIN ."</I>.";
               } else {
               $list .= l("S'ha enviat la nova contrasenya al teu correu electrònic.");
                  } 
    
	$p->DisplayPage($list);
}

function verify_email($element_name,$element_value) {
	global $form;
	$member = new cMember;

	if(!$member->VerifyMemberExists($form->getElementValue("member_id")))
		return false;  // Don't want to try to load member if member_id invalid, 
							// because of inappropriate error message.
		
	$member->LoadMember($form->getElementValue("member_id"));

	if($element_value == $member->person[0]->email)
		return true;
	else
		return false;
}

?>
