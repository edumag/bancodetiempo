<?php

/**
 * @file Configuración
 */

/** 
 * Directorio raiz 
 *
 * Puede venir definido de una pagina de un subdirectorio
 */

$DIR_BASE = ( isset($DIR_BASE) ) ? $DIR_BASE : './';

/** Carpeta de bdt respecto a la raiz del dominio */

$DIR_BDT = $DIR_BASE.'./';

/** Cual es dominio de la web */

//define ("SERVER_DOMAIN","localhost");
define ("SERVER_DOMAIN","totbisbal.com");    // no http:// servidor


/** Cual es la ruta de bdt */

define ("SERVER_PATH_URL","/bdtdev");

/** Cual es la ruta donde se encuentran las librerias pear */

define ("PEAR_PATH", $DIR_BASE."pear"); // no ending slash

/** Directorio de las librerías de GCM */

define('GCM_DIR',$DIR_BASE.'gcm/');
//define('GCM_DIR',$DIR_BASE.'../subversion/gcm/trunk/gcm/'); // DEV

// Control de seguridad entre otros, para totbisbal comentar o 
// modificar para otros proyectos

// include_once($DIR_BASE."generals/cap.php");

// Funciones generales

include_once(dirname(__FILE__).'/inc.general.php');

require(GCM_DIR.'lib/int/idiomas/lib/IdiomasCore.php');
include($DIR_BDT.'config/config.php');

$configuracion_idiomas = array( 'dir_idiomas' => $config['Directorio idiomas']
                              , 'idiomaxdefecto' => $config['Idioma por defecto']
                              , 'idiomas_activados' => $config['Idiomas activados']
                              , 'proyecto' => $config['Proyecto']
                              );

$idiomas = new IdiomasCore($configuracion_idiomas);
$idiomas->seleccion_idioma();

$temaxdefecto = $config['Tema'];

require_once(GCM_DIR.'lib/int/temas/lib/TemaGcm.php');

$estamos = getcwd();
chdir ($DIR_BASE.'temes/'.$temaxdefecto);
$archivos_tema['html'] = glob('*/html/*');
foreach ( glob('*/html/*') as $f ) {
   $archivos_tema['html'][$f] = $f;
   }
chdir ($estamos);

$tema = new TemaGcm($archivos_tema, FALSE);

if (!isset($global))
{
    die(__FILE__." was included directly.  This file should only be included via inc.global.php. Include() that one instead.");
}

// Ok, then lets define some paths (no need to edit these)
define ("HTTP_BASE",SERVER_DOMAIN.SERVER_PATH_URL);
define ("CLASSES_PATH",$DIR_BDT."/classes/");
define ("IMAGES_PATH",SERVER_DOMAIN.SERVER_PATH_URL."/images/");
//define ("UPLOADS_PATH",$_SERVER["DOCUMENT_ROOT"].SERVER_PATH_URL."/uploads/");
/**********************************************************/
/***************** DATABASE LOGIN  ************************/


define ("DATABASE_USERNAME","mytotbisbal");
define ("DATABASE_PASSWORD","jBABubajabsAJs12");
define ("DATABASE_NAME","totproves");
define ("DATABASE_SERVER","localhost"); // often "localhost"

/**********************************************************/
/********************* SITE NAMES *************************/

// What is the name of the site?

define ("SITE_LONG_TITLE", l("Banc de temps de..."));
define ("SITE_LONG_TITLEMI", l("banc de temps de...")); // Principi en minúscules per centre de frase.

// What is the short, friendly, name of the site?
define ("SITE_SHORT_TITLE", "bdtdev");

/**********************************************************/
/***************** FOR MAINTENANCE ************************/

// If you need to take the website down for maintenance (such
// as during an upgrade), set the following value to true
// and customize the message, if you like

define ("DOWN_FOR_MAINTENANCE", false);// true
define ("MAINTENANCE_MESSAGE", l("Estem fent tasques de manteniment al ").SITE_SHORT_TITLE .".<br />".l("Si us plau, torna més tard").".");

/**************************************************************/
/******************** SITE CUSTOMIZATION **********************/

