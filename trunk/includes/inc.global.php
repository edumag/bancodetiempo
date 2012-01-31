<?php

/************************************************************
This file includes necesary class files and other include files.
It also defines global constants, and kicks off the session. 
It should be included by all pages in the site.  It does not
need to be edited for site installation, and in fact should
only be modified with care.
************************************************************/

/*********************************************************/
/******************* GLOBAL CONSTANTS ********************/

// These constants should only be changed with extreme caution
define("LOGGED_OUT","!");
define("GO_BACK","< Back");
define("GO_NEXT","Next >");
define("GO_FINISH","Finish");
define("REDIRECT_ON_ERROR", true);
define("FIRST", true);
define("LONG_LONG_AGO", "1970-01-01");
define("FAR_FAR_AWAY", "2030-01-01");
define("ACTIVE","A");
define("INACTIVE","I");
define("EXPIRED","E");
define("WAITING","X");
define("DISABLED","D");
define("LOCKED","L");
define("BUYER","B");
define("SELLER","S");
define("POSITIVE","3");
define("NEGATIVE","1");
define("NEUTRAL","2");
define ("OFFER_LISTING", "Offer");
define ("OFFER_LISTING_CODE", "O");
define ("WANT_LISTING", "Want");
define ("WANT_LISTING_CODE", "W");
define("DAILY",1);
define("WEEKLY",7);
define("MONTHLY",30);
define("NEVER",0);

// The following constants are used for logging. Add new categories if
// needed, but edit existing ones with caution.
define("TRADE","T"); // Logging event category
define("TRADE_BY_ADMIN","A");
define("TRADE_ENTRY","T");
define("TRADE_REVERSAL","R");
define("FEEDBACK","F"); // Logging event category
define("FEEDBACK_BY_ADMIN","A");
define("ACCOUT_EXPIRATION","E"); // Logging event category - System Event
define("DAILY_LISTING_UPDATES","D"); // Logging event category - System Event
define("WEEKLY_LISTING_UPDATES","W"); // Logging event category - System Event
define("MONTHLY_LISTING_UPDATES","M"); // Logging event category - System Event

/*********************************************************/
define("LOCALX_VERSION", "0.3.2");

/**********************************************************/
/***************** DATABASE VARIABLES *********************/

/** Prefijo para los nombres de las tablas */
$prefijo_tablas = "bdt_";

define ("DATABASE_LISTINGS",   $prefijo_tablas. "listings");
define ("DATABASE_PERSONS",    $prefijo_tablas. "person");
define ("DATABASE_MEMBERS",    $prefijo_tablas. "member");
define ("DATABASE_TRADES",     $prefijo_tablas. "trades");
define ("DATABASE_LOGINS",     $prefijo_tablas. "logins");
define ("DATABASE_LOGGING",    $prefijo_tablas. "admin_activity");
define ("DATABASE_USERS",      $prefijo_tablas. "member");
define ("DATABASE_CATEGORIES", $prefijo_tablas. "categories");
define ("DATABASE_FEEDBACK",   $prefijo_tablas. "feedback");
define ("DATABASE_REBUTTAL",   $prefijo_tablas. "feedback_rebuttal");
define ("DATABASE_NEWS",       $prefijo_tablas. "news");
define ("DATABASE_UPLOADS",    $prefijo_tablas. "uploads");

/*********************************************************/
// This section is deprecated.  It has been relocated to 
// inc.config.php, and would be removed but for a bunch of
// references to the following two, now bogus, values...

// TODO: Clean up all references and remove the two lines below
define ("SITE_SECTION_DEFAULT",-1);		
define ("SITE_SECTION_OFFER_LIST",0); 
/*********************************************************/


$global = ""; 	// $global lets other includes know that 
					// inc.global.php has been included

session_start();

include_once("inc.config.php");
include_once(CLASSES_PATH ."class.datetime.php");
include_once(CLASSES_PATH ."class.error.php");
include_once(CLASSES_PATH ."class.database.php");
include_once(CLASSES_PATH ."class.login_history.php");
include_once(CLASSES_PATH ."class.member.php");
include_once(CLASSES_PATH ."class.page.php");
include_once(CLASSES_PATH ."class.logging.php");

// For maintenance, see inc.config.php
if(DOWN_FOR_MAINTENANCE and !$running_upgrade_script) {
	$p->DisplayPage(MAINTENANCE_MESSAGE);
	exit;
}

?>
