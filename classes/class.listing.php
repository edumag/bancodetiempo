<?php 

if (!isset($global))
{
	die(__FILE__." was included without inc.global.php being included first.  Include() that file first, then you can include ".__FILE__);
}

include_once("class.category.php");
include_once("class.feedback.php");

class cListing
{
	public $member; // this will be an object of class cMember
	public $title;
	public $description;
	public $category; // this will be an object of class cCategory
	public $rate;
	public $status;
	public $posting_date; // the date a listing was created or last modified
	public $expire_date;
	public $reactivate_date;
	public $type;


	function __construct($member=null, $values=null) {
		if($member) {
			$this->member = $member;
			$this->title = $values['title'];
			$this->description = $values['description'];
			$this->rate = $values['rate'];
			$this->expire_date = $values['expire_date'];
			$this->type = $values['type'];
			$this->reactivate_date = null;
			$this->status = 'A';
			$this->category = new cCategory();
			$this->category->LoadCategory($values['category']);
		}
		
	}	

	function TypeCode() {
		if($this->type == OFFER_LISTING)
			return OFFER_LISTING_CODE;
		else
			return WANT_LISTING_CODE;
	}

	function TypeDesc($type_code) {
		if($type_code == OFFER_LISTING_CODE)
			return OFFER_LISTING;
		else
			return WANT_LISTING;
	}

	function SaveNewListing() {
		global $cDB, $cErr;

		$insert = $cDB->Query("INSERT INTO ".DATABASE_LISTINGS." (title, description, category_code, member_id, rate, status, expire_date, reactivate_date, type) VALUES (". $cDB->EscTxt($this->title) .",". $cDB->EscTxt($this->description) .",'". $this->category->id ."','". $this->member->member_id ."',". $cDB->EscTxt($this->rate) .",'". $this->status ."',". $cDB->EscTxt($this->expire_date) .",". $cDB->EscTxt($this->reactivate_date) .",'". $this->TypeCode() ."');");	

		return $insert;
	}			
		
	function SaveListing($update_posting_date=true) {
		global $cDB, $cErr;			
		
		if(!$update_posting_date)
			$posting_date = ", posting_date=posting_date";
		else
			$posting_date = "";

		$update = $cDB->Query("UPDATE ".DATABASE_LISTINGS." SET title=". $cDB->EscTxt($this->title) .", description=". $cDB->EscTxt($this->description) .", category_code='". $this->category->id ."', rate=". $cDB->EscTxt($this->rate) .", status='". $this->status ."', expire_date=". $cDB->EscTxt($this->expire_date) .", reactivate_date=". $cDB->EscTxt($this->reactivate_date) . $posting_date ." WHERE title=". $cDB->EscTxt($this->title) ." AND member_id='".$this->member->member_id ."' AND type='". $this->TypeCode() ."';");	

		return $update;
	}
	
	function DeleteListing($title,$member_id,$type_code) {
		global $cDB, $cErr;
		
		$query = $cDB->Query("DELETE FROM ". DATABASE_LISTINGS ." WHERE title=".$cDB->EscTxt($title)." AND member_id='".$member_id."' AND type='". $type_code ."';");

		return mysql_affected_rows();
	}

