<?php 
include_once("includes/inc.global.php");
$cUser->MustBeLevel(1);
$p->site_section = EVENTS;
$p->page_title = l("Escoge la noticia a editar");

include("includes/inc.forms.php");
include_once("classes/class.news.php");

$news = new cNewsGroup;
$news->LoadNewsGroup();
if($news_array = $news->MakeNewsArray()) {
    $form->addElement("select", "news_id", l("¿Qué noticia?"), $news_array, array('class' => 'formulari2'));
    $form->addElement("static", null, null, null);
    $form->addElement('submit', 'btnSubmit', l('Editar'), array('class' => 'formulari2'));
} else {
    $form->addElement("static", null, l("No hay ninguna noticia publicada."), null);
}

if ($form->validate()) { // Form is validated so processes the data
   $form->freeze();
     $form->process("process_data", false);
} else {  // Display the form
    $p->DisplayPage($form->toHtml());
}

function process_data ($values) {
    global $cUser;
    header("location:http://".HTTP_BASE."/news_edit.php?news_id=".$values["news_id"]);
    exit;    
}

?>
