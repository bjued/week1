<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$partyname	= $_POST['pnm'];
$accepted	= $_POST['acp'];

$partyname	= addslashes(trim($partyname));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$userID'");
	if (!($row = mysql_fetch_array($results))) exit("1".$error.' - '.$errorNoUser);
	$pnName = ucwords($row['firstName'].' '.substr($row['lastName'],0,1).'.');
	
	// Ensure partyname is in the Database
	$results = mysql_query("SELECT * FROM Parties WHERE name='$partyname'");
	if (!($row = mysql_fetch_array($results))) exit("1".$error.' - '.$errorNoParty);
	$partyID = $row['partyID'];
	$leaderID = $row['leaderID'];
	
	// if joining, ensure party is not full
	if ($accepted=="1") {
		$results = mysql_query("SELECT * FROM PartyMembers WHERE partyID='$partyID' AND accepted=1");
		if (mysql_num_rows($results)>4) exit("1".$sorry.' - '.$sorryFullParty);
	}
	
	// if joining, ensure not already in a party
	if ($accepted=="1") {
		$results = mysql_query("SELECT * FROM PartyMembers WHERE userID='$userID' AND accepted=1");
		if (($row = mysql_fetch_array($results))) exit("1".$sorry.' - '.$sorryAlreadyInAParty);
	}
	
	$datetime	= date('Y-m-d H:i:s');
	
	if ($accepted=="1") {
		$set = "accepted=1,points=0,dateAccept='$datetime',nominateID='$leaderID'";
	} else {
		$set = "accepted=0,points=0,dateAccept='0000-00-00 00:00:00',nominateID=0";
	}
	
	/*
	 * Insert into the database
	 */
	if (!mysql_query("UPDATE PartyMembers SET $set WHERE partyID='$partyID' AND userID='$userID'")) exit("1".$error.' - '.$errorServerFailure);

	// Update
	if ($accepted!="1") mysql_query("DELETE FROM PartyKicks WHERE kickID='$userID' OR userID='$userID'");
	
	/*
	 * Handle Push Notes
	 */
	// Push to Everyone Else!
	$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM PartyMembers AS p
LEFT JOIN Users AS u USING(userID)
WHERE userID!='$userID'
AND accepted=1
AND partyID='$partyID'
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $row['userID'];
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"$pnName has ".($accepted=="1"?"joined":"left")." you in '$partyname'!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Party", "partyID"=>(int)$partyID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
			include('pnQueue.php');
		}
	}

	mysql_close();
}
echo("0");
?>
