<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$partyname	= $_POST['pnm'];

$partyname	= addslashes(trim($partyname));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID=$userID");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoUser);
	$points = $row['points'];
	
	// Ensure userID is not already in a Party
	$results = mysql_query("SELECT * FROM PartyMembers WHERE userID='$userID' AND accepted=1");
	if (mysql_num_rows($results)>0) exit("1".$error.' - '.$errorAlreadyPartied);
	
	// Ensure partyname is not already taken by a Party
	$results = mysql_query("SELECT * FROM Parties WHERE name='$partyname'");
	if (mysql_num_rows($results)>0) exit("1".$sorry.' - '.$sorryPartyNameTaken);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO Parties (name, leaderID, datetime)
VALUES ('$partyname', '$userID', '$datetime')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	
	$partyID = mysql_insert_id();
	
	$query = <<<QUERY
INSERT INTO PartyMembers (partyID, userID, accepted, points, nominateID, dateInvite, dateAccept)
VALUES ('$partyID', '$userID', '1', 0, '$userID', '$datetime', '$datetime')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	
	mysql_close();
}
echo("0");
?>
