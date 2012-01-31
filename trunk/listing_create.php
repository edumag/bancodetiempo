<?php 
include_once("includes/inc.global.php");

$cUser->MustBeLoggedOn();
$p->site_section = LISTINGS;
if ($_REQUEST["type"] == "Offer") $p->page_title = l("Crear una oferta");
if ($_REQUEST["type"] == "Want") $p->page_title = l("Crear una demanda");

include("classes/class.listing.php");
include("includes/inc.forms.php");

if($cUser->member_id == "admin") {
	$p->DisplayPage(l("No pots crear ofertes o demandes si ets l'administrador/a").". <p>".l("Pots crear intercanvis per altres membres des del")." <a href=admin_menu.php>".l("Menú d'administració")."</a>.");	
	exit;
}


//
// First, we define the form
//
if($_REQUEST["mode"] == "admin") {  // Administrator is creating listing for another member
	$cUser->MustBeLevel(1);
	$form->addElement("hidden","mode","admin");
	if (isset($_REQUEST["member_id"])) {
		$form->addElement("hidden","member_id", $_REQUEST["member_id"]);
	} else {
		$ids = new cMemberGroup;
		$ids->LoadMemberGroup();
		$form->addElement("select", "member_id", l("Per a quin membre?"), $ids->MakeIDArray(), array('class' => 'formulari2'));
	}
} else {  // Member is creating offer for his/her self
	$cUser->MustBeLoggedOn();
    $cUser->LimitesPasados();
    /*   if($cUser->balance <= 0 and $_REQUEST["type"] == "Want"){
       $p->DisplayPage("Tus demandas están desactivadas.");
       exit;
       } */
	$form->addElement("hidden","member_id", $cUser->member_id);
	$form->addElement("hidden","mode","self");
}

$form->addElement('hidden','type',$_REQUEST['type']);
$title_list = new cTitleList($_REQUEST['type']);
$form->addElement('text', 'title', l('Títol'), array('size' => 30, 'maxlength' => 60, 'class' => 'formulari2'));
$form->addRule('title',l('Introdueix un títol'),'required');
$form->registerRule('verify_not_duplicate','function','verify_not_duplicate');
$form->addRule('title',l('Ja tens una oferta o demanda amb aquest títol'),'verify_not_duplicate');
$category_list = new cCategoryList();
$form->addElement('select', 'category', l('Categoria'), $category_list->MakeCategoryArray(), array('class' => 'formulari2'));
if(USE_RATES)
	$form->addElement('text', 'rate', l('Puntuació'), array('size' => 15, 'maxlength' => 30, 'class' => 'formulari2'));
else
	$form->addElement('hidden', 'rate');

$form->addElement('static', null, l('Descripció'), null);
$form->addElement('textarea', 'description', null, array('cols'=>45, 'rows'=>5, 'wrap'=>'soft', 'class' => 'formulari2'));
$form->addElement('html', '<TR><TD></TD><TD><BR></TD></TR>');
$form->addElement('advcheckbox', 'set_expire_date', l('Aquesta oferta o demanda ha d\'expirar automàticament?'));
$today = getdate();
$options = array('language'=> 'ca', 'format' => 'dFY', 'minYear' => $today['year'],'maxYear' => $today['year']+5, 'addEmptyOption'=>'Y', 'emptyOptionValue'=>'0');
$form->addElement('date','expire_date', l('Expira'), $options, array('class' => 'formulari2'));
$form->registerRule('verify_temporary','function','verify_temporary');
//$form->addRule('expire_date','Temporary listing box must be checked for expiration','verify_temporary');
$form->registerRule('verify_future_date','function','verify_future_date');
$form->addRule('expire_date',l('Ha de ser una data futura'),'verify_future_date');
$form->registerRule('verify_valid_date','function','verify_valid_date');
$form->addRule('expire_date',l('La data no és vàlida'),'verify_valid_date');
$form->registerRule('verify_category','function','verify_category');
$form->addRule('category', l('Tria una categoria'), 'verify_category');

$form->addElement('submit', 'btnSubmit', l('Enviar'), array('class' => 'formulari2'));

//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process('process_data', false);
} else {
   $p->DisplayPage($form->toHtml());  // just display the form
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
	global $p, $cUser,$cErr;
	
	$member = new cMember;
	
	if($_REQUEST["mode"] == "admin")
		$member->LoadMember($_REQUEST["member_id"]);
	else
		$member = $cUser;
		
	$list = "";
	$date = $values['expire_date'];

	if($date['F'] == '0' and $date['d'] == '0' and $date['Y'] == '0') {
		$parms['expire_date'] = null;
	} else {
		$expire_date = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
		$parms['expire_date'] = $expire_date;
	}
	$parms['title'] = $values['title'];
	$parms['description'] = $values['description'];
	$parms['category'] = $values['category'];	
	$parms['rate'] = $values['rate'];
	$parms['type'] = $_REQUEST['type'];

	$listing = new cListing($member, $parms);
	$created = $listing->SaveNewListing();

	if($created) {
		$list .= l("Element creat. Crear")." <A HREF=listing_create.php?type=".$_REQUEST["type"]."&mode=".$_REQUEST["mode"]."&member_id=".$member->member_id.">".l("un altre")."</A>?";	
	} else {
		$cErr->Error(l("Hi ha hagut un error gravant l'element. Intenta-ho més tard."));
	}
    $member->Limites(); 
   $p->DisplayPage($list);
}
//
// And the following functions verify form data
//

function verify_future_date ($element_name,$element_value) {
	global $form;

	$today = getdate();
	$date = $element_value;
	
	if($date['F'] == '0' and $date['d'] == '0' and $date['Y'] == '0')
		return true;
	
	$date_str = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];

	if (strtotime($date_str) <= strtotime("now")) // date is a past date
		return false;
	else
		return true;
}

function verify_valid_date ($element_name,$element_value) {
	$date = $element_value;
	
	if($date['F'] == '0' and $date['d'] == '0' and $date['Y'] == '0')
		return true;
	return checkdate($date['F'],$date['d'],$date['Y']);
}

function verify_not_duplicate ($element_name,$element_value) {
	global $title_list;
	
	$titles = $title_list->MakeTitleArray($_REQUEST["member_id"]);
	
	foreach ($titles as $title) {
		if($element_value == $title)
			return false;
	}
	return true;
}

function verify_category ($z, $category) {
	if($category == "0")
		return false;
	else
		return true;
}

?>
