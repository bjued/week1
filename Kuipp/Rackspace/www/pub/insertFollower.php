<?
include('errorCodes.inc.php');
include('checkSession.php');
/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$followingID= $_POST['fid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID=$userID");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoFollower);
	$pnName = ucfirst($row['firstName']).' '.ucfirst(substr($row['lastName'],0,1)).'.';
	$pnFollower = $userID;
	
	// Ensure followingID is NOT userID
	if ($followingID==$userID) exit("1".$error.' - '.$errorFollowSelf);
	
	// Ensure followingID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID=$followingID");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoFollowing);
	$pnFollowing = $followingID;
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query ="INSERT INTO Followers ".
			"(userID, followingID, datetime) ".
			"VALUES ".
			"('$userID', '$followingID', '$datetime')";
	if (!mysql_query($query)) exit("1".$error.' - '.$errorAlreadyFollowing);
		
	/*
	 * Update corresponding tables
	 */
	$query = <<<QUERY
INSERT INTO Updates
(userID, userID2, type, datetime)
VALUES
($userID, $followingID, 4, '$datetime')
QUERY;
	mysql_query($query);
		
	// Update numFollowing for userID
	$query = "UPDATE Users SET numFollowing = numFollowing + 1 WHERE userID=$userID";
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	// Update numFollowers for followingID
	$query = "UPDATE Users SET numFollowers = numFollowers + 1 WHERE userID=$followingID";
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	
	/*
	 * GameSystem handling
	 */
	$type  = "GainFollower";
	$userID = $followingID;
	include('gameSystem.php');
	include('medalSystem.php');
	
	/*
	 * PushSystem handling
	 */
	// FMe
	$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM Users
WHERE FMe=1
AND userID=$pnFollowing
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $pnFollowing;
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"$pnName is now following you!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"User", "userID"=>(int)$userID, "followerID"=>(int)$pnFollower);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID=$pnID");
			include('pnQueue.php');
		}
	}
	
	mysql_close();
}
echo("0");
?>
