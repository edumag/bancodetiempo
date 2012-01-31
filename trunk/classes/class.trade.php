<?php 

if (!isset($global))
{
	die(__FILE__." was included without inc.global.php being included first.  Include() that file first, then you can include ".__FILE__);
}

include_once("class.category.php");
include_once("class.feedback.php");

class cTrade {
	public $trade_id;
	public $trade_date;
	public $status;
	public $member_from;
	public $member_to;
	public $amount;
	public $category;		// this will be an object of class cCategory
	public $description;
	public $type;
	public $feedback_buyer;	// added after trade completed; object of type cFeedback
	public $feedback_seller; // added after trade completed; object of type cFeedback

	function __construct ($member_from=null, $member_to=null, $amount=null, $category=null, $description=null, $type='T') {
		if($member_from) {
			$this->status = 'V';  // Doesn't make sense for a new Trade to not be valid
			$this->amount = $amount;
			$this->description = $description;
			$this->member_from = $member_from;
			$this->member_to = $member_to;
			$this->type = $type;
			$this->category = new cCategory();
			$this->category->LoadCategory($category);
		}
	}
	
	function ShowTrade() {
		global $cDB;
		
		$content = $this->trade_id .", ". $this->trade_date .", ". $this->status .", ". $this->member_from->member_id .", ". $this->member_to->member_id .", ". $this->amount .", ". $this->category->id .", ". $this->description .", ". $this->type;
		
		return $content;
	}

	function SaveTrade() {  // This function should never be called directly
		global $cDB, $cErr;
		
		$insert = $cDB->Query("INSERT INTO ". DATABASE_TRADES ." (trade_date, status, member_id_from, member_id_to, amount, category, description, type) VALUES (now(), '". $this->status ."', '". $this->member_from->member_id ."', '". $this->member_to->member_id ."', ". $this->amount .", '".$this->category->id ."', ". $cDB->EscTxt($this->description) .", '". $this->type ."');");

		if(mysql_affected_rows() == 1) {
			$this->trade_id = mysql_insert_id();	
			$query = $cDB->Query("SELECT trade_date from ". DATABASE_TRADES ." WHERE trade_id=". $this->trade_id .";");
			$row = mysql_fetch_array($query);
			$this->trade_date = $row[0];	
			return true;
		} else {
			return false;
		}
	}
	
	function LoadTrade($trade_id) {
		global $cDB, $cErr;
		
		$query = $cDB->Query("SELECT date_format(trade_date,'%Y-%m-%d'), status, member_id_from, member_id_to, amount, description, type, category FROM ".DATABASE_TRADES." WHERE trade_id=".$trade_id.";");
		
		if($row = mysql_fetch_array($query)) {		
			$this->trade_id = $trade_id;
			$this->trade_date = $row[0];
			$this->status = $row[1];
			$this->member_from = new cMember;
			$this->member_from->LoadMember($row[2]);
			$this->member_to = new cMember;
			$this->member_to->LoadMember($row[3]);
			$this->amount = $row[4];
			$this->description = $cDB->UnEscTxt($row[5]);
			$this->type = $row[6];
			$this->category = new cCategory();
			$this->category->LoadCategory($row[7]);
			
			$feedback = new cFeedback;
			$feedback_id = $feedback->FindTradeFeedback($trade_id, $this->member_from->member_id);
			if($feedback_id) {
				$this->feedback_buyer = new cFeedback;
				$this->feedback_buyer->LoadFeedback($feedback_id);
			}
			$feedback_id = $feedback->FindTradeFeedback($trade_id, $this->member_to->member_id);
			if($feedback_id) {
				$this->feedback_seller = new cFeedback;
				$this->feedback_seller->LoadFeedback($feedback_id);
			}
			
		} else {
			$cErr->Error("S'ha produït un error en accedir a la taula d'intercanvis. Si us plau, intenti-ho més tard.");
			include("redirect.php");
		}				
	}

