<?php 

include_once("includes/inc.global.php");

$cUser->MustBeLoggedOn();
$p->site_section = LISTINGS;
if ($_REQUEST['type'] == "Offer") $p->page_title = l('Esborrar oferta');
if ($_REQUEST['type'] == "Want") $p->page_title = l('Esborrar demanda');

include("classes/class.listing.php");
include("includes/inc.forms.php");

$message = "";

// First, need to change the default form template so checkbox comes before the label
$renderer->setElementTemplate('<TR><TD>{element}<!-- BEGIN required --><font> *</font><!-- END required --></FONT><!-- BEGIN error --><font color=RED size=2>   *{error}*</font><br /><!-- END error -->&nbsp;<FONT SIZE=2>{label}</FONT></TD></TR>');  

$form->addElement('hidden','type',$_REQUEST['type']);
$form->addElement('hidden','mode',$_REQUEST['mode']);


$member = new cMember;

if($_REQUEST["mode"] == "admin")
	$member->LoadMember($_REQUEST["member_id"]);
else
    {
	$member = $cUser;
    $cUser->LimitesPasados();
       /* if($cUser->balance <= 0 and $_REQUEST["type"] == "Want"){
         $p->DisplayPage("Tus demandas están desactivadas.");    
          exit; */
         
    }
$form->addElement('hidden','member_id',$member->member_id);

$title_list = new cTitleList($_REQUEST['type']);
$titles = $title_list->MakeTitleArray($member->member_id);

$listings_exist = false;

while (list($key, $title) = each ($titles)) {
	if($title != "") {
		$form->addElement('checkbox', $key, $title);
		$listings_exist=true;
	}
}

if ($listings_exist) {
	$form->addElement('static', null, null);
	$form->addElement('submit', 'btnSubmit', l('Esborrar'), array('class' => 'formulari2'));
} else {
	if($_REQUEST["mode"] == "self")
		$text = l("No tens ");
	else
		$text = $member->PrimaryName() . " ".l("no té ");
	
		if ($_REQUEST['type'] == "Offer") $message = $text ."".l("actualment cap oferta");
		if ($_REQUEST['type'] == "Want") $message = $text ."".l("actualment cap demanda");
	
	
}

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process('process_data', false);
} else {
   $p->DisplayPage($form->toHtml() ."<BR>". $message);  // just display the form
}

function process_data ($values) {
	global $p, $cErr, $titles, $member;
	$list = "";
	$deleted = 0;
	$listing = new cListing;
	while (list ($key, $value) = each ($values)) {
		$affected = 0;
		if(is_numeric($key))  // Two of the values are hidden fields.  Need to skip those.
			$affected = $listing->DeleteListing($titles[$key],$member->member_id,substr($_REQUEST['type'],0,1));

		$deleted += $affected;
	}
	
	if($deleted == 1) 
		$list .= l("1 element esborrat.");
	elseif($deleted > 1)
		$list .= $deleted . " ".l("elements esborrats").".";	
	else
		$cErr->Error(l("Hi ha hagut un error esborrant l'element. Has comprovat tots els elements?"));
	$member->Limites(); 	
   $p->DisplayPage($list);
}

?>
