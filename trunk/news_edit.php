<?php 

include_once("includes/inc.global.php");
include("classes/class.news.php");
include("includes/inc.forms.php");

$cUser->MustBeLevel(1);

$p->site_section = EVENTS;

$news = new cNews;
$news->LoadNews($_REQUEST["news_id"]);
$p->page_title = l("Editar")." '". $news->title ."'";



//
// First, we define the form
//

$form->addElement("hidden","news_id",$_REQUEST["news_id"]);
$form->addElement("text", "title", l("Títul"), array("size" => 35, "maxlength" => 100, 'class' => 'formulari2'));
$today = getdate();
$options = array("language"=> "ca", "format" => "dFY", "minYear" => $today["year"],"maxYear" => $today["year"]+5);
$form->addElement("date","expire_date", l("Caduca el"), $options, array('class' => 'formulari2'));
$sequence = new cNewsGroup();
$sequence->LoadNewsGroup();
$form->addElement("select", "sequence",l("Posició"), $sequence->MakeNewsSeqArray($news->sequence), array('class' => 'formulari2'));
//$form->addElement("static", null, "Description", null);
$form->addElement("textarea", "description", l("Descripció"), array("cols"=>65, "rows"=>5, "wrap"=>"soft", 'class' => 'formulari2'));

$form->addElement("submit", "btnSubmit", l("Publicar"), array('class' => 'formulari2'));

//
// Set up validation rules for the form
//
$form->addRule("title",l("Introduce un título"),"required");
$form->addRule("description",l("Introduce una descripción"),"required");
$form->registerRule("verify_valid_date","function","verify_valid_date");
$form->addRule("expire_date",l("La fecha es incorrecta"),"verify_valid_date");

//
// Then check if we are processing a submission or just displaying the form
//
if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
     $form->process("process_data", false);
} else {
    $current_values = array ("title"=>$news->title, "description"=>$news->description, "expire_date"=>$news->expire_date->DateArray(), "sequence"=>$news->sequence);
    
    $form->setDefaults($current_values);
   $p->DisplayPage($form->toHtml());  // just display the form
}

//
// The form has been submitted with valid data, so process it   
//
function process_data ($values) {
    global $p, $news, $cErr;
    
    $date = $values['expire_date'];
    $expire_date = $date['Y'] . '/' . $date['F'] . '/' . $date['d'];
    $news->title = $values["title"];
    $news->description = $values["description"];
    $news->expire_date->Set($expire_date);
    $news->sequence = $values["sequence"];
    $success = $news->SaveNews();    
    
    if ($success)
        $output = l("Se han guardado los cambios.");
    else
        $output = l("Ha ocurrido un error al grabar la noticia");
        
    $p->DisplayPage($output);
    
}


//
// Custom validation functions
//

function verify_valid_date ($element_name,$element_value) {
    $date = $element_value;
    return checkdate($date["F"],$date["d"],$date["Y"]);
}
