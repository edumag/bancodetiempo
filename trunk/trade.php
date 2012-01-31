<?php 

include_once("includes/inc.global.php");

$p->site_section = EXCHANGES;
$p->page_title = l("Desar un intercanvi");

include("classes/class.trade.php");
include("includes/inc.forms.validation.php");

//
// Define form elements
//
$member = new cMember;

if($cUser->member_id == "admin") {
	$p->DisplayPage(l("No puedes guardar intercambios mientras estés como ADMIN").". ".l("Esta es una cuenta para temas administrativos solamente.")."<p>");	
	exit;
}

if($_REQUEST["mode"] == "admin") {
	$cUser->MustBeLevel(2);
	$member->LoadMember($_REQUEST["member_id"]);
	$p->page_title .= " per a ". $member->PrimaryName();
} else {
	$cUser->MustBeLoggedOn();
	$member = $cUser;
    if ($member->balance <= 0)
    {    $p->DisplayPage(l("Ho sentim, no pots fer intercanvis mentre no tinguis saldo").". <p>");
    exit;
    }
}	
	
$form->addElement('hidden', 'member_id', $member->member_id);
$form->addElement('hidden', 'mode', $_REQUEST["mode"]);
$form->addElement("html", "<TR></TR>");  // TODO: Move this to the header
$name_list = new cMemberGroup;
$name_list->LoadMemberGroup();
$form->addElement("select", "member_to", l("Transferir a l'associat/da"), $name_list->MakeNameArray(), array('class' => 'formulari2'));
$category_list = new cCategoryList();
$form->addElement('select', 'category', l('Categoria'), $category_list->MakeCategoryArray(), array('class' => 'formulari2'));
$form->addElement("text", "units", "# ". UNITS ." ".l("a transferir")."", array('size' => 5, 'maxlength' => 10, 'class' => 'formulari2'));
if(UNITS == l("Hores")) {
	$form->addElement("text","minutes","".l("# minuts a transferir")."",array('size'=>2,'maxlength'=>2, 'class' => 'formulari2'));
}
$form->addElement('static', null, l('Escriu una breu descripció del servei que t\'han prestat:'), null);
$form->addElement('textarea', 'description', null, array('cols'=>50, 'rows'=>4, 'wrap'=>'soft', 'class' => 'formulari2'));
$form->addElement('submit', 'btnSubmit', l('Enviar'), array('class' => 'formulari2'));

//
// Define form rules
//
$form->addRule('description', l('Introdueix el concepte pel que vas a fer la transferència'), 'required');
$form->registerRule('verify_not_self','function','verify_not_self');
$form->addRule('member_to', l('No pots transferir-te hores a tu mateix'), 'verify_not_self');
$form->registerRule('verify_selection','function','verify_selection');
$form->addRule('category', l('Tria una categoria'), 'verify_selection');
$form->addRule('member_to', l('Tria a una persona'), 'verify_selection');
$form->addRule('description', l('Descripció massa llarga, màxim 255 caràcters'), 'verify_max255');
$form->registerRule('verifica_numero','function','verifica_numero');
$form->addRule('units', l('No és un nombre vàlid'), 'verifica_numero'); 
$form->addRule('minutes', l('No és un nombre vàlid'), 'verifica_numero'); 
$form->registerRule('verifica_saldo','function','verifica_saldo');
$form->addRule('units', l('El teu saldo és inferior a la quantitat que estàs transferint'), 'verifica_saldo'); 
$form->registerRule('verifica_tope','function','verifica_tope');
$form->addRule('member_to', l('No pots pagar a aquest usuari ja que el seu saldo és superior a 12 hores').'. '.l('Podràs pagar-li quan hi hagi demandat algun servei i el seu saldo sigui inferior a 12 hores').'. '.l('Anima\'l a que demani algun servei teu o d\'algun altre membre.'), 'verifica_tope'); 

