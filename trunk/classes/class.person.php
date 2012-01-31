<?php 

class cPerson
{
	public $person_id;			
	public $member_id;
	public $primary_member;
	public $directory_list;
	public $first_name;
	public $last_name;
	public $mid_name;
	public $dob;
	public $mother_mn;
	public $email;
	public $phone1_area;
	public $phone1_number;
	public $phone1_ext;
	public $phone2_area;
	public $phone2_number;
	public $phone2_ext;
	public $fax_area;
	public $fax_number;
	public $fax_ext;
	public $address_street1;
	public $address_street2;
	public $address_city;
	public $address_state_code;
	public $address_post_code;
	public $address_country;
    public $imagen;


	function __construct($values=null) {
		if($values) {
			$this->member_id = $values['member_id'];
			$this->primary_member = $values['primary_member'];
			$this->directory_list = $values['directory_list'];
			$this->first_name = $values['first_name'];
			$this->last_name = $values['last_name'];
			$this->mid_name = $values['mid_name'];
			$this->dob = $values['dob'];
			$this->mother_mn = $values['mother_mn'];
			$this->email = $values['email'];
			$this->phone1_area = $values['phone1_area'];
			$this->phone1_number = $values['phone1_number'];
			$this->phone1_ext = $values['phone1_ext'];
			$this->phone2_area = $values['phone2_area'];
			$this->phone2_number = $values['phone2_number'];
			$this->phone2_ext = $values['phone2_ext'];
			$this->fax_area = $values['fax_area'];
			$this->fax_number = $values['fax_number'];
			$this->fax_ext = $values['fax_ext'];
			$this->address_street1 = $values['address_street1'];
			$this->address_street2 = $values['address_street2'];
			$this->address_city = $values['address_city'];
			$this->address_state_code = $values['address_state_code'];
			$this->address_post_code = $values['address_post_code'];
			$this->address_country = $values['address_country'];	
            $this->imagen = $values['imagen'];	
		}
	}

	function SaveNewPerson() {
		global $cDB, $cErr;

      $sql_duplicate_exists = "SELECT NULL FROM ".DATABASE_PERSONS." WHERE member_id='". $this->member_id ."' AND first_name". $cDB->EscTxt2($this->first_name) ." AND last_name". $cDB->EscTxt2($this->last_name) ." AND mother_mn". $cDB->EscTxt2($this->mother_mn) ." AND mid_name". $cDB->EscTxt2($this->mid_name) ." AND dob". $cDB->EscTxt2($this->dob) .";";

		$duplicate_exists = $cDB->Query($sql_duplicate_exists);
		
		if($row = mysql_fetch_array($duplicate_exists)) {
			$cErr->Error("No s'ha pogut desar un nou membre. Ja hi ha una persona amb el mateix nom, connom i data de naixement. Si vostè rep aquest error prém el botó Enrere, intentar tornar al menú i començar de nou.");
			include("redirect.php");
		}
		$nadie = "nadie";
      $sql_insert = "INSERT INTO ".DATABASE_PERSONS." (member_id, primary_member, directory_list, first_name, last_name, mid_name, dob, mother_mn, email, phone1_area, phone1_number, phone1_ext, phone2_area, phone2_number, phone2_ext, fax_area, fax_number, fax_ext, address_street1, address_street2, address_city, address_state_code, address_post_code, address_country, imagen) VALUES ('". $this->member_id ."','". $this->primary_member ."','". $this->directory_list ."',". $cDB->EscTxt($this->first_name) .",". $cDB->EscTxt($this->last_name) .",". $cDB->EscTxt($this->mid_name) .",". $cDB->EscTxt($this->dob) .",". $cDB->EscTxt($this->mother_mn) .",". $cDB->EscTxt($this->email) .",". $cDB->EscTxt($this->phone1_area) .",". $cDB->EscTxt($this->phone1_number) .",". $cDB->EscTxt($this->phone1_ext) .",". $cDB->EscTxt($this->phone2_area) .",". $cDB->EscTxt($this->phone2_number) .",". $cDB->EscTxt($this->phone2_ext) .",". $cDB->EscTxt($this->fax_area) .",". $cDB->EscTxt($this->fax_number) .",". $cDB->EscTxt($this->fax_ext) .",". $cDB->EscTxt($this->address_street1) .",". $cDB->EscTxt($this->address_street2) .",'". escapa($this->address_city) ."','". $this->address_state_code ."','". $this->address_post_code ."','". escapa($this->address_country) ."','". $nadie ."');";             //,'". $this->imagen ."');";
		$insert = $cDB->Query($sql_insert);
	
		return $insert;
	}
			