// email addresses & phone number to be listed in the site
define ("EMAIL_FEATURE_REQUEST",SITE_SHORT_TITLE."<bdt@totbisbal.com>");
define ("EMAIL_ADMIN",SITE_SHORT_TITLE. "<bdt@totbisbal.com>");
define ("PHONE_ADMIN",SITE_SHORT_TITLE. " - tel. 972 646 836"); // an email address may be substituted...
define ("EMAIL_FROM",SITE_SHORT_TITLE. "<bdt@totbisbal.com>"); // to override EMAIL_ADMIN 
                                                                                // for replies

// What should appear at the front of all pages?
// Titles will look like "PAGE_TITLE_HEADER - PAGE_TITLE", or something 
// like "Local Exchange - Member Directory";
define ("PAGE_TITLE_HEADER", SITE_LONG_TITLE);

// What keywords should be included in all pages?
define ("SITE_KEYWORDS", l("intercamvi, Banc de temps, trueque, ").SITE_LONG_TITLE);

// Logo Graphic for Header
define ("HEADER_LOGO", "localx_logo.png");

// Title Graphic for Header
define ("HEADER_TITLE", "localx_title.png");

// Logo for Home Page
define ("HOME_LOGO", "localx_black.png");

// Picture appearing left of logo on Home Page
define ("HOME_PIC", "localx_home.png");

//define ("PAGE_FOOTER_CONTENT", "<div id=\"footer\" align=\"center\">".l("Creat sota")." <a target=\"_blank\" href=\"http://www.gnu.org/copyleft/gpl.html\">GPL</a> • <a href=\"http://". HTTP_BASE ."/info/credits.php\">".l("Credits")."</a></div></div><br>");

/**********************************************************/
/**************** DEFINE SIDEBAR MENU *********************/

$SIDEBAR = array (
    array(l("Principal"),"index.php"),
    array(l("Ofertes"),"listings.php?type=Offer"),
    array(l("Demandes"),"listings.php?type=Want"),
    array(l("Contacta'ns"),"contact.php"),
    array(l("Fes-te'n soci/a"),"member_self.php"));

// El resto de opciones está en classes/class.page.php
    
/**********************************************************/
/**************** DEFINE SITE SECTIONS ********************/

define ("EXCHANGES",0);
define ("LISTINGS",1);
define ("EVENTS",2);
define ("ADMINISTRATION",3);
define ("PROFILE",4);
define ("SECTION_FEEDBACK",5);
define ("SECTION_EMAIL",6);
define ("SECTION_INFO",7);
define ("SECTION_DIRECTORY",8);

$SECTIONS = array (
    array(0, "Exchanges", "exchange.gif"),
    array(1, "Listings", "listing.png"),
    array(2, "Events", "news.png"),
    array(3, "Administration", "admin.png"),
    array(4, "Events", "member.png"),
    array(5, "Feedback", "feedback.png"),
    array(6, "Email", "contact.png"),
    array(7, "Info", "info.png"),
    array(8, "Directory", "directory.png"));

/**********************************************************/
/******************* GENERAL SETTINGS *********************/

define ("USE_RATES", false); // If turned on, listings will include a "Rate" field
define ("UNITS", l("Hores"));  // This setting affects functionality, not just text displayed, so if you want to use hours/minutes this needs to read "Hours" exactly.  All other unit descriptions are ok, but receive no special treatment (i.e. there is no handling of "minutes").

define ("MAX_FILE_UPLOAD","5000000"); // Maximum file size, in bytes, allowed for uploads to the server
define ("EMAIL_LISTING_UPDATES", true); // Should users receive automatic updates
                                                     // for new and modified listings?
define ("DEFAULT_UPDATE_INTERVAL", WEEKLY); // If automatic updates are sent, this is
                                                     // the default interval. Possible
                                                     // values are NEVER, DAILY, WEEKLY & MONTHLY.
// The following text will appear at the beggining of the email update messages
define ("LISTING_UPDATES_MESSAGE", l("Les seguents ofertes i demandes son noves o s'han actualitzat").".<p>".l("Si no vols rebre correus-e automàtics, o si vols canviar la seva frequencia, ho pots fer a l'àrea de")." <a href=http://".HTTP_BASE."/member_edit.php?mode=self>".l("Editar la meva informació personal")."</a>");