	function LoadListing($title,$member_id,$type_code)
	{
		global $cDB, $cErr;

		// select all offer data and populate the variables
		$query = $cDB->Query("SELECT description, category_code, member_id, rate, status, posting_date, expire_date, reactivate_date FROM ".DATABASE_LISTINGS." WHERE title=".$cDB->EscTxt($title)." AND member_id='".$member_id."' AND type='". $type_code ."';");
		
		if($row = mysql_fetch_array($query))
		{		
			$this->title=$title;
			$this->description=$cDB->UnEscTxt($row[0]);
			$this->member_id=$row[2];
			$this->rate=$cDB->UnEscTxt($row[3]);
			$this->status=$row[4];
			$this->posting_date=$row[5];
			$this->expire_date=$row[6];
			$this->reactivate_date=$row[7];
			$this->type=$this->TypeDesc($type_code);
			$this->category = new cCategory();
			$this->category->LoadCategory($row[1]);
		}
		else 
		{
			$cErr->Error("".l("There was an error accessing the")." '".$cDB->EscTxt($title)."' ".l("listing for")." ".$member_id.".  ".l("Torneu-ho a provar més tard").".");
			include("redirect.php");
		}		
		
		// load member associated with member_id
		$this->member = new cMember;
		$this->member->LoadMember($member_id);
		
		$this->DeactivateReactivate();
	}
	
    
	function DeactivateReactivate() {
		if($this->reactivate_date) {
			$reactivate_date = new cDateTime($this->reactivate_date);
			if ($this->status == INACTIVE and $reactivate_date->Timestamp() <= strtotime("now")) {
				$this->status = ACTIVE;
				$this->reactivate_date = null;
				$this->SaveListing();
			}
		}
		if($this->expire_date) {
			$expire_date = new cDateTime($this->expire_date);
			if ($this->status <> EXPIRED and $expire_date->Timestamp() <= strtotime("now")) {
				$this->status = EXPIRED;
				$this->SaveListing();
			}
		}
	}

	function ShowListing()
	{
		$output = $this->type . ":<BR>";
		$output .= $this->title . ", " . $this->description . ", " . $this->category->id . ", " . $this->member->member_id . ", " . $this->rate . ", " . $this->status . ", " . $this->posting_date . ", " . $this->expire_date . ", " . $this->reactivate_date . "<BR><BR>";
		$output .= $this->member->ShowMember();
		
		return $output;
	}
	
	function DisplayListing()
	{
		$output = "";
		if($this->description != "")
			$output .= "<STRONG>".l("Descripció").":</STRONG> ". $this->description ."<BR>";
		if($this->rate != "")
			$output .= "<STRONG>".l("Valoració").":</STRONG> ". $this->rate ."<BR>";
		$output .= $this->member->DisplayMember();
		return $output;
	}	
}

class cListingGroup
{
	public $title;
	public $listing;  // this will be an array of objects of type cListing
	public $num_listings;  // number of active offers
	public $type;
	public $type_code;
	
	function __construct($type) {
		$this->type = $type;
		if($type == OFFER_LISTING)
			$this->type_code = OFFER_LISTING_CODE;
		else
			$this->type_code = WANT_LISTING_CODE;
	}
	
	function InactivateAll($reactivate_date) {
		global $cErr;
		
		if (!isset($this->listing))
			return true;
		
		foreach($this->listing as $listing)	{
			$current_reactivate = new cDateTime($listing->reactivate_date, false);
			if(($listing->reactivate_date == null or $current_reactivate->Timestamp() < $reactivate_date->Timestamp()) and $listing->status != EXPIRED) {
				$listing->reactivate_date = $reactivate_date->MySQLDate();
				$listing->status = INACTIVE;
				$success = $listing->SaveListing();
				
				if(!$success)
					$cErr->Error(l("Could not inactivate listing").": '".$listing->title."'");
			}
		}
		return true;
	}
	
	function ExpireAll($expire_date) {
		global $cErr;
		
		if (!isset($this->listing))
			return true;
		
		foreach($this->listing as $listing)	{
			$listing->expire_date = $expire_date->MySQLDate();
			$success = $listing->SaveListing(false);
				
			if(!$success)
				$cErr->Error(l("Could not expire listing").": '".$listing->title."'");
		}
		return true;
	}	
	
