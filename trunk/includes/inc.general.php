<?php
/**
 * @file inc.general.php Funcions generals
 */

if (!function_exists("escapa")) {

   /**
    * Protecció abans de entrar dades a mysql
    */

   function escapa($theValue, $theType = FALSE, $theDefinedValue = "", $theNotDefinedValue = "") {

      if (PHP_VERSION < 6) {
         $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
         }

      $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

      if ( $theType ) {
         switch ($theType) {
         case "text":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;    
         case "long":
         case "int":
            $theValue = ($theValue != "") ? intval($theValue) : "NULL";
            break;
         case "double":
            $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
            break;
         case "date":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
         case "defined":
            $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
            break;
            }
         }
      return $theValue;
      }
   }

/**
 * Convertir sql a array
 */

function sql2array($sql) {

   global $cDB,$cErr;

   //$resultado = mysql_query($sql) or die ("Error al conectar: ".mysql_errno());

   $resultado = $cDB->Query($sql);	

   while ($fila = mysql_fetch_array($resultado)) {
      $registros[$fila[0]] = $fila[0];
      }

   return $registros;
   }

/** l()
*
* Devolver literal o en su defecto retornamos la cadena enviada
*
* Si especificamos el tipo de literal que queremos, de proyecto o de gcm, y no es
* encontrado se añadirá al archivo correspondiente, aunque fuera con valor vacío,
* esto nos permitirá tener reflejado los literales que se están pidiendo y que no
* tenemos añadidos.
*
* @author Eduardo Magrané
* @version 1.0
*
* @param literal cadena asociada a un literal
*
* @return literal
*
*/

function l($literal) {

   global $LG, $DIR_BDT;

   global $idiomas; // Instancia de Idiomas
   
   $dir_idiomas    = $idiomas->getDir_idiomas();
   $idioma_actual  = $idiomas->getIdioma_actual();
   $idiomaxdefecto = $idiomas->getIdiomaxdefecto();

   // echo '<br>dir_idiomas: '.$dir_idiomas;
   // echo '<br>idioma_actual: '.$idioma_actual;
   // echo '<br>idiomaxdefecto: '.$idiomaxdefecto;

   $literal = html_entity_decode($literal,ENT_NOQUOTES,'UTF-8');

   if ( empty($literal) || $literal == ' ' || $literal == '' ) {
      die ('Error, literal sin especificar');
      return FALSE;
      }

   if ( isset($LG[$literal]) && $LG[$literal] != "" ) {

      return $LG[$literal] ;

   } else {

      // Si estamos en el ordenador de Jaume no guardamos literal
      if ( $_SERVER['DOCUMENT_ROOT'] == 'C:/EasyPHP5.3.0/www' ) return $literal;

      $valor = '';
      $file=$dir_idiomas."/LG_".$idioma_actual.".php";


      // Si el idioma actual no es por defecto se debe comprobar la existencia
      // sobre el idioma actual.

      if ( $idioma_actual == $idiomaxdefecto ) {

         if ( is_array($LG) && ! @array_key_exists  ( $literal , $LG  ) ) {

            // Si se pide un literal que no tenemos lo añadimos al archivo vacio
            // para tener constancia de ello.

            require_once(GCM_DIR."lib/int/GcmConfig/lib/GcmConfigFactory.php");

            $arr = GcmConfigFactory::GetGcmConfig($file);
            $arr->ordenar = FALSE;
            $arr->set($literal,$valor);
            $arr->guardar_variables();

            }

      } else {

         require_once(GCM_DIR."lib/int/GcmConfig/lib/GcmConfigFactory.php");

         $arr = GcmConfigFactory::GetGcmConfig($file);
         if ( $arr->get($literal) ) return $literal;
         $arr->ordenar = FALSE;
         $arr->set($literal,$valor);
         $arr->guardar_variables();
         }

         $LG[$literal] = '';

         return $literal;

      }

   }

/**
 * Incluir ficheros segun idioma de usuario
 */

function fl($fichero) {

   global $idiomas; // Instancia de Idiomas
   
   $dir_idiomas    = $idiomas->getDir_idiomas();
   $idioma_actual  = $idiomas->getIdioma_actual();
   $idiomaxdefecto = $idiomas->getIdiomaxdefecto();

   // echo '<br>dir_idiomas: '.$dir_idiomas;
   // echo '<br>idioma_actual: '.$idioma_actual;
   // echo '<br>idiomaxdefecto: '.$idiomaxdefecto;

   // Si tenemos archivo de idioma usuario incluimos
   if ( file_exists($dir_idiomas.'/'.$idioma_actual.'/'.$fichero) ) return $dir_idiomas.'/'.$idioma_actual.'/'.$fichero;

   // Si tenemos archivo de idioma por defecto
   if ( file_exists($dir_idiomas.'/'.$idiomaxdefecto.'/'.$fichero) ) return $dir_idiomas.'/'.$idiomaxdefecto.'/'.$fichero;

   echo "Error: fichero especificado [".$fichero."] no encontrado";
   // Error no existe archivo pedido
   return FALSE;

   }

/**
 * Devolver la salida de un include
 *
 * @param $filename Archivo a incluir
 * @param $datos Datos para la plantilla
 */

// function get_include_contents($filename,$datos=NULL) {
// 
//    if (is_file($filename)) {
// 
//       ob_start();
//       include $filename;
//       $contents = ob_get_contents();
//       ob_end_clean();
//       return $contents;
//       }
// 
//     return false;
// 
//    }

?>