// Should inactive accounts have their listings automatically expired?
// This can be a useful feature.  It is an attempt to deal with the 
// age-old local currency problem of new members joining and then not 
// keeping their listings up to date or using the system in any way.  
// It is designed so that if a member doesn't record a trade OR update 
// a listing in a given period of time (default is six months), their 
// listings will be set to expire and they will receive an email to 
// that effect (as will the admin).
define ("EXPIRE_INACTIVE_ACCOUNTS",false); 

// If above is set, after this many days, accounts that have had no
// activity will have their listings set to expire.  They will have 
// to reactiveate them individually if they still want them.
define ("MAX_DAYS_INACTIVE","180");  

// How many days in the future the expiration date will be set for
define ("EXPIRATION_WINDOW","15");    

// How long should expired listings hang around before they are deleted?
define ("DELETE_EXPIRED_AFTER","90"); 

// The following message is the one that will be emailed to the person 
// whose listings have been expired (a delicate matter).
define ("EXPIRED_LISTINGS_MESSAGE", l("Hola").",\n\n".l("Com que no estàs actiu, el teu compte")." ".SITE_SHORT_TITLE." ".l("ha expirat fa")." ". EXPIRATION_WINDOW ." ".l("dies").".\n\n".l("Perquè")." ".SITE_LONG_TITLE." ".l("estigui actualitzat i en correcte...")." ".MAX_DAYS_INACTIVE." ".l("dies. Volem tenir el directori al dia...")."\n\n".l("Lamentem qualsevol problema que hagis...")." \n\n ".l("En tot cas, tens")." ". EXPIRATION_WINDOW ." ".l("dies per entrar amb la teva clau i reactivar...")." ". DELETE_EXPIRED_AFTER ." ".l("dies, període durant el que encara podràs...")." \n\n\n".l("Instruccions per a la reactivació").":\n1) ".l("Entrar amb la teva clau")."\n2) ".l("Anar a actualitzar comptes")."\n3) ".l("Seleccionar Editar comptes")."\n4) ".l("Selecciona el compte")."\n5) ".l("Desactiva la casella corresponent a")." ".l("'¿Se pondrá esta cuenta para finalización automática?'")."\n6) ".l("Pulsa Actualizar")."\n7) ".l("Repite los pasos 1-6 para todas las cuentas que quieres reactivar").". \n");

// The year your local currency started -- the lowest year shown
// in the Join Year menu option for accounts.
define ("JOIN_YEAR_MINIMUM", "2008");

define ("DEFAULT_COUNTRY", "Girona");
define ("DEFAULT_ZIP_CODE", "17100");
define ("DEFAULT_CITY", "La Bisbal");
define ("DEFAULT_STATE", "ES");
define ("DEFAULT_PHONE_AREA", "360");

// Should short date formats display month before day (US convention)?
define ("MONTH_FIRST", false);

