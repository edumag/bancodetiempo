<?php 

if (!isset($global))
{
	die(__FILE__." was included without inc.global.php being included first.  Include() that file first, then you can include ".__FILE__);
}

include_once("class.person.php");
include_once("Text/Password.php");
require_once "Mail.php";
require_once "Mail/mime.php";

class cMember
{
	public $person;  // this will be an array of cPerson class objects
	public $member_id;
	public $password;
	public $member_role;
	public $security_q;
	public $security_a;
	public $status;
	public $member_note;
	public $admin_note;
	public $join_date;
	public $expire_date;
	public $away_date;
	public $account_type;
	public $email_updates;
	public $balance;

	function __construct($values=null) {
		if ($values) {
			$this->member_id = $values['member_id'];
			$this->password = $values['password'];
			$this->member_role = $values['member_role'];
			$this->security_q = $values['security_q'];
			$this->security_a = $values['security_a'];
			$this->status = $values['status'];
			$this->member_note = $values['member_note'];
			$this->admin_note = $values['admin_note'];
			$this->join_date = $values['join_date'];
			$this->expire_date = $values['expire_date'];
			$this->away_date = $values['away_date'];
			$this->account_type = $values['account_type'];
			$this->email_updates = $values['email_updates'];
			$this->balance = $values['balance'];	
		}
	}

	function SaveNewMember() {
		global $cDB, $cErr;	

      $sql = "INSERT INTO ".DATABASE_MEMBERS." (member_id, password, member_role, security_q, security_a, status, member_note, admin_note, join_date, expire_date, away_date, account_type, email_updates, balance) VALUES ('". $this->member_id ."',sha('". $this->password ."'),'". $this->member_role ."',". $cDB->EscTxt($this->security_q) .",". $cDB->EscTxt($this->security_a) .",'". $this->status ."',". $cDB->EscTxt($this->member_note) .",". $cDB->EscTxt($this->admin_note) .",'". $this->join_date ."',". $cDB->EscTxt($this->expire_date) .",". $cDB->EscTxt($this->away_date) .",'". $this->account_type ."',". $this->email_updates .",'". $this->balance ."');";

		$insert = $cDB->Query($sql);

		return $insert;
	}

	function RegisterWebUser()
	{	
		if (isset($_SESSION["user_login"]) and $_SESSION["user_login"] != LOGGED_OUT) {
			$this->member_id = $_SESSION["user_login"];
			$this->LoadMember($_SESSION["user_login"]);
		}
		else {
			$this->LoginFromCookie();
		}		
	}
	
	function LoginFromCookie()
	{
		if (isset($_COOKIE["login"]) && isset($_COOKIE["pass"]))
		{
         if ( ! $this->Login($_COOKIE["login"], $_COOKIE["pass"], true) ) {
            unset($_COOKIE["login"]);
            unset($_COOKIE["pass"]);
            }
		}
	}

	function IsLoggedOn()
	{
		if (isset($_SESSION["user_login"]) and $_SESSION["user_login"] != LOGGED_OUT)
			return true;
		else
			return false;
	}

	function Login($user, $pass, $from_cookie=false) {
		global $cDB,$cErr;

		$login_history = new cLoginHistory();

		$query = $cDB->Query("SELECT member_id, password, member_role FROM ".DATABASE_USERS." WHERE member_id = '".$user."' AND (password=sha('".$pass."') OR password='".$pass."') and status = 'A';");	
	
		if($row = mysql_fetch_array($query)) {

			$login_history->RecordLoginSuccess($user);
			$this->DoLoginStuff($user, $row["password"]);	// using pass from db since it's encrypted, and $pass isn't, if it was entered in the browser.
			return true;
		} elseif (!$from_cookie) {
			$query = $cDB->Query("SELECT NULL FROM ".DATABASE_USERS." WHERE status = 'L' and member_id='". $user ."';");
			if($row = mysql_fetch_array($query)) {
				$cErr->Error(l("El teu compte ha estat bloquejat per massa intents fallits. Si us plau, contacta amb")." <A HREF=contact.php>".l("l'administració")."</A> ".l("per desbloquejar").".");
			} else {
				
			$query = $cDB->Query("SELECT NULL FROM ".DATABASE_USERS." WHERE status = 'X' and member_id='". $user ."';");
			if($row = mysql_fetch_array($query)) {
				$cErr->Error(l("Encara no t'ha donat d'alta. Tingues una mica de paciència").".");
				} else {
				$cErr->Error(l("Contrasenya o número d'identitat incorrecte. Si us plau, proba de nou, o veu")." <A HREF=password_reset.php>".l("aquí")."</A> ".l("per reiniciar la contrasenya").".", ERROR_SEVERITY_INFO);}
			}
			$login_history->RecordLoginFailure($user);
			return false;
		}	
		return false;
	}
	
