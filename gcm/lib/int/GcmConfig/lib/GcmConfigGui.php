<?php

/**
 * @file      GcmConfigGui.php
 *
 * Interface para modificar variables de php de un archivo.
 *
 * @author    Eduardo Magrané 
 *
 * @internal
 *   Created  21/01/10
 *  Revision  SVN $Id: GcmConfigGui.php 443 2011-01-02 13:15:38Z eduardo $
 * Copyright  Copyright (c) 2010, Eduardo Magrané
 *
 * This source code is released for free distribution under the terms of the
 * GNU General Public License as published by the Free Software Foundation.
 */

require_once('GcmConfig.php');

/**
 * @class GcmConfig
 *
 * @brief     Lectura y edición de archivos de configuración
 *
 * Esta clase nos permite leer archivos de configuración nativos de php y
 * a la vez la edición de su contenido con formularios php.
 *
 * Inspirado en @see http://www.jourmoly.com.ar/introduccion-a-mvc-con-php-segunda-parte/
 * 
 *
 * @version 0.1
 */

class GcmConfigGui extends GcmConfig {

   function __construct($archivo) {

      parent::__construct($archivo);

      }


   /** 
    * Presentamos formulario con las variables a modificar
    *
    * Le un array definido en un archivo y
    * lo colocamos en un formulario para ser modificado
    *
    * @param $args Opciones definidas en un array
    *
    *        idioma:                   Idioma actual para presentar las descripciones
    *        ampliar:                  Posibilidad de añadir elementos nuevos si/no, por defecto no.
    *        eliminar:                 Posibilidad de eliminar elementos si/no, por defecto no.
    *        modificar_descripciones   Permitir modificar descripciones de variables
    *        plantilla                 Plantilla a utilizar por defecto la que viene con el módulo
    *        css                       Archivo con los css a utilizar, por defecto los del módulo.
    */

   function formulario($args = NULL) {

      $ampliar   = ( isset($args['ampliar']) ) ? $args['ampliar'] : FALSE;
      $eliminar  = ( isset($args['eliminar']) ) ? $args['eliminar'] : FALSE;
      $plantilla = ( isset($args['plantilla']) ) ? $args['plantilla'] : dirname(__FILE__).'/../html/formGcmConfigGui.html';
      $css       = ( isset($args['css']) ) ? $args['css'] : FALSE;
      $modificar_descripciones = ( isset($args['modificar_descripciones']) ) ? $args['modificar_descripciones'] : FALSE;


      if ( $css ) {

         echo '<link rel="stylesheet" href="'.$css.'" type="text/css">';

      } else {

         echo '<style type="text/css">';
         include (dirname(__FILE__).'/../css/formGcmConfigGui.css');
         echo '</style>'; 

         }

      if ( !include($plantilla)) {
         throw new Exception("Error al incluir plantilla [".$plantilla.']');
         }


      }

   /**
    * Escribimos en el archivo que contiene el array
    * la nueva información que recibimos del formulario
    *
    * @author Eduardo Magrané
    * @version 1.0
    * @param archivo archivo que contiene el array
    *
    * @return FALSE/TRUE
    */

   function escribir_desde_post() {

      global $HTTP_POST_VARS;

      $idioma  = $_POST['idioma'];
      $archivo = $_POST['archivo'];

      /* Comprobar la existencia de contenido en las variables */

      if ( count($_POST['escribir_'.$idioma]) <= 0 ) {
         trigger_error('Sin valores validos en GET');
         return FALSE;
         }

      /* Variables a cero */

      $this->variables = array();

      foreach( $_POST['escribir_'.$idioma] as $clave => $valor ) {

         $this->set($clave,$valor);

         }

      /* Descripciones */

      if ( count($_POST['descripcion_'.$idioma]) > 0 ) {

         $this->descripciones[$idioma] = array();

         $this->descripciones_recogidas = TRUE;

         foreach( $_POST['descripcion_'.$idioma] as $clave => $valor ) {

            $this->setDescripcion($clave,$valor, $idioma);

            }

         }
      
      return TRUE;

      }

   }

?>