define ("PASSWORD_RESET_SUBJECT", l("El teu compte del")." ". SITE_LONG_TITLEMI ." ");
define ("PASSWORD_RESET_MESSAGE", l("La clau per el")." ". SITE_LONG_TITLEMI ." ".l("ha sigut canviada (resetejada)").". ".l("Si no has demanat aquest reseteig, és possible qu hi hagi hagut algun problema mab la teva conte; pose't en contacta amb la persona administradora a")." ".PHONE_ADMIN.".\n\n".l("La teva nova clau està la final d'aquest missatge i la pots canviar entrant en el sistema a l'apartat de Perfil d'associat/a").".");
define ("NEW_MEMBER_SUBJECT", l("Benvingut/a al")." ". SITE_LONG_TITLEMI);
define ("NEW_MEMBER_MESSAGE", l("Hola, i benvingut/uda a la comunitat del")." ". SITE_LONG_TITLEMI ."\n\n".l("S'ha creat una conta d'associat/da per tu a")."\n \n\n".l("Si us plau, entra en el sistema i crea les teves ofertas i demandas").". ".l("El teu identificador d'usuari/a i clau estàn al final d'aquest missatge").". ".l("Pots canviar la clau entrant en el sistema a l'apartat de Perfil d'associat/a").".\n\n".l("Gracies per unirte al Banc de Temps").". ");
define ("NEW_MEMBER_PENDING", l("Hola, i benvingut/uda a la comunitat del")." ". SITE_LONG_TITLEMI ."\n\n".l("S'ha creat una conta d'associat/da per tu a")."<a href=http://".SERVER_DOMAIN.SERVER_PATH_URL."/member_login.php> ".SITE_SHORT_TITLE."</a>\n\n".l("Per ara no pots entrar al Banc de Temps fins que l'administrador t'autoritzi en el sistema").". ".l("El teu identificador d'usuari/a i clau estàn al final d'aquest missatge").".\n\n".l("Rebràs un correo-e quan la teva conta estigui plenament operativa").". ".l("També pots trucar a")." ".PHONE_ADMIN." ".l("per tal que activem la teva conta").".\n\n".l("Gracies per unirte al Banc de Temps").".");
define ("ACTIVE_MEMBER_SUBJECT", l("Conta activada al")." ". SITE_LONG_TITLEMI); 
define ("ACTIVE_MEMBER_MESSAGE", l("Hola, i benvingut/uda a la comunitat del")." ". SITE_LONG_TITLEMI ."\n\n".l("S'ha activat la conta d'asociat/da per tu a")."<a href=http://".SERVER_DOMAIN.SERVER_PATH_URL."/member_login.php> ".SITE_SHORT_TITLE."</a>\n\n".l("Si us plau, entra en el sistema i crea les teves ofertas i demandas").". ".l("El teu identificador d'usuari/a i clau estàn al final d'aquest missatge").". ".l("Pots canviar la clau entrant en el sistema a l'apartat de Perfil d'associat/a").".\n\n".l("Gracies per unirte al Banc de Temps").".");


/********************************************************************/
/************************* ADVANCED SETTINGS ************************/
// Normally, the defaults for the settings that follow don't need
// to be changed.

// What's the name and location of the stylesheet?
define ("SITE_STYLESHEET", "style.css");

// How long should trades be listed on the "leave feedback for 
// a recent exchange" page?  After this # of days they will be
// dropped from that list.
define ("DAYS_REQUEST_FEEDBACK", "30"); 

// Is debug mode on? (display errors to the general UI?)
define ("DEBUG",FALSE);

// Should adminstrative activity be logged?  Set to 0 for no logging; 1 to 
// log trades recorded by administrators; 2 to also log changes to member 
// settings (LEVEL 2 NOT YET IMPLEMENTED)
define ("LOG_LEVEL", 1);

// How many consecutive failed logins should be allowed before locking out an account?
// This is important to protect against dictionary attacks.  Don't set higher than 10 or 20.
define ("FAILED_LOGIN_LIMIT", 10);

// Are magic quotes on?  Site has not been tested with magic_quotes_runtime on, 
// so if you feel inclined to change this setting, let us know how it goes :-)
define ("MAGIC_QUOTES_ON",false);
set_magic_quotes_runtime (0);

// CSS-related settings.  If you'r looking to change colors, 
// best to edit the CSS rather than add to this...
$CONTENT_TABLE = array("id"=>"contenttable", "cellspacing"=>"0", "cellpadding"=>"3");

// System events are processes which only need to run periodically,
// and so are run at intervals rather than weighing the system
// down by running them each time a particlular page is loaded.
// System Event Codes (such as ACCOUNT_EXPIRATION) are defined in inc.global.php
// System Event Frequency (how many minutes between triggering of events)
$SYSTEM_EVENTS = array (
    ACCOUT_EXPIRATION => 1440);  // Expire accounts once a day (every 1440 minutes)


/**********************************************************/
//    Everything below this line simply sets up the config.
//    Nothing should need to be changed, here.

if (PEAR_PATH != "")
    ini_set("include_path", PEAR_PATH .'/'. PATH_SEPARATOR . ini_get("include_path"));  
 

if (DEFAULT_COUNTRY == "Espanya") {
    define ("ZIP_TEXT", l("CP"));
    define ("STATE_TEXT", l("Regió"));
} else {
    define ("ZIP_TEXT", l("CP"));
    define ("STATE_TEXT", l("Regió"));
}

