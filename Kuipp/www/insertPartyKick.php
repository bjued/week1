<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid	= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$iid	= $_POST['iid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$userID'");
	if (!$results) exit("1".$error.' - '.$errorNoUser);
	
	// Ensure userID is in a party
	$results = mysql_query("SELECT * FROM PartyMembers WHERE userID='$userID' AND accepted=1");
	if (!$results) exit("1".$error.' - '.$errorNotMember);
	$row = mysql_fetch_array($results);
	$partyID = $row['partyID'];
	$partyname = $row['name'];
	
	// Ensure iid is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$iid'");
	if (!$results) exit("1".$error.' - '.$errorNoUser);
	$row = mysql_fetch_array($results);
	$pnName = ucwords($row['firstName'].' '.substr($row['lastName'],0,1).'.');
	
	// Ensure iid is in the same party
	$results = mysql_query("SELECT * FROM PartyMembers WHERE partyID='$partyID' AND userID='$iid' AND accepted=1");
	if (mysql_num_rows($results)!=1) exit("1".$error.' - '.$errorAlreadyKicked);
	
	// Ensure userID hasn't already tried to kick iid
	$results = mysql_query("SELECT * FROM PartyKicks WHERE partyID='$partyID' AND userID='$userID' AND kickID='$iid'");
	if (mysql_num_rows($results)>0) exit("0");
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into database
	 */
	if (!mysql_query("INSERT INTO PartyKicks (partyID, userID, kickID) VALUES ('$partyID', '$userID', '$iid')")) exit("1".$error.' - '.$errorServerFailure);
	
	// Determine if a user has been dropped
	$results = mysql_query("SELECT * FROM PartyMembers WHERE partyID='$partyID' AND accepted=1");
	$numMembers = mysql_num_rows($results);
	
	$results = mysql_query("SELECT * FROM PartyKicks WHERE partyID='$partyID' AND kickID='$iid'");
	$numKicks = mysql_num_rows($results);
	
	if ($numKicks/$numMembers > 0.5) {
		mysql_query("DELETE FROM PartyMembers WHERE partyID='$partyID' AND userID='$iid'");
		
		// Revert all Kicks against iid and made by iid
		mysql_query("DELETE FROM PartyKicks WHERE kickID='$iid' OR userID='$iid'");
	
		/*
		 * Handle Push Notes
		 */
		// You've been kicked!
		$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM Users
WHERE userID='$iid'
QUERY;
		$results = mysql_query($query);
		while ($row = mysql_fetch_assoc($results)) {
			$pnDevice = $row['deviceToken'];
			$pnID = $row['userID'];
			if (strlen($pnDevice)>0) {
				$pn2DArray['aps'] = array("alert"=>"You have been kicked from your party!", "badge"=>$row['badges']+1, "sound"=>"default");
				$pn2DArray['kuipp'] = array("head"=>"Party", "partyID"=>(int)$partyID);
				mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
				include('pnQueue.php');
			}
		}
		
		// Someone's been kicked!
		$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM PartyMembers AS p
LEFT JOIN Users AS u USING(userID)
WHERE userID!='$iid'
AND accepted=1
AND partyID='$partyID'
QUERY;
		$results = mysql_query($query);
		while ($row = mysql_fetch_assoc($results)) {
			$pnDevice = $row['deviceToken'];
			$pnID = $row['userID'];
			if (strlen($pnDevice)>0) {
				$pn2DArray['aps'] = array("alert"=>"$pnName has been kicked from '$partyname'!", "badge"=>$row['badges']+1, "sound"=>"default");
				$pn2DArray['kuipp'] = array("head"=>"Party", "partyID"=>(int)$partyID);
				mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
				include('pnQueue.php');
			}
		}
	}

	mysql_close();
}
echo("0");
?>
