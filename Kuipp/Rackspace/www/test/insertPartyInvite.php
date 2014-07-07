<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$inviteID	= $_POST['iid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure inviteID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$inviteID'");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoUser);
	$points = $row['points'];
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$userID'");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoUser);
	
	// Ensure userID is leader of a Party
	$results = mysql_query("SELECT * FROM Parties WHERE leaderID='$userID'");
	if (mysql_num_rows($results)==0) exit("1".$error.' - '.$errorNotPartyLeader);
	$row = mysql_fetch_array($results);
	$partyname = $row['name'];
	$partyID = $row['partyID'];
	
	// Ensure partyID has room for another member
	$results = mysql_query("SELECT * FROM PartyMembers WHERE partyID='$partyID' AND accepted=1");
	if (mysql_num_rows($results)>5) exit("1".$error.' - '.$errorNoRoomInParty);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO PartyMembers (partyID, userID, dateInvite)
VALUES ('$partyID', '$inviteID', '$datetime')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorAlreadySentInvite);
	
	/*
	 * Handle Push Notes
	 */
	// You've been invited!
	$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM Users
WHERE userID='$inviteID'
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $row['userID'];
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"You have been invited to join '$partyname'!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Party", "partyID"=>(int)$partyID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
			include('pnQueue.php');
		}
	}
	
	mysql_close();
}
echo("0");
?>
