<?php 
include_once("includes/inc.global.php");
$p->site_section = SECTION_EMAIL;
$p->page_title = l("Correu-e a tot el col·lectiu");

$cUser->MustBeLevel(2);

include("includes/inc.forms.php");
require_once "Mail.php"; 
require_once "Mail/mime.php";  
//
// First, we define the form
//
$form->addElement("static", null, l("Aquest correu-e s'enviarà a")." <i>".l("TOTES")."</i> ".l("las persones usuàries del")." ".SITE_LONG_TITLE.". ".l("AQUEST PROCES POT TARDAR ALGUNS MINUTS, NO HAS DE CANCELAR EL PROCÈS FINS QUE S'ACABI."), null);
$form->addElement("static", null, null, null);
$form->addElement("text", "subject", l("Encapçelament"), array("size" => 30, "maxlength" => 50, 'class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement("textarea", "message", l("El teu missage"), array("cols"=>65, "rows"=>10, "wrap"=>"soft", 'class' => 'formulari2'));
$form->addElement("static", null, null, null);

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", l("Enviar"), array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule("subject", "Introdueix un encapçalament", "required");
$form->addRule("message", "Introdueix el teu missatge", "required");

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
	
	$output = "";
	$errors = "";
	$all_members = new cMemberGroup;
	$all_members->LoadMemberGroup();
    //set_time_limit(10);
    ignore_user_abort();
	foreach($all_members->members as $member) {
		if($errors != "")
			$errors .= ", ";
		
		if($member->person[0]->email != "")
        {
	    $text = wordwrap($values["message"], 64);
        $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.nl2br($text).AVISO_LEGAL);
        $crlf = "\n";
        $to = $member->person[0]->email;
        $headers = array ('From' => EMAIL_ADMIN,
        'To' => $to,
        'Subject' => $values["subject"]);
        $mime = new Mail_mime($crlf);
        $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
        $mime->setTXTBody($text);
        $mime->setHTMLBody($html);  
        $body = $mime->get();
        $headers = $mime->headers($headers);
    
        $smtp = Mail::factory('mail');
        $mailed = $smtp->send($to, $headers, $body);
        }
		else
		$mailed = true;
		
		if (PEAR::isError($mailed))
			$errors .= $member->person[0]->email;
    }    
	    if($errors == "")
		    $output .= l("El mensaje ha sido enviado a todos los usuarios.");
	    else
		    $output .= l("Ha habido un error enviando el mensaje a estas direcciones:")."<BR>". $errors;
		
	    $p->DisplayPage($output);
}
   

?>