	function ValidatePassword($pass) {
		global $cDB;

		$query = $cDB->Query("SELECT member_id, password, member_role FROM ".DATABASE_USERS." WHERE member_id = '".$this->member_id."' AND (password=sha('".$pass."') OR password='".$pass."');");	

		if($row = mysql_fetch_array($query))
			return true;
		else
			return false;
	}
	function UnlockAccount() {
		$history = new cLoginHistory;
		$has_logged_on = $history->LoadLoginHistory($this->member_id);
		if($has_logged_on) {
			$consecutive_failures = $history->consecutive_failures;
			$history->consecutive_failures = 0;  // Set count back to zero whether locked or not
			$history->SaveLoginHistory();	
		} 
		
		if($this->status == LOCKED) {
			$this->status = ACTIVE;
			if($this->SaveMember()) {
				return $consecutive_failures;
			}			
		}
		return false;
	}
	
	function DeactivateMember() {
		if($this->status == ACTIVE) {
			$this->status = INACTIVE;
			return $this->SaveMember();
		} else {
			return false;	
		}
	}
	
	function ReactivateMember() {
		if($this->status != ACTIVE) {
			$this->status = ACTIVE;
			return $this->SaveMember();
		} else {
			return false;	
		}
	}
	
	function ChangePassword($pass) { // TODO: Should use SaveMember and should reset $this->password
		global $cDB, $cErr;
		
		$update = $cDB->Query("UPDATE ". DATABASE_MEMBERS ." SET password=sha('". $pass ."') WHERE member_id='". $this->member_id ."';");
		
		if($update) {
			return true;
		} else {
			$cErr->Error(l("Hi ha hagut un error redirigint la contrasenya. Si us plau intenta-ho més tard").".");
			include("redirect.php");
		}
	}
	
	function GeneratePassword() {  
		return Text_Password::create(6) . chr(rand(50,57));
	}

	function DoLoginStuff($user, $pass)
	{
		global $cDB;
		
		setcookie("login",$user,time()+60*60*24*1,"/");
		setcookie("pass",$pass,time()+60*60*24*1,"/");

		$this->LoadMember($user);
		$_SESSION["user_login"] = $user;
	}

	function UserLoginPage() // A free-standing login page
	{
// ORIGINAL (PRIMERA LINEA)	$output = "<DIV STYLE='width=60%; padding: 5px;'><FORM ACTION=".SERVER_PATH_URL."/login.php METHOD=POST>
// Cambiada también tercera línea: 
//					<INPUT TYPE=HIDDEN NAME=location VALUE='".$_SERVER["REQUEST_URI"]."'>
		$output = "<DIV STYLE='width=60%; padding: 5px;'><FORM ACTION=login.php METHOD=POST>
					<INPUT TYPE=HIDDEN NAME=action VALUE=login>
					<INPUT TYPE=HIDDEN NAME=location VALUE='".$_SERVER["REQUEST_URI"]."'>
					<TABLE class=NoBorder><TR><TD ALIGN=LEFT>".l("Identificador").":</TD><TD ALIGN=LEFT><INPUT TYPE=TEXT SIZE=12 NAME=user></TD></TR>
					<TR><TD ALIGN=LEFT>".l("Clau").":</TD><TD ALIGN=LEFT><INPUT TYPE=PASSWORD SIZE=12 NAME=pass></TD></TR></TABLE>
					<DIV align=LEFT><INPUT TYPE=SUBMIT VALUE='Login'></DIV>
					</FORM></DIV>
					<BR>
					".l("Si encara no tens un compte").", <A HREF=member_self.php>".l("inscriu-te")."</A> ".l("per unir-te al banc de temps").".
					<BR>";	
		return $output;
	}

	function UserLoginLogout() {
		if ($this->IsLoggedOn())
		{
	//		$output = "<FONT SIZE=1><A HREF='".SERVER_PATH_URL."/member_logout.php'>Logout</A>&nbsp;&nbsp;&nbsp;";
                $output = "<A class=\"am\" HREF='".SERVER_PATH_URL."/member_logout.php'><B>".l("Sortir")."</B></A>";
		} else {
	//		$output = "<FONT SIZE=1><A HREF='".SERVER_PATH_URL."/member_login.php'>Login</A>&nbsp;&nbsp;&nbsp;";
                $output = "<A class=\"am\" HREF='".SERVER_PATH_URL."/member_login.php'><B>".l("Entrar")."</B></A>";		
		}

		return $output;		
	}

