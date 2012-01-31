<?php 
include_once("includes/inc.global.php");
$p->site_section = LISTINGS;
if ($_REQUEST["type"] == "Offer") $p->page_title = l("Llista d'Ofertes");
if ($_REQUEST["type"] == "Want") $p->page_title = l("Llista de Demandes");

include("classes/class.listing.php");
include("includes/inc.forms.php");

$form->addElement("hidden","type", $_REQUEST["type"]);
$form->addElement("static", null, l("Selecciona la categoria i la franja de temps que vols consultar i després prem Continuar; per veure les llistes, simplement prem Continuar").". <br><!-- Si vols imprimir o baixar al teu ordinador el directori complet, prem <A HREF=directory.php><span class=\"at\">aquí</span></A>.<br>-->".l("Si vols afegir alguna nova oferta o demanda, fes clic")." <A HREF=listings_menu.php><span class=\"at\">".l("aquí")."</span></A>", null);
$form->addElement("static", null, null, null);
$category_list = new cCategoryList();
$categories = $category_list->MakeCategoryArray(ACTIVE, substr($_REQUEST["type"],0,1));
$categories[0] = l("(Totes les categories)");
$form->addElement("select", "category", l("Segons la categoria"), $categories, array('class' => 'formulari2'));
$text = l("Proposat fa menys de ");
$form->addElement("select", "timeframe", l("Segons el temps"), array("0"=>l("(Tots els llistats)"), "3"=>$text ."".l("3 díes")."", "7"=>$text ."".l("una setmana")."", "14"=>$text ."".l("2 setmanas")."", "30"=>$text ."".l("un mes")."", "90"=>$text ."".l("3 mesos").""), array('class' => 'formulari2'));
$form->addElement("static", null, null, null);
$form->addElement("submit", "btnSubmit", l("Continuar"), array('class' => 'formulari2'));

//$form->registerRule('verify_selection','function','verify_selection');
//$form->addRule('category', 'Choose a category', 'verify_selection');

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
 	$form->process("process_data", false);
} else {  // Display the form
	$p->DisplayPage($form->toHtml());
}

function process_data ($values) {
	global $p;

	header("location:http://".HTTP_BASE."/listings_found.php?type=".$_REQUEST["type"]."&category=".$values["category"]."&timeframe=".$_REQUEST["timeframe"]);
	exit;
}

function verify_selection ($z, $selection) {
	if($selection == "0")
		return false;
	else
		return true;
}


?>