	function LoadListingGroup($title=null, $category=null, $member_id=null, $since=null, $include_expired=true)
	{
		global $cDB, $cErr;

      //if ( empty($this->listing) ) return false;

		if($title == null)
			$this->title = "%";
		else
			$this->title = $title;
			
		if($category == null)
			$category = "%";
			
		if($member_id == null)
			$member_id = "%";
			
		if($since == null) 
			$since = "19990101000000";
			
		if($include_expired)
			$expired = "";
		else
			$expired = " AND expire_date is null";
			
		//select all the member_ids for this $title
		$query = $cDB->Query("SELECT title, member_id FROM ".DATABASE_LISTINGS.", ".DATABASE_CATEGORIES." WHERE title LIKE ".$cDB->EscTxt($this->title)." AND ".DATABASE_LISTINGS.".category_code =".DATABASE_CATEGORIES.".category_id AND ".DATABASE_CATEGORIES.".category_id LIKE '".$category."' AND type='". $this->type_code ."' AND member_id LIKE '".$member_id."' AND posting_date >= '". $since ."'". $expired ." ORDER BY ".DATABASE_CATEGORIES.".description, title, member_id;");

		// instantiate new cOffer objects and load them
		$i = 0;
		$this->num_listings = 0;
				
		while($row = mysql_fetch_array($query))
		{
			$this->listing[$i] = new cListing;			
			$this->listing[$i]->LoadListing($row[0],$row[1],$this->type_code);
			if($this->listing[$i]->status == 'A')
			{
				$this->num_listings += 1;
			}
			$i += 1;
		}

		if($i == 0) {
			return false;
		}

		return true;
	}
	
	function DisplayListingGroup($show_ids=false, $active_only=true)
	{
		global $cUser;
	
		$output = "";
		$current_cat = "";
		if(isset($this->listing)) {
			foreach($this->listing as $listing) {
				if($active_only and $listing->status != ACTIVE)
					continue; // Skip inactive items
					
				if($current_cat != $listing->category->id) {
					$output .= "<P><STRONG>" . l($listing->category->description) . "</STRONG><P>";
				}
				
				if ($listing->description != "")
					$details = "&#8212;". $listing->description;
				else
					$details = "";
				
				if($show_ids)
                {   $details .= " (". $listing->member_id; 
                    if($listing->member->balance <= 0 and $_REQUEST["type"] == "Want") 
					$details .= ': <font color="red">'.l('El teu saldo es inferior a 1 Hora').'</font>';
                    $details .= ")";
				}
				$output .= "<A HREF=http://".HTTP_BASE."/listing_detail.php?type=". $this->type ."&title=" . urlencode($listing->title) ."&member_id=". $listing->member_id ."><FONT SIZE=2>" . $listing->title ."</A>". $details ."</FONT><BR>";
			
				$current_cat = $listing->category->id;
				$current_title = $listing->title;
			}
		} 
		
		if($output == "")
			$output = l("No s'han trobat").".";

		return $output;
	}
}



class cTitleList  // This class circumvents the cListing class for performance reasons
{
	public $type;
	public $type_code;  // TODO: 'type' needs to be its own class which would include 'type_code'
	public $items_per_page;  // Not using yet...
	public $current_page;   // Not using yet...

	function __construct($type) {
		$this->type = $type;
		if($type == OFFER_LISTING)
			$this->type_code = OFFER_LISTING_CODE;
		else
			$this->type_code = WANT_LISTING_CODE;
	}	
									
	function MakeTitleArray($member_id="%") {
		global $cDB, $cErr;

		$query = $cDB->Query("SELECT DISTINCT title FROM ".DATABASE_LISTINGS." WHERE member_id LIKE '". $member_id . "' AND type='". $this->type_code ."';");

		$i=0;		
		while($row = mysql_fetch_array($query))
		{
			$titles[$i]= $cDB->UnEscTxt($row[0]);
			$i += 1;
		}
		
		if ($i == 0)
			$titles[0]= "";
		
		return $titles;
	}	

	function DisplayMemberListings($member) {
		global $cDB;

		$query = $cDB->Query("SELECT title FROM ".DATABASE_LISTINGS." WHERE member_id='".$member->member_id."' AND type='". $this->type_code ."' ORDER BY title;");
		
		$output = "";
		$current_cat = "";
		while($row = mysql_fetch_array($query)) {
			$output .= "<A HREF=listing_edit.php?title=" . urlencode($cDB->UnEscTxt($row[0])) ."&member_id=".$member->member_id ."&type=". $this->type ."&mode=" . $_REQUEST["mode"] ."><FONT SIZE=2>". $cDB->UnEscTxt($row[0]) ."</FONT></A><BR>";
		}

		return $output;
	}

}


?>