	function MustBeLoggedOn()
	{
		global $p, $cErr;
		
		if ($this->IsLoggedOn())
			return true;
		
		// user isn't logged on, but is in a section of the site where they should be logged on.
		$_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
		$cErr->SaveErrors();
		header("location:http://".HTTP_BASE."/login_redirect.php");
				
		exit;
	}

	function Logout() {
		setcookie("login","",time()-3600,"/");
		setcookie("pass","",time()-3600,"/");
		$_SESSION["user_login"] = LOGGED_OUT;
	}

	function MustBeLevel($level) {
		global $p;
		$this->MustBeLoggedOn(); // seems prudent to check first.

		if ($this->member_role<$level)
		{
			$page = "<DIV class='AccessDenied'>".l("Ho sento, no teniu permís per accedir").".<BR></DIV>";
			$p->DisplayPage($page);
			exit;

		}

	}
	
	function LoadMember($member, $redirect=true) {
		global $cDB, $cErr;

		//
		// select all Member data and populate the properties
		//
		$query = $cDB->Query("SELECT member_id, password, member_role, security_q, security_a, status, member_note, admin_note, join_date, expire_date, away_date, account_type, email_updates, balance FROM ".DATABASE_MEMBERS." WHERE member_id='".$member."'");
	
		if($row = mysql_fetch_array($query))
		{		
			$this->member_id=$row[0];
			$this->password=$row[1];
			$this->member_role=$row[2];
			$this->security_q=$cDB->UnEscTxt($row[3]);
			$this->security_a=$cDB->UnEscTxt($row[4]);
			$this->status=$row[5];
			$this->member_note=$cDB->UnEscTxt($row[6]);
			$this->admin_note=$cDB->UnEscTxt($row[7]);
			$this->join_date=$row[8];
			$this->expire_date=$row[9];
			$this->away_date=$row[10];
			$this->account_type=$row[11];
			$this->email_updates=$row[12];
			$this->balance=$row[13];		
		}
		else
		{
			if ($redirect) {
				$cErr->Error("".l("Hi ha hagut un error per que puguis accedir")." (".$member."). ".l("Si us plau, torna més tard").".");
				include(dirname(__FILE__)."/../redirect.php");
			}
			return false;
		}	
						
		//
		// Select associated person records and load into person object array
		//

		$query = $cDB->Query("SELECT person_id FROM ".DATABASE_PERSONS." WHERE member_id='".$member."' ORDER BY primary_member DESC, last_name, first_name");
		$i = 0;
		
		while($row = mysql_fetch_array($query))
		{
			$this->person[$i] = new cPerson;			// instantiate new cPerson objects and load them
			$this->person[$i]->LoadPerson($row[0]);
			$i += 1;
		}

		if($i == 0)
		{
			if ($redirect) {
				$cErr->Error("".l("S'ha produït un error en accedir al membre")." (".$member."). ".l("Si us plau, prova'ho més tard").".");
            
				include("redirect.php");
			}
			return false;
		}
		return true;
	}
	
	function ShowMember()
	{
		$output = l("Member Data").":<BR>";
		$output .= $this->member_id . ", " . $this->password . ", " . $this->member_role . ", " . $this->security_q . ", " . $this->security_a . ", " . $this->status . ", " . $this->member_note . ", " . $this->admin_note . ", " . $this->join_date . ", " . $this->expire_date . ", " . $this->away_date . ", " . $this->account_type . ", " . $this->email_updates . ", " . $this->balance . "<BR><BR>";
		
		$output .= l("Person Data").":<BR>";
		
		foreach($this->person as $person)
		{
			$output .= $person->ShowPerson();
			$output .= "<BR><BR>";
		}			
						
		return $output;
	}		
	
	function UpdateBalance($amount) {
		$this->balance += $amount;
		return $this->SaveMember();
	}
	