	function SavePerson() {
		global $cDB, $cErr;
		
		$update = $cDB->Query("UPDATE ". DATABASE_PERSONS ." SET member_id='". $this->member_id ."', primary_member='". $this->primary_member ."', directory_list='". $this->directory_list ."', first_name=". $cDB->EscTxt($this->first_name) .", last_name=". $cDB->EscTxt($this->last_name) .", mid_name=". $cDB->EscTxt($this->mid_name) .", dob=". $cDB->EscTxt($this->dob) .", mother_mn=". $cDB->EscTxt($this->mother_mn) .", email=". $cDB->EscTxt($this->email) .", phone1_area=". $cDB->EscTxt($this->phone1_area) .", phone1_number=". $cDB->EscTxt($this->phone1_number) .", phone1_ext=". $cDB->EscTxt($this->phone1_ext) .", phone2_area=". $cDB->EscTxt($this->phone2_area) .", phone2_number=". $cDB->EscTxt($this->phone2_number) .", phone2_ext=". $cDB->EscTxt($this->phone2_ext) .", fax_area=". $cDB->EscTxt($this->fax_area) .", fax_number=". $cDB->EscTxt($this->fax_number) .", fax_ext=". $cDB->EscTxt($this->fax_ext) .", address_street1=". $cDB->EscTxt($this->address_street1) .", address_street2=". $cDB->EscTxt($this->address_street2) .", address_city='". escapa($this->address_city) ."', address_state_code='". $this->address_state_code ."', address_post_code='". $this->address_post_code ."', address_country='". $this->address_country ."' WHERE person_id='".$this->person_id ."';");

		if(!$update)
			$cErr->Error("No s'han pogur guardar el canvis per '". $this->first_name ." ". $this->last_name ."'. Si us plau, prova-ho mes tard.");	
			
		return $update;
	}

	function LoadPerson($who)
	{
		global $cDB, $cErr;

		$query = $cDB->Query("SELECT member_id, primary_member, directory_list, first_name, last_name, mid_name, dob, mother_mn, email, phone1_area, phone1_number, phone1_ext, phone2_area, phone2_number, phone2_ext, fax_area, fax_number, fax_ext, address_street1, address_street2, address_city, address_state_code, address_post_code, address_country, imagen FROM ".DATABASE_PERSONS." WHERE person_id=".$who);
		
		if($row = mysql_fetch_array($query))
		{
			$this->person_id=$who;	
			$this->member_id=$row[0];
			$this->primary_member=$row[1];
			$this->directory_list=$row[2];
			$this->first_name=$cDB->UnEscTxt($row[3]);
			$this->last_name=$cDB->UnEscTxt($row[4]);
			$this->mid_name=$cDB->UnEscTxt($row[5]);
			$this->dob=$row[6];
			$this->mother_mn=$cDB->UnEscTxt($row[7]);
			$this->email=$row[8];
			$this->phone1_area=$row[9];
			$this->phone1_number=$row[10];
			$this->phone1_ext=$row[11];
			$this->phone2_area=$row[12];
			$this->phone2_number=$row[13];
			$this->phone2_ext=$row[14];
			$this->fax_area=$row[15];
			$this->fax_number=$row[16];
			$this->fax_ext=$row[17];
			$this->address_street1=$cDB->UnEscTxt($row[18]);
			$this->address_street2=$cDB->UnEscTxt($row[19]);
			$this->address_city=$row[20];
			$this->address_state_code=$row[21];
			$this->address_post_code=$row[22];
			$this->address_country=$row[23];
            $this->imagen=$row[24];
	
		}
		else 
		{
			$cErr->Error("Hi ha un error per accedir a aquesta persona (".$who.").  Si us plau, prova-ho mes tard");
			include("redirect.php");
		}		
	}		
	
