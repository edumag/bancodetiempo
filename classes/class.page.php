<?php 

if (!isset($global))
{
	die(__FILE__." was included without inc.global.php being included first.  Include() that file first, then you can include ".__FILE__);
}

class cPage {
	public $page_title;
	public $page_title_image; // Filename, no path
	public $page_header;	// HTML
	public $page_footer;	// HTML
	public $keywords;		
	public $site_section;
	public $sidebar_buttons; 	// An array of cMenuItem objects
	public $top_buttons;			// An array of cMenuItem objects    TODO: Implement top buttons...

	function __construct() {
		global $cUser, $SIDEBAR;
		
		$this->keywords = SITE_KEYWORDS;
		$this->page_header = PAGE_HEADER_CONTENT;
		//$this->page_footer = PAGE_FOOTER_CONTENT;
		
		foreach ($SIDEBAR as $button) {
			$this->AddSidebarButton($button[0], $button[1]);
		}

		if ($cUser->IsLoggedOn()) {	
			$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Les meves ofertes")."</div>", "listings_menu_ofertas.php");
			$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Les meves demandes")."</div>", "listings_menu_demandas.php");
			$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Intercanvis")."</div>", "exchange_menu.php");
			$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Membres")."</div>", "member_directory.php");
			$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Perfil")."</div>", "member_profile.php");
			//$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Altres")."</div>", "listings_menu_otros.php");
		}

		if ($cUser->member_role > 0) 
			$this->AddSidebarButton("<div class=\"costextnegreta\">".l("Administració")."</div>", "admin_menu.php");

	}		
									
	function AddSidebarButton ($button_text, $url) {
		$this->sidebar_buttons[] = new cMenuItem($button_text, $url);
	}
	
	function AddTopButton ($button_text, $url) { // Top buttons aren't integrated into header yet...
		$this->top_buttons[] = new cMenuItem($button_text, $url);
	}

	function MakePageHeader() {

		global $cUser, $tema, $DIR_BASE, $temaxdefecto;
		
      $plantilla = $DIR_BASE.'temes/'.$temaxdefecto.'/'.$tema->ruta('page','html','header.phtml'); 

		if(isset($this->page_title)) 
			$title = " - ". $this->page_title;
		else
			$title = "";

      ob_start();
      include ($plantilla);
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;

	}

	function MakePageMenu() {

		global $cUser, $cSite, $cErr, $DIR_BASE, $temaxdefecto, $tema;
		
      $plantilla = $DIR_BASE.'temes/'.$temaxdefecto.'/'.$tema->ruta('page','html','menu.phtml'); 

      ob_start();
      include ($plantilla);
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
	
	}

	function MakePageTitle() {
		global $SECTIONS;
		
		if (!isset($this->page_title) or !isset($this->site_section)) {
			return "";
		} else {
			if (!isset($this->page_title_image))
				$this->page_title_image = $SECTIONS[$this->site_section][2];

			return '<H2><IMG SRC="http://'. IMAGES_PATH . $this->page_title_image .'" align=middle>'. $this->page_title .'</H2><P>';
		}
	}

	function MakePageFooter() {
		
		 global $cUser, $cSite, $cErr, $DIR_BASE, $temaxdefecto, $tema;
		
      $plantilla = $DIR_BASE.'temes/'.$temaxdefecto.'/'.$tema->ruta('page','html','peu.phtml'); 

      ob_start();
      include ($plantilla);
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
	}	

	function DisplayPage($content = "") {
		global $cErr, $cUser;
		if ($content=="")
			$cErr->Error(l("DisplayPage() was called with no content included! Was a blank page intended?")."",ERROR_SEVERITY_HIGH,__FILE__,__LINE__);
		if ($_REQUEST["printer_view"]!=1 || !$cUser->IsLoggedOn()) { 
			print $this->MakePageHeader();
			print $this->MakePageMenu();	
		}
		else {
	
			print '<head><link rel="stylesheet" href="http://'. HTTP_BASE .'/print.css" type="text/css"></link></head>';
		}
		
		print $this->MakePageTitle();
		
		print $content;
		
		if ($_REQUEST["printer_view"]!=1 || !$cUser->IsLoggedOn()) { 
			print $this->MakePageFooter();
		}
	}
}

class cMenuItem {
	public $button_text;
	public $url;
	
	function __construct ($button_text, $url) {
		$this->button_text = $button_text;
		$this->url = $url;
	}
	
	function DisplayButton() {
		return "<div class=\"itemMenu\"><a class=\"aa\" href=\"http://". HTTP_BASE ."/". $this->url ."\">". $this->button_text ."</a></div>\n";

	}
}

$p = new cPage;

?>
