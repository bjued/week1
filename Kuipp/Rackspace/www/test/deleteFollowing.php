<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Get Form Data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$followingID= $_POST['fid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure followingID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$followingID"))) exit("1".$error.' - '.$errorNoUser);

	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$userID"))) exit("1".$error.' - '.$errorNoUser);
	
	// Ensure userID is following followingID
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Followers WHERE userID=$userID AND followingID=$followingID"))) exit("1".$error.' - '.$errorNoFollowingEntry);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Delete from the database
	 */
	$query = <<<QUERY
DELETE FROM Followers
WHERE userID=$userID
AND followingID=$followingID
LIMIT 1
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	
	/*
	 * GameSystem handling
	 */
	$type  = "LoseFollower";
	$userID = $followingID;
	include('gameSystem.php');

	mysql_close();
}
echo("0");
?>