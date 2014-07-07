<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$chainID	= $_POST['cid'];
$toUser		= $_POST['tid'];
$fromUser	= $_POST['fid'];
if ($fromUser=="0") $fromUser = $userID;
$subject	= $_POST['sub'];
$message	= $_POST['msg'];
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$location	= $_POST['loc'];

$subject	= addslashes(trim($subject));
$message	= addslashes(trim($message));
$location	= addslashes(trim($location));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure toUser is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$toUser"))) exit("1".$error.' - '.$errorNoToUser);
	$pnUserID = $toUser;
	
	// Ensure fromUser != toUser
	if ($fromUser==$toUser) exit("1".$error.' - '.$errorMessageToSelf);

	// Ensure fromUser is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID=$fromUser");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoFromUser);
	$pnName = ucfirst($row['firstName']).' '.ucfirst(substr($row['lastName'],0,1)).'.';
	
	$datetime	= date('Y-m-d H:i:s');
	if ($chainID==0) {
		/*
		 * Find a new chainID
		 */
		mysql_query("INSERT INTO Chains (subject) VALUES ('$subject')");
		$chainID = mysql_insert_id();
	}
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO Messages (chainID, toUser, fromUser, datetime, message, latitude, longitude, location)
VALUES ($chainID, $toUser, $fromUser, '$datetime', '$message', '$latitude', '$longitude', '$location')
QUERY;
	if	(!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	$id = mysql_insert_id();
	
	/*
	 * PushSystem handling
	 */
	// MMe
	$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM Users
WHERE MMe=1
AND userID=$pnUserID
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $row['userID'];
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"$pnName has just sent you a message!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Message", "chainID"=>(int)$chainID, "messageID"=>$id);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID=$pnID");
			include('pnQueue.php');
		}
	}
	
	mysql_close();
}
echo("0");
?>
