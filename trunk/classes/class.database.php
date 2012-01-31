<?php 
if (!isset($global))
{
	die(__FILE__." was included directly.  This file should only be included via inc.global.php.  Include() that one instead.");
}

class cDatabase
{
	public $isConnected;
	public $db_link;

	function Database()
	{
		$this->isConnected = false;
	}

	function Connect()
	{
		if ($this->isConnected)
			return;

		$this->db_link = mysql_connect(DATABASE_SERVER,DATABASE_USERNAME,DATABASE_PASSWORD)
		       or die("Could not connect");	// TODO: fix error messages
		mysql_selectdb(DATABASE_NAME)
		       or die("Could not select database");	// TODO: fix error messages
		$this->isConnected=true;
      mysql_query("SET NAMES 'utf8'");
	}

	function Query($thequery)
	{
		if (!$this->isConnected)
			$this->Connect();

		$ret = mysql_query($thequery);
//		       or die ("Query failed: ".mysql_errno() . ": " . mysql_error()); // TODO: fix error messages
		return $ret;
	}

	function NumRows($thequery)
	{
		if (!$this->isConnected)
			$this->Connect();

		$result = mysql_query($thequery);

		return mysql_num_rows($result);
	}

	function MakeSimpleTable($theQuery)
	{
		$query = $this->Query($theQuery);

		/* Printing results in HTML */
		$table = "<TABLE>\n";
		while ($line = mysql_fetch_array($query, MYSQL_ASSOC)) {
			$table .= "\t<TR>\n";
			foreach ($line as $col_value)
			{
				$table .= "\t\t<TD>$col_value</TD>\n";
			}
			$table .= "\t</TR>\n";
		}
		$table .= "</TABLE>\n";

		return $table;
	}

	function EscTxt($text) {
		if($text) {
			if(MAGIC_QUOTES_ON) 
				return "'". $text ."'";
			else 
				return "'". addslashes($text) ."'";
		} else {
			return "null";
		}
	}
	
	function EscTxt2($text) {  // TODO: Rename to EscQueryTxt() and update through site
		if($text) {
			if(MAGIC_QUOTES_ON) 
				return "='". $text ."'";
			else 
				return "='". addslashes($text) ."'";
		} else {
			return " IS NULL";
		}
	}
		
	function UnEscTxt($text) {
		if(MAGIC_QUOTES_ON)
			return $text;
		else
			return stripslashes($text);
	}	

}


$cDB = new cDatabase;
?>