	// It is very important that this function prevent the database from going out balance.
	function MakeTrade($reversed_trade_id=null) { 
		global $cDB, $cErr;
		
		if ($this->amount <= 0 and $this->type != TRADE_REVERSAL) // Amount should be positive unless
			return false;									 // this is a reversal of a previous trade.
			
		if ($this->amount >= 0 and $this->type == TRADE_REVERSAL)	 // And likewise.
			return false;
			
		if ($this->member_from->member_id == $this->member_to->member_id)
			return false;		// don't allow trade to self
		
		$balances = new cBalancesTotal;
	
		// TODO: At some point, we should handle out-of-balance problems without shutting 
		// down all trades.  But for now, seems like a wonderfully simple solution.		
		if(!$balances->Balanced()) {
			$cErr->Error("La base de dades d'intercanvis té descompensada el balanç! Contacta amb l'administrador del sistema en ". EMAIL_ADMIN .".", ERROR_SEVERITY_HIGH);  

			include("redirect.php");
			exit;  // Probably unnecessary...
		}	

		// NOTE: Need table type InnoDB to do the following transaction-style statements.		
		$cDB->Query("SET AUTOCOMMIT=0");
		
		$cDB->Query("BEGIN");
		
		if($this->SaveTrade()) {
			$success1 = $this->member_from->UpdateBalance(-($this->amount));
			$success2 = $this->member_to->UpdateBalance($this->amount);
			
			if(LOG_LEVEL > 0 and $this->type != TRADE_ENTRY) {//Log if enabled & not an ordinary trade
				$log_entry = new cLogEntry (TRADE, $this->type, $this->trade_id);
				$success3 = $log_entry->SaveLogEntry();
			} else {
				$success3 = true;
			}
			
			if($reversed_trade_id) {  // If this is a trade reversal, need to mark old trade reversed
				$success4 = $cDB->Query("UPDATE ".DATABASE_TRADES." SET status='R', trade_date=trade_date WHERE trade_id=". $reversed_trade_id .";");
			} else {
				$success4 = true;
			}

			if($success1 and $success2 and $success3 and $success4) {
				$cDB->Query('COMMIT');
				$cDB->Query("SET AUTOCOMMIT=1"); // Probably isn't necessary...
                
                $this->member_from->Limites();
                $this->member_to->Limites();   
				return true;
			} else {
				$cDB->Query('ROLLBACK');
				$cDB->Query("SET AUTOCOMMIT=1"); // Probably isn't necessary...
				return false;
			}
		} else {
			$cDB->Query("SET AUTOCOMMIT=1"); // Probably isn't necessary...
			return false;
		}			
	}
	
	function ReverseTrade($description) { 	// This method allows administrators to reverse
		global $cUser;								// trades that were made in error.
		
		if($this->status == "R")
			return false;		// Can't reverse the same trade twice
			
		$new_trade = new cTrade;
        $trade_date = new cDateTime($this->trade_date);    				
		$new_trade->status = "V";
		$new_trade->member_from = $this->member_from;
		$new_trade->member_to = $this->member_to;
		$new_trade->amount = -$this->amount;
		$new_trade->category = $this->category;
		$new_trade->description = "[Desfet l´intercavi nº". $this->trade_id." de ". $trade_date->ShortDate()." per el administrador] ". $description;           //'". $cUser->member_id ."'    
		$new_trade->type = "R";
		return $new_trade->MakeTrade($this->trade_id);
	}
}

class cTradeGroup {
	public $trade;   	// an array of cTrade objects
	public $member_id;
	public $from_date;
	public $to_date;
	
	function __construct($member_id="%", $from_date=LONG_LONG_AGO, $to_date=FAR_FAR_AWAY) {
		$this->member_id = $member_id;
		$this->from_date = $from_date;
		$this->to_date = $to_date;
	}
	