	function SaveMember() {
		global $cDB, $cErr;				
		
		$update = $cDB->Query("UPDATE ".DATABASE_MEMBERS." SET password='". $this->password ."', member_role=". $this->member_role .", security_q=". $cDB->EscTxt($this->security_q) .", security_a=". $cDB->EscTxt($this->security_a) .", status='". $this->status ."', member_note=". $cDB->EscTxt($this->member_note) .", admin_note=". $cDB->EscTxt($this->admin_note) .", join_date='". $this->join_date ."', expire_date=". $cDB->EscTxt($this->expire_date) .", away_date=". $cDB->EscTxt($this->away_date) .", account_type='". $this->account_type ."', email_updates=". $this->email_updates .", balance=". $this->balance ." WHERE member_id='".$this->member_id ."';");	

		if(!$update)
			$cErr->Error(l("No es poden desar els canvis per a")." '". $this->member_id ."'. ".l("Si us plau, prova'ho més tard").".");

		foreach($this->person as $person) {
			$person->SavePerson();
		}
				
		return $update;	
	}
	
	function PrimaryName () {
		return $this->person[0]->first_name . " " . $this->person[0]->last_name;
	}
	
	function VerifyPersonInAccount($person_id) { // Make sure hacker didn't manually change URL
		global $cErr;
		foreach($this->person as $person) {
			if($person->person_id == $person_id)
				return true;
		}
		$cErr->Error(l("Invalid person id in URL. This break-in attempt has been reported").".",ERROR_SEVERITY_HIGH);
		include("redirect.php");
	}
	
	function PrimaryAddress () {
		if($this->person[0]->address_street1 != "") {
			$address = $this->person[0]->address_street1 . ", ";
			if($this->person[0]->address_street2 != "")
				$address .= $this->person[0]->address_street2 . ", ";
		} else {
			$address = "";
		}
		
		return $address . $this->person[0]->address_city;
	}
	
	function AllNames () {
		foreach($this->person as $person) {
			if($person->primary_member == "Y") {
				$names = $person->first_name ." ". $person->last_name;
			} else {
				$names .= ", ". $person->first_name ." ". $person->last_name;
			}	
		}
		return $names;
	}
	
	function AllPhones () {
		$phones = "";
		$reg_phones[]="";
		$fax_phones[]="";
		foreach($this->person as $person) {
			if($person->primary_member == "Y") {
				if($person->phone1_number != "") {
					$phones .= $person->DisplayPhone(1);
					$reg_phones[] = $person->DisplayPhone(1);
				}
				if($person->phone2_number != "") {
					$phones .= ", ". $person->DisplayPhone(2);
					$reg_phones[] = $person->DisplayPhone(2);
				}
				if($person->fax_number != "") {
					$phones .= ", ". $person->DisplayPhone("fax"). " (Fax)";
					$fax_phones[] = $person->DisplayPhone("fax");
				}
			} else {
				if($person->phone1_number != "" and array_search($person->DisplayPhone(1), $reg_phones) === false){ 
					$phones .= ", ". $person->DisplayPhone(1). " (". $person->first_name .")";
					$reg_phones[] = $person->DisplayPhone(1);
				}
				if($person->phone2_number != "" and array_search($person->DisplayPhone(2), $reg_phones) === false) {
					$phones .= ", ". $person->DisplayPhone(2). " (". $person->first_name .")";
					$reg_phones[] = $person->DisplayPhone(2);
				}
				if($person->fax_number != "" and array_search($person->DisplayPhone("fax"), $fax_phones) === false) {
					$phones .= ", ". $person->DisplayPhone("fax"). " (". $person->first_name ."'s Fax)";
					$fax_phones[] = $person->DisplayPhone("fax");
				}
			}	
		}
		return $phones;		
	}
	
	function AllEmails () {
		foreach($this->person as $person) {
			if($person->primary_member == "Y") {
				$emails = '<A HREF=email.php?email_to='. $person->email .'&member_to='. $this->member_id .'>'. $person->email .'</A>';
			} else {
				if($person->email != "" and strpos($emails, $person->email) === false)
					$emails .= ', <A HREF=email.php?email_to='. $person->email .'&member_to='. $this->member_id .'>'. $person->email .'</A> ('. $person->first_name .')';
			}	
		}
		return $emails;	
	}
	
	function VerifyMemberExists($member_id) {
		global $cDB;
	
		$query = $cDB->Query("SELECT NULL FROM ".DATABASE_MEMBERS." WHERE member_id='".$member_id."'");
		
		if($row = mysql_fetch_array($query))
			return true;
		else
			return false;
	}
	
	function MemberLink () {
		return "<A HREF=member_summary.php?member_id=". $this->member_id .">". $this->member_id ."</A>";
	}
	
