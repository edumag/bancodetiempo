<?php 
include_once("includes/inc.global.php");
include("classes/class.uploads.php");

$cUser->MustBeLevel(1);

$p->site_section = EVENTS;
$p->page_title = "Publicación subida";

$upload = new cUpload("N", $_REQUEST["Description"]);
if($upload->SaveUpload())
    $output = "Documento subido.";
else
    $output = "Ocurrió un error al subir el documento.";

$p->DisplayPage($output);
?>