	function LoadTradeGroup() {
		global $cDB, $cErr;
		
		$to_date = strtotime("+1 days", strtotime($this->to_date));
		
		//select all trade_ids for this member
		$query = $cDB->Query("SELECT trade_id FROM ".DATABASE_TRADES." WHERE (member_id_from LIKE '".$this->member_id."' OR member_id_to LIKE '". $this->member_id ."') AND trade_date > '". $this->from_date ."' AND trade_date < '". date("Ymd", $to_date) ."' ORDER BY trade_date DESC;");

		// instantiate new cTrade objects and load them
		$i=0;
		while($row = mysql_fetch_array($query))
		{
			$this->trade[$i] = new cTrade;			
			$this->trade[$i]->LoadTrade($row[0]);
			$i += 1;
		}
		
		if($i == 0)
			return false;
		else
			return true;
	}
	
	function DisplayTradeGroup() {
		global $cDB, $cUser;
		
		$output = "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=\"100%\"><TR BGCOLOR=\"#d8dbea\"><TD><FONT SIZE=2><B>Data</B></FONT></TD><TD><FONT SIZE=2><B>De</B></FONT></TD><TD><FONT SIZE=2><B>A</B></FONT></TD><TD ALIGN=RIGHT><FONT SIZE=2><B>". UNITS ."&nbsp;</B></FONT></TD><TD><FONT SIZE=2><B>&nbsp;Descripció</B></FONT></TD></TR>";
		
		if(!$this->trade)
			return $output. "</TABLE>";   // No trades yet, presumably
		
		$i=0;
		foreach($this->trade as $trade) {
			if($trade->type == TRADE_REVERSAL or $trade->status == TRADE_REVERSAL)
				$fcolor = "pink";
			elseif ($trade->member_to->member_id == $this->member_id)
				$fcolor = "#4a5fa4";
			else
				$fcolor = "#554f4f";
				
			if($i % 2)
				$bgcolor = "#e4e9ea";
			else
				$bgcolor = "#FFFFFF";
			
			$trade_date = new cDateTime($trade->trade_date);			
			
			$output .= "<TR VALIGN=TOP BGCOLOR=". $bgcolor ."><TD><FONT SIZE=2 COLOR=".$fcolor.">". $trade_date->ShortDate()."</FONT></TD><TD><FONT SIZE=2 COLOR=".$fcolor.">". $trade->member_from->member_id ."</FONT></TD><TD><FONT SIZE=2 COLOR=".$fcolor.">". $trade->member_to->member_id ."</FONT></TD><TD ALIGN=RIGHT><FONT SIZE=2 COLOR=".$fcolor.">". $trade->amount ."&nbsp;</FONT></TD><TD><FONT SIZE=2 COLOR=".$fcolor.">". $cDB->UnEscTxt($trade->description) ."</FONT></TD></TR>";
			$i+=1;
		}
		
		return $output . "</TABLE>";
	}
	
	function MakeTradeArray() {
		$trades = "";
		if($this->trade) {
			foreach($this->trade as $trade) {
				if($trade->type != "R" and $trade->status != "R") {
                    $trade_date = new cDateTime($trade->trade_date);
					$trades[$trade->trade_id] = "Nº ". $trade->trade_id ." : ". $trade->amount ." ". UNITS . " de ". $trade->member_from->member_id ." a ". $trade->member_to->member_id .", el ". $trade_date->ShortDate();
				}
			}
		}
		
		return $trades;
	}
}

class cTradeStats extends cTradeGroup {
	public $total_trades = 0;
	public $total_units = 0;
	public $most_recent = ""; // Will be an object of class cDateTime
	
	function __construct ($member_id="%", $from_date=LONG_LONG_AGO, $to_date=FAR_FAR_AWAY) {
      parent::__construct($member_id, $from_date, $to_date);
		if(!$this->LoadTradeGroup())
			return;
		
		foreach($this->trade as $trade) {
			if ($trade->type == TRADE_REVERSAL or $trade->status == TRADE_REVERSAL)
				continue; // skip reversed trades
				
			$this->total_trades += 1;
			$this->total_units += $trade->amount;
			
			if($this->most_recent == "") {
				$this->most_recent = new cDateTime($trade->trade_date);
			} elseif ($this->most_recent->MySQLDate() < $trade->trade_date) {
				$this->most_recent->Set($trade->trade_date);
			}	
		}
	}

}

?>