if(UNITS == "Horas") {
	$form->registerRule('verify_whole_hours','function','verify_whole_hours');
	$form->addRule('units', l('Les hores han de ser un nombre enter i positiu'), 'verify_whole_hours');
	$form->registerRule('verify_even_minutes','function','verify_even_minutes');
	$form->addRule('minutes', l('Introdueix 15, 30, o 45 (o altres números en increments de 3 minuts)'), 'verify_even_minutes');
} else {
	$form->registerRule('verify_valid_units','function','verify_valid_units');
	$form->addRule('units', l('Introdueix un nombre positiu amb no més de dos punts decimals'), 'verify_valid_units');
}


//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process('process_data', false);
} else {
   $p->DisplayPage($form->toHtml());  // just display the form
}

function process_data ($values) {
	global $p, $member, $cErr;
	$list = "";
	
	if(UNITS == l("Hores")) {
		if($values['minutes'] > 0)
			$values['units'] = $values['units'] + ($values['minutes'] / 60);
	}
	
	if(!($values['units'] > 0)) {
		$cErr->Error(l("No s'han introduït unitats!"));
		include("redirect.php");
	}
	
	$member_to_id = substr($values['member_to'],0, strpos($values['member_to'],"?")); // TODO:
	$member_to = new cMember;
	$member_to->LoadMember($member_to_id);
	
	if ($_REQUEST["mode"] == "admin")
		$type = TRADE_BY_ADMIN; // record that trade was entered by an admin & log if logging enabled
	else
		$type = TRADE_ENTRY;  // regular trade
       
	$trade = new cTrade($member, $member_to, $values['units'], $values['category'], $values['description'], $type);
	$status = $trade->MakeTrade();
	if(!$status)
		$list .= l("Intercanvi fallit, prova més tard.");
	else
		$list .= l("Has transferit")." ". $values['units'] ." ". strtolower(UNITS) ." ".l("a")." ". $member_to_id .". ".l("Vols")." <A HREF=trade.php?mode=".$_REQUEST["mode"]."&member_id=". $_REQUEST["member_id"].">".l("fer un altre")."</A> ".l("intercanvi?")."<P>".l("O vols introduir una")." <A HREF=feedback.php?mode=". $_REQUEST["mode"] ."&author=". $member->member_id ."&about=". $member_to_id ."&trade_id=". $trade->trade_id .">".l("valoració a aquest associat")." </A> ".l("sobre el servei prestat?")."";
		
   $p->DisplayPage($list);
}

function verify_not_self($element_name,$element_value) {
	global $member;
	$member_id = substr($element_value,0, strpos($element_value,"?"));
	if ($member_id == $member->member_id)
		return false;
	else
		return true;
}

function verify_valid_units($element_name,$element_value) { 
	if ($element_value < 0)
		return false; 
	elseif ($element_value * 100 != floor($element_value * 100)) 
		return false;	// allow no more than two decimal points
	else
		return true;
}

function verify_even_minutes ($z, $minutes) { // verifies # of minutes entered represents an evenly
	if($minutes/60*1000 == floor($minutes/60*1000)) 	// divisible fraction w/ no more than 3
		return true;												//	decimal points
	else
		return false;
}

function verify_whole_hours ($z, $hours) {
	if(abs(floor($hours)) != $hours)
		return false;
	else
		return true;
}

function verifica_saldo($element_name,$element_value) {
    global $member;
    
    if ($_REQUEST["mode"] != "admin")
    {if ($element_value > $member->balance)
        return false;
    else
        return true;
    }
    else
        return true;
}

function verifica_tope($element_name,$element_value) {
    global $member;

    if ($_REQUEST["mode"] != "admin")
    {
    $member_to_id = substr($element_value,0, strpos($element_value,"?"));  
    $member_to = new cMember;
    $member_to->LoadMember($member_to_id);
    if($member_to->balance >= 12)
        return false;
    else
        return true;
    }
    else
        return true;
}

function verifica_numero($element_name,$element_value) {
    global $member;
    
    if ($_REQUEST["mode"] != "admin")
    {if (is_numeric($element_value))
        if($element_value >=0)
        return true;
    else
        return false;
    }
    else
        return true;
}

?>