if (DEBUG) {
   error_reporting(E_ALL);
} else {
   // Report simple running errors
   error_reporting(E_ERROR | E_WARNING | E_PARSE);
   }

define("LOAD_FROM_SESSION",-1);  // Not currently in use

// URL to PHP page which handles redirects and such.
define ("REDIRECT_URL",SERVER_PATH_URL."/redirect.php");

define ("AVISO_LEGAL",'<p>&nbsp;</p><p><font face="Arial" color="#336699" size="1"><strong>'.l("AVÍS LEGAL").'</strong> - '.l("La informació continguda en aquest correu-e és per l'us exclusiu de la persona o persones anomenades com a destinataries").". ".l("Aquest correu-e i, en el seu cas, els arxius adjunts, contenen informació confidencial i/o protegida legalment per lleis de propietat intelectual o per altres lleis").'.'.l("Aquest missatge no constitueix cap compromis per part dela persona remitent, si no hi ha cap pacte en contrari, previ o per escrit entre la persona destinataria i la remitent").'.'.l("Si no ets el destinatari/ària i reps...").' '.EMAIL_ADMIN.' '.l("y proceda inmediatamente a su total destrucción").". ".l("Así mismo, le informamos de que no debe, directa o indirectamente, usar, distribuir, reproducir, imprimir o copiar, total o parcialmente este mensaje si no es la persona destinataria designada").'.<br>');


define ("LOPD",'<p>&nbsp;</p><p><font face="Arial" color="#660000" size="1"><b>'.l("AVÍS LEGAL").'</b> - '.l("De conformidad con lo dispuesto en la Ley Orgánica 15/1999, de 13 de diciembre, de Protección de Datos de Carácter Personal, El").' '.SITE_LONG_TITLEMI .', '.l("le informa que los datos que usted remitió están incorporados a un fichero de su titularidad cuya finalidad es la participación en el").' '.SITE_LONG_TITLEMI .'. '.l("El").' '.SITE_LONG_TITLEMI .' '.l("se compromete a cumplir su obligación de guardar secreto respecto de los datos de carácter personal que figuran en el mismo y garantiza la adopción de las medidas de seguridad necesarias para velar por la confidencialidad de dichos datos, que conservará durante un periodo de dos años desde que Usted lo envió, transcurrido dicho periodo se procederá a su cancelación").'. '.l("Se le reconoce la posibilidad de ejercitar gratuitamente los derechos de acceso, rectificación, cancelación y oposición, en los términos previstos en la Ley Orgánica 15/1999, en la dirección arriba indicada").'.<br />'.l("Transcurridos ocho días desde la emisión de esta comunicación sin que usted manifieste nada en contrario, El").' '.SITE_LONG_TITLEMI .' '.l("entenderá que autoriza el tratamiento de sus datos en los términos indicados").'. </font><br>
</font></p>');

 define("ROTULO_MAIL", '<TABLE width="100%" border="0" cellPadding="0" cellSpacing="0" valign="middle"><TBODY><TR><TD width="100%" style="padding-left: 9px;font-family: Arial,Helvetica,sans-serif;background-color: #CC9900;">
     <table width="98%" height="61" border="0" cellpadding="0" cellspacing="0" valign="middle">
     <tbody>
       <tr>
         <td style="PADDING-RIGHT: 7px; PADDING-LEFT: 8px; FONT-WEIGHT: bold; FONT-SIZE: 25px; COLOR: #fff; FONT-FAMILY: Arial,Helvetica,sans-serif; BACKGROUND-COLOR: #666666" align="middle">&gt;</td>
         <td width="100%" style="color: #000000;font-size: 18px;PADDING-LEFT: 8px;"><strong> '.SITE_LONG_TITLE .'</strong></td>
       </tr>
     </tbody>
   </table>
           <div style="font-size: 12px; font-weight: bold; color: #666666; padding-bottom:1px;">'.SITE_LONG_TITLE.' '.SITE_LONG_TITLEMI.'</span></TD>
       </TR>
 </TBODY>
 </TABLE><p>&nbsp;</p>');
 
?>