	function DisplayMember () {
		$output = "<STRONG>".l("Usuari/ària").":</STRONG> ". $this->PrimaryName() . " (". $this->MemberLink().")"."<BR>";
        if($this->member_role<1){
        //$output .= "<STRONG>".l("Saldo de")." ". $this->PrimaryName() . ": </STRONG>";
        $output .= "<STRONG>".l("Saldo").": </STRONG>";
        if ($this->balance <= 0)
            $output .= '<font color="red">'." ". $this->balance ." ". strtolower(UNITS) .'</font><BR>';
        else        
            $output .= " ". $this->balance ." ". strtolower(UNITS) .'<BR>';
        }      
		$stats = new cTradeStats($this->member_id);
		$output .= "<STRONG>".l("Activitat").":</STRONG> ";
		if ($stats->most_recent == "")
			$output .= l("Sense intercanvis de moment")."<BR>";
		else		
			$output .= '<A HREF="trade_history.php?mode=other&member_id='. $this->member_id .'">'. $stats->total_trades ." ".l("intercanvis en total")."</A> , ".l("sumant")." ". $stats->total_units . " ". strtolower(UNITS) . ", ".l("l'últim el dia")." ". $stats->most_recent->ShortDate() ."<BR>";
		$feedbackgrp = new cFeedbackGroup;
		$feedbackgrp->LoadFeedbackGroup($this->member_id);
		if(isset($feedbackgrp->feedback)) {
			$output .= "<b>".l("Valoració").":</b> <A HREF=feedback_all.php?mode=other&member_id=". $this->member_id . ">" . $feedbackgrp->PercentPositive() . "% ".l("vots positius")."</A> (" . $feedbackgrp->TotalFeedback() . " ".l("en total").", " . $feedbackgrp->num_negative ." ".l("negatius i")." " . $feedbackgrp->num_neutral . " ".l("neutrals").")<BR>";
		}

		$joined = new cDateTime($this->join_date);
		$output .= "<STRONG>".l("Apuntat").":</STRONG> ". $joined->ShortDate() ."<BR>";

		if($this->person[0]->email != "")
			$output .= "<STRONG>".l("Correu-e").":</STRONG> ". "<A HREF=email.php?email_to=". $this->person[0]->email ."&member_to=". $this->member_id .">". $this->person[0]->email ."</A><BR>";	
		if($this->person[0]->phone1_number != "")
			$output .= "<STRONG>".l("Telèfon").":</STRONG> ". $this->person[0]->DisplayPhone("1") ."<BR>";
		if($this->person[0]->phone2_number != "")
			$output .= "<STRONG>".l("Segon telèfon").":</STRONG> ". $this->person[0]->DisplayPhone("2") ."<BR>";
		if($this->person[0]->fax_number != "")
			$output .= "<STRONG>".l("Fax").":</STRONG> ". $this->person[0]->DisplayPhone("fax") ."<BR>";
			
		foreach($this->person as $person) {
			if($person->primary_member == "Y")
				continue;	// Skip the primary member, since we already displayed above
		
			if($person->directory_list == "Y") {
				$output .= "<STRONG>".l("Membre associat").":</STRONG> ". $person->first_name ." ". $person->last_name ."<BR>";
				if($person->email != "")
					$output .= "<STRONG>".l("Correu-e de")." ". $person->first_name .":</STRONG> ". "<A HREF=email.php?email_to=". $person->email ."&member_to=". $this->member_id .">". $person->email ."</A><BR>";
				if($person->phone1_number != "")
					$output .= "<STRONG>".l("Telèfon de")." ". $person->first_name .":</STRONG> ". $person->DisplayPhone("1") ."<BR>";
				if($person->phone2_number != "")
					$output .= "<STRONG>".l("Segon telèfon de")." ". $person->first_name .":</STRONG> ". $person->DisplayPhone("2") ."<BR>";						
				if($person->fax_number != "")
				$output .= "<STRONG>".l("Fax de")." ". $person->first_name .":</STRONG> ". $person->DisplayPhone("fax") ."<BR>";				
			}
		}		
	return $output;	
	}
	
	function MakeJointMemberArray() {
		global $cDB;
		
		$names="";				
		foreach ($this->person as $person) {
			if($person->primary_member != 'i') {
				$names[$person->person_id] = $person->first_name ." ". $person->last_name;
				}
		}
		
		return $names;	
	}		
	
	function DaysSinceLastTrade() {
		global $cDB;
	
		$query = $cDB->Query("SELECT max(trade_date) FROM ". DATABASE_TRADES ." WHERE member_id_to='".$this->member_id."' OR member_id_from='".$this->member_id."';");
		
		$row = mysql_fetch_array($query);
		
		if($row[0] != "")
			$last_trade = new cDateTime($row[0]);
		else
			$last_trade = new cDateTime($this->join_date);

		return $last_trade->DaysAgo();
	}
	
