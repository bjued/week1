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
	
	// Ensure userID is the leader of a party
	$results = mysql_query("SELECT * FROM Parties WHERE leaderID='$userID'");
	if (!$results) exit("1".$error.' - '.$errorNotLeader);
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
	if (mysql_num_rows($results)!=1) exit("1".$error.' - '.$errorNotMember);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Update database
	 */
	if (!mysql_query("UPDATE Parties SET leaderID='$iid' WHERE partyID='$partyID'")) exit("1".$error.' - '.$errorServerFailure);
	
	// Revert all Nominations to nil
	mysql_query("UPDATE PartyMembers SET nominateID=0 WHERE partyID='$partyID'");

	/*
	 * Handle Push Notes
	 */
	// You've been promoted!
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
			$pn2DArray['aps'] = array("alert"=>"You have been choosen to lead '$partyname' to victory!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Party", "partyID"=>(int)$partyID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
			include('pnQueue.php');
		}
	}
	
	// Someone's been promoted!
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
			$pn2DArray['aps'] = array("alert"=>"$pnName has been promoted to lead '$partyname' to victory!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Party", "partyID"=>(int)$partyID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
			include('pnQueue.php');
		}
	}
	
	mysql_close();
}
echo("0");
?>