	function DeletePerson() {
		global $cDB, $cErr;
		
		if($this->primary_member == 'Y') {
			$cErr->Error("No es pot eliminar el membre principal!");	
			return false;
		} 
		
		$delete = $cDB->Query("DELETE FROM ".DATABASE_PERSONS." WHERE person_id=". $this->person_id);
		
		unset($this->person_id);
		
		if (mysql_affected_rows() == 1) {
			return true;
		} else {
			$cErr->Error("Error deleting joint member. Si us plau, prova-ho mes tard");
		}
		
	}
							
	function ShowPerson()
	{
		$output = $this->person_id . ", " . $this->member_id . ", " . $this->primary_member . ", " . $this->directory_list . ", " . $this->first_name . ", " . $this->last_name . ", " . $this->mid_name . ", " . $this->dob . ", " . $this->mother_mn . ", " . $this->email . ", " . $this->phone1_area . ", " . $this->phone1_number . ", " . $this->phone1_ext . ", " . $this->phone2_area . ", " . $this->phone2_number . ", " . $this->phone2_ext . ", " . $this->fax_area . ", " . $this->fax_number . ", " . $this->fax_ext . ", " . $this->address_street1 . ", " . $this->address_street2 . ", " . $this->address_city . ", " . $this->address_state_code . ", " . $this->address_post_code . ", " . $this->address_country ."," . $this->imagen;
		
		return $output;
	}

	function Name() {
		return $this->first_name . " " .$this->last_name;	
	}
			
	function DisplayPhone($type)
	{
		global $cErr;

		switch ($type)
		{
			case "1":
				$phone_area = $this->phone1_area;
				$phone_number = $this->phone1_number;
				$phone_ext = $this->phone1_ext;
				break;
			case "2":
				$phone_area = $this->phone2_area;
				$phone_number = $this->phone2_number;
				$phone_ext = $this->phone2_ext;
				break;
			case "fax":
				$phone_area = $this->fax_area;
				$phone_number = $this->fax_number;
				$phone_ext = $this->fax_ext;
				break;								
			default:
				$cErr->Error("No existe ese tipo de teléfono.");
				return "ERROR";
		}
		
		if($phone_number != "") {
		    $phone = $phone_area . $phone_number;
		} else {
			$phone = "";
		}
		
		return $phone;
	}
}


class cPhone {
    public $area;
    public $prefix;
    public $suffix;
    public $ext;
    
    function __construct($phone_str=null) { // this constructor attempts to break down free-form phone #s
        if($phone_str) {                        // TODO: Use reg expressions to shorten this thing
            $ext = "";
            $phone_str = strtolower($phone_str);
            $phone_str = ereg_replace("\(","",$phone_str);
            $phone_str = ereg_replace("\)","",$phone_str);
            $phone_str = ereg_replace("-","",$phone_str);
            $phone_str = ereg_replace("\.","",$phone_str);
            $phone_str = ereg_replace(" ","",$phone_str);
            $phone_str = ereg_replace("e","",$phone_str);
            
            if (strlen($phone_str) == 9) {
                $this->area = substr($phone_str,0,3);
                $this->prefix = substr($phone_str,3,3);
                $this->suffix = substr($phone_str,6,3);
                $this->ext = $ext;
            } else {
                return false;
            }
        }
    }
    /* paso de renombrar */
    function SevenDigits() {
        return $this->prefix . $this->suffix;
    }
    
} 
?>