	function DaysSinceUpdatedListing() {
		global $cDB;
	
		$query = $cDB->Query("SELECT max(posting_date) FROM ". DATABASE_LISTINGS ." WHERE member_id='".$this->member_id."';");
		
		$row = mysql_fetch_array($query);
		
		if($row[0] != "")
			$last_update = new cDateTime($row[0]);
		else
			$last_update = new cDateTime($this->join_date);

		return $last_update->DaysAgo();
	}	
    
    function Limites ()
    {
        global $cDB, $cErr;
        
         if($this->balance <= 0) {
             $success5 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='B' WHERE status='A' AND member_id='". $this->member_id."' AND type='W';");  
             $success5 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='J' WHERE status='I' AND member_id='". $this->member_id."' AND type='W';");
             }
         else 
         {
             $success5 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='A' WHERE status='B' AND member_id='". $this->member_id."' AND type='W';"); 
             $success5 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='I' WHERE status='J' AND member_id='". $this->member_id."' AND type='W';"); 
                if ($this->balance >= 7 and $this->balance <12)
                {    
                $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='B' WHERE status='A' AND member_id='". $this->member_id."' AND type='O';");  
                $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='J' WHERE status='I' AND member_id='". $this->member_id."' AND type='O';");

                $demandas_necesarias=0;
                if($this->balance >= 7 and $this->balance <8)
                $demandas_necesarias=1;
                if($this->balance >= 8 and $this->balance <9)
                $demandas_necesarias=2;
                if($this->balance >= 9 and $this->balance <10)
                $demandas_necesarias=3;
                if($this->balance >= 10 and $this->balance <11)
                $demandas_necesarias=5;
                if($this->balance >= 11 and $this->balance <12)
                $demandas_necesarias=10;
     
                $query = $cDB->Query("SELECT COUNT(title) AS total FROM ".DATABASE_LISTINGS." WHERE status='A' AND  member_id='". $this->member_id."' AND type='W';");
                $demandas_actuales= mysql_fetch_row($query);    
                    if(doubleval($demandas_actuales[0]) >= $demandas_necesarias) {        
                        $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='A' WHERE status='B' AND member_id='". $this->member_id."' AND type='O';"); 
                        $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='I' WHERE status='J' AND member_id='". $this->member_id."' AND type='O';"); 
                        }
                }
                elseif ($this->balance < 7){
                $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='A' WHERE status='B' AND member_id='". $this->member_id."' AND type='O';"); 
                $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='I' WHERE status='J' AND member_id='". $this->member_id."' AND type='O';"); 
                }
                elseif ($this->balance >= 12){
                $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='B' WHERE status='A' AND member_id='". $this->member_id."' AND type='O';"); 
                $success6 = $cDB->Query("UPDATE ". DATABASE_LISTINGS ." SET status='J' WHERE status='I' AND member_id='". $this->member_id."' AND type='O';"); 
                }
           }
    }
    
    function LimitesPasados($tipo="")     //al llamarla hay que pasar el tipo, si es oferta o demanda   como "Oferta" o "Demanda"
    {  
        global $cDB, $cErr, $p; 
        
        if($_REQUEST["type"] == "Want")
            $tipo = "Demanda";
        if($_REQUEST["type"] == "Offer") 
            $tipo = "Oferta";
            
           if($this->balance <= 0 and $tipo=="Demanda"){ 
        $p->DisplayPage(l("Les teves demandes estan desactivades").".");
        exit;
        }
        
       elseif ($this->balance >= 7 and $this->balance <12 and $tipo=="Oferta"){
                $demandas_necesarias=0;
                if($this->balance >= 7 and $this->balance <8)
                $demandas_necesarias=1;
                if($this->balance >= 8 and $this->balance <9)
                $demandas_necesarias=2;
                if($this->balance >= 9 and $this->balance <10)
                $demandas_necesarias=3;
                if($this->balance >= 10 and $this->balance <11)
                $demandas_necesarias=5;
                if($this->balance >= 11 and $this->balance <12)
                $demandas_necesarias=10;
     
                $query = $cDB->Query("SELECT COUNT(title) AS total FROM ".DATABASE_LISTINGS." WHERE status='A' AND  member_id='". $this->member_id."' AND type='W';");
                $demandas_actuales= mysql_fetch_row($query);
                $demandas_actuales= doubleval($demandas_actuales[0]);
                $almenos= $demandas_necesarias-$demandas_actuales;
                if($demandas_actuales < $demandas_necesarias) {
                    if ($almenos == 1)       
                    $p->DisplayPage(l("Tus ofertas están desactivadas").". <BR>".l("Necesitas crear al menos")." <B>". $almenos ." ".l("demanda")." </B>".l("para que tus ofertas sean visibles").".</BR>");
                    else
                    $p->DisplayPage(l("Tus ofertas están desactivadas").". <BR>".l("Necesitas crear al menos")." <B>". $almenos ." ".l("demandas")." </B>".l("para que tus ofertas sean visibles").".</BR>"); 
                     exit;
                        }
        }

           elseif ($this->balance >= 12 and $tipo=="Oferta"){     
                    $p->DisplayPage(l("Tus ofertas están desactivadas").". <BR>".l("Has acumulado más de 12 horas de saldo").".<B> ".l("Tienes que demandar algún servicio")."</B> ".l("para que tus ofertas sean visibles").".</BR>"); 
                     exit;
        }
    }
        
}

class cMemberGroup {
	public $members;
	
	function LoadMemberGroup ($active_only=TRUE, $non_members=FALSE, $waiting_members=FALSE) {
		global $cDB;
				
		if($active_only)
			$exclusions = " AND status in ('A','L')";
		else
			$exclusions = null;

		if($waiting_members)
			$exclusions .= " AND status = 'X'";	
		
		if(!$non_members)
			$exclusions .= " AND member_role != '9'";


		
		$query = $cDB->Query("SELECT ".DATABASE_MEMBERS.".member_id FROM ". DATABASE_MEMBERS .",". DATABASE_PERSONS." WHERE ". DATABASE_MEMBERS .".member_id=". DATABASE_PERSONS.".member_id". $exclusions. " AND primary_member='Y' ORDER BY first_name, last_name;");
		
		$i=0;
		while($row = mysql_fetch_array($query))
		{
			$this->members[$i] = new cMember;
			$this->members[$i]->LoadMember($row[0]);
			$i += 1;
		}
		
		if($i == 0)
			return false;
		else
			return true;
	}	
	
	function MakeIDArray() {
		global $cDB, $cErr;
		
		$ids="";		
		if($this->members) {
			foreach($this->members as $member) {
					$ids[$member->member_id] = $member->PrimaryName() ." (". $member->member_id .")";
			}
		}
		
		return $ids;
	}	
	
	function MakeNameArray() {
		global $cDB, $cErr;
		
		$names["0"] = "";
		
		if($this->members) {
			foreach($this->members as $member) {
				foreach ($member->person as $person) {
					$names[$member->member_id ."?". $person->person_id] = $person->first_name ." ". $person->last_name ." (". $member->member_id .")";
				}
			}
		
			array_multisort($names);// sort purely by person name (instead of member, person)
		}
		
		return $names;
	}
	
	// Use of this function requires the inclusion of class.listing.php
	function EmailListingUpdates($interval) {
		if(!isset($this->members)) {
			if(!$this->LoadMemberGroup())
				return false;
		}

		$listings = new cListingGroup(OFFER_LISTING);
		$since = new cDateTime("-". $interval ." days"); 
		$listings->LoadListingGroup(null,null,null,$since->MySQLTime());
		$offered_text = $listings->DisplayListingGroup(true);
		$listings = new cListingGroup(WANT_LISTING);
		$listings->LoadListingGroup(null,null,null,$since->MySQLTime());
		$wanted_text = $listings->DisplayListingGroup(true);
		
		$email_text = "";
		if($offered_text != l("No s'han trobat").".")
			$email_text .= "<h2>".l("Llistes de Ofertes")."</h2><br>". $offered_text ."<p><br>";
		if($wanted_text != l("No s'han trobat").".")
			$email_text .= "<h2>".l("Llistes de Demandes")."</h2><br>". $wanted_text;
		if(!$email_text)
			return; // If no new listings, don't email
		
		$email_text = "<html><body>". LISTING_UPDATES_MESSAGE ."<p><br>".$email_text. "</body></html>";
			
		if ($interval == '1')
			$period = " ".l("el día")."";
		elseif ($interval == '7')
			$period = l("l'última setmana");
		else
			$period = l("l'últim mes");
		set_time_limit(0); 
        ignore_user_abort();   
		foreach($this->members as $member) {
			if($member->email_updates == $interval and $member->person[0]->email) {
	//			mail($member->person[0]->email, SITE_SHORT_TITLE .": Ofertas o demandas nuevas o actualizadas durante el periodo  ". $period, wordwrap($email_text, 64), "De:". EMAIL_ADMIN ."\nMIME-Version: 1.0\n" . "Content-type: text/html; charset=UTF-8"); 
    $text = wordwrap($email_text, 64);
    $html = iconv('utf-8', 'windows-1252', ROTULO_MAIL.$text.AVISO_LEGAL); 
    $to = $member->person[0]->email;
    $crlf = "\n";
    $headers = array ('From' => EMAIL_FROM,
    'To' => $to,
    'Subject' => SITE_SHORT_TITLE .": ".l("Ofertes i demandes noves o actualitzades en")." ". $period);
            $mime = new Mail_mime($crlf);
            $mime->get(array("text_encoding" => "8bit", "html_charset" => "UTF-8"));
            $mime->setTXTBody($text);
            $mime->setHTMLBody($html); 
            $body = $mime->get();
            $headers = $mime->headers($headers);
     $smtp = Mail::factory('mail');
     $mailed = $smtp->send($to, $headers, $body);
            }
		
		}
	
	}
	
	// Use of this function requires the inclusion of class.listing.php
	function ExpireListings4InactiveMembers() {
		if(!isset($this->members)) {
			if(!$this->LoadMemberGroup())
				return false;
		}
		
		foreach($this->members as $member) {
			if($member->DaysSinceLastTrade() >= MAX_DAYS_INACTIVE
			and $member->DaysSinceUpdatedListing() >= MAX_DAYS_INACTIVE) {
				$offer_listings = new cListingGroup(OFFER_LISTING);
				$want_listings = new cListingGroup(WANT_LISTING);
				
				$offered_exist = $offer_listings->LoadListingGroup(null, null, $member->member_id, null, false);
				$wanted_exist = $want_listings->LoadListingGroup(null, null, $member->member_id, null, false);
				
				if($offered_exist or $wanted_exist)	{
					$expire_date = new cDateTime("+". EXPIRATION_WINDOW ." days");
					if($offered_exist)
						$offer_listings->ExpireAll($expire_date);
					if($wanted_exist)
						$want_listings->ExpireAll($expire_date);
				
					if($member->person[0]->email != null) {
	//					mail($member->person[0]->email, "Información importante sobre tu cuenta en  ". SITE_SHORT_TITLE ." ", wordwrap(EXPIRED_LISTINGS_MESSAGE, 64), "De:". EMAIL_ADMIN); 
						$note = "";
						$subject_note = "";
					} else {
						$note = "\n\n***".l("NOTA: Este miembro no tiene email, hay que avisarle por teléfono de que su cuenta está inactiva").".";
						$subject_note = " ".l("(sin email)")."";
					}
					
	//				mail(EMAIL_ADMIN, SITE_SHORT_TITLE ." ofertas y demandas finalizadas de ". $member->member_id. $subject_note, wordwrap("Todas las ofertas y demandas de este miembro han sido finalizadas por inactividad.  To turn off this feature, see inc.config.php.". $note, 64) , "From:". EMAIL_ADMIN);
				}
			}
		}
	}
}

class cMemberGroupMenu extends cMemberGroup {
	public $id;
	public $name;
	public $person_id;

	function MakeMenuArrays() {
		global $cDB, $cErr;
		
		$i = 0;
		$j = 0;	
		foreach($this->members as $member) {
			foreach ($member->person as $person) {
				$this->id[$i] = $member->member_id;
				$this->name[$i][$j] = $person->first_name." ".$person->last_name;
				$this->person_id[$i][$j] = $person->person_id;
				$j += 1;
			}
			$i += 1;
		}
		
		if($i <> 0)
			return true;
		else 
			return false;
	}
}

class cBalancesTotal {
	public $balance;
    public $usuarios;
	function Balanced() {
		global $cDB, $cErr;
		
	//	$query = $cDB->Query("SELECT sum(balance) from ". DATABASE_MEMBERS .";");
		$query = $cDB->Query("SELECT sum(balance), COUNT(*) from ". DATABASE_MEMBERS .";"); 
        
		if($row = mysql_fetch_array($query)) {
			$this->balance = $row[0];
			$this->usuarios = $row[1];
			if($this->balance == (($this->usuarios)-1)*3)  // pablox if($row[0] == 0 aunque ahora no es así     CORREGIR LUEGO QUE SERA -1 PORQUE BORRAMOS AL ADMIN
				return true;
			else
				return false;
		} else {
			$cErr->Error(l("No s'ha pogut accedir a la informació sobre el saldo").". ".l("Torneu-ho a provar més tard").".");
			return false;
		}		
	}
}

$cUser = new cMember();
$cUser->RegisterWebUser();

?>
