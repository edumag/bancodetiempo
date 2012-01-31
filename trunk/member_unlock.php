<?php 
include_once("includes/inc.global.php");
require_once "Mail.php";
require_once "Mail/mime.php"; 

$cUser->MustBeLevel(1);
$p->site_section = ADMINISTRATION;
$p->page_title = l("Desbloquejar una conta y restablir la clau");

include("includes/inc.forms.php");

$form->addElement("static", 'contact', l("Amb aquest formulari pots desbloquejar una conta (si està bloquejada) i restablir la clau d'un usari. La nova clau s'enviarà per correu-e a l'usuari").". ".l("Asegúrat que la direcció de correu de l'usuari segueix vàlida").".", null);
$form->addElement("static", null, null);
$ids = new cMemberGroup;
$ids->LoadMemberGroup();
$form->addElement("select", "member_id", l("Selecciona la conta de l'usuari"), $ids->MakeIDArray(), array('class' => 'formulari2'));

$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", l("Desbloquejar i restablir la clau"), array('class' => 'formulari2'));


if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
     $form->process("process_data", false);
} else {  // Display the form
    $p->DisplayPage($form->toHtml());
}

function process_data ($values) {
    global $p;
    
    $list = "";
    $member = new cMember;
    $member->LoadMember($values["member_id"]);
    
    if($consecutive_failures = $member->UnlockAccount()) {
        $list .= l("La cuenta de usuario había sido bloqueada después de")." ". $consecutive_failures ." ".l("intentos de acceso fallidos").". ".l("La cuenta ha sido desbloqueada").". ".l("Si el número de intentos es más de 10 o 20, puedes contactar con el administrador en")." ". PHONE_ADMIN ."</I>, ".l("porque alguien puede estar intentando entrar con tu cuenta").".<P>";
    }


    $password = $member->GeneratePassword();
    $member->ChangePassword($password); // This will bomb out if the password change fails
    
    
    
    $list .= l("La clau s'ha restablert").".<P>";
    
    if($member->person[0]->email !="")
    {
    $text = PASSWORD_RESET_MESSAGE . "\n\n".l("Identificador de conta").": ". $member->member_id ."\n".l("Nova clau").": ". $password;
    $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.nl2br($text).AVISO_LEGAL); 
    $to = $member->person[0]->email;
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
               $list .= ". <I>".l("Sin embargo, ha fallado el envío del correo con la nueva contraseña").". ".l("Esto probablemente se deba a un problema técnico").".  ".l("Contacta con el adminstrador en")." ". PHONE_ADMIN ."</I>.";
               } else {
               $list .= " ".l("i enviat a l'adreça de correu de l'usuari")." (". $member->person[0]->email .").";
                  } 
    }
    else{
    $list .= l("Sin embargo, el usuario no ha suministrado ninguna dirección de correo").". <b>".l("Tendrá que ser informado por otro medio")."</b> ".l("de que sus datos de acceso son los siguientes").":<p>";
    $list .= l("Identificador de conta").": <b>" . $member->member_id ."</b><br>".l("Nova clau").": <b>". $password ."</b>"; 
    }
    $p->DisplayPage($list);
}

?>

     
