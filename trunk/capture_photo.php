<?php 
include_once("includes/inc.global.php"); 


$cUser->MustBeLevel(1);
$p->site_section = ADMINISTRATION;
$p->page_title = l("Capturar foto");

$list='<div id="captura"></div>'; 
$list.='<br/><form><input type=hidden name="member_id" value="'.$_REQUEST["member_id"].'"><input type=button value="'.l("Configurar...").' onClick="webcam.configure()">&nbsp;&nbsp;&nbsp;<input type=button value="'.l("Sacar foto").'" onClick="webcam.snap()"></form>'; 
$list.='<div id="guardada"></div>';


$miembro = $_REQUEST["member_id"]; 
 
if(isset($_POST["Aceptar"])) {
 $imageurl =  $_POST["nombre_imagen"];
 $imagen = basename($imageurl);  
 createThumbnail("media/fotos", $imagen, "media/fotos/mini", 100);
 updateImage($miembro, $imagen);
 header("location:http://".HTTP_BASE."/member_edit.php?mode=admin&member_id=".$miembro);
 exit;

}
 

$p->DisplayPage($list);



function createThumbnail($imageDirectory, $imageName, $thumbDirectory, $thumbWidth)
{
$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
$origWidth = imagesx($srcImg);
$origHeight = imagesy($srcImg);

$ratio = $thumbWidth / $origWidth;
$thumbHeight = $origHeight * $ratio;

$thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);

imagejpeg($thumbImg, "$thumbDirectory/$imageName");
}

function updateImage($userid, $imagen)
{ 
 global $cDB, $cErr;
 
 $update = $cDB->Query("UPDATE ". DATABASE_PERSONS ." SET imagen='". $imagen ."' WHERE member_id='". $userid ."';"); 
 if(!$update)
            $cErr->Error(l("No se ha podido actualizar la imagen"));

}

?>   
    <!-- First, include the JPEGCam JavaScript Library -->
    <script type="text/javascript" src="webcam.js"></script>
    
    <!-- Configure a few settings -->
    <script language="JavaScript">
        webcam.set_api_url( 'test.php' );
        webcam.set_quality( 90 ); // JPEG quality (1 - 100)
        webcam.set_shutter_sound( true ); // play shutter click sound
    </script>
    
    <!-- Next, write the movie to the page at 320x240 -->

 
     <script language="JavaScript">
     
        document.getElementById('captura').innerHTML = webcam.get_html(320,240);

    </script>

    

<script language="JavaScript">
        webcam.set_hook( 'onComplete', 'my_completion_handler' );
        
        function my_completion_handler(msg) {
            // extract URL out of PHP output
            if (msg.match(/(http\:\/\/\S+)/)) {
                var image_url = RegExp.$1;
                // show JPEG image in page
                document.getElementById('guardada').innerHTML = 
                    '<h3>Imagen guardada</h3>' + 
                    '<img src="'+image_url+'">' +
                    '<form action="capture_photo.php" method="post"><input type=hidden name="member_id" value="<?php echo $_REQUEST["member_id"] ?>"><br><input type="submit" name="Aceptar" value="Aceptar"><INPUT type="hidden" value="'+image_url+'" name="nombre_imagen"/></form>';
            }
            else alert("PHP Error: " + msg);
        }
    </script>

   

                   



   