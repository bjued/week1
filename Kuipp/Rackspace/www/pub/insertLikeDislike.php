<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$answerID	= $_POST['aid'];
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$location	= $_POST['loc'];
$doLike		= $_POST['doL']=='on'?'1':'0';

$location	= addslashes(trim($location));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID='$userID'"))) exit("1".$error.' - '.$errorNoUser);
	
	// Ensure answerID is in the Database
	$result = mysql_fetch_assoc(mysql_query("SELECT * FROM Answers WHERE answerID='$answerID'"));
	if (!$result) exit("1".$error.' - '.$errorNoAnswer);

	// Ensure userID isn't Liking/Disliking their own answerID
	$answerUID = $result['userID'];
	if ($answerUID==$userID) exit("1".$error.' - '.$errorRateOwnAnswer);
	
	$datetime	= date('Y-m-d H:i:s');
	$time		= $result['datetime'];
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO LikeDislike
(userID, answerID, datetime, latitude, longitude, location, doLike)
VALUES
('$userID', '$answerID', '$datetime', '$latitude', '$longitude', '$location', '$doLike')
QUERY;
	if (mysql_query($query)) {
		
		/*
			Update corresponding tables
		*/
		// Update numLike/numDislike for answerID
		$doLike = $doLike=='1'?'numLike':'numDislike';
		if (!mysql_query("UPDATE Answers SET $doLike=$doLike+1 WHERE answerID='$answerID'")) {
			exit("1".$error.' - '.$errorServerFailure);				//answers failed to update
		}
		// GameSystem handling
		$type = "LikeDislike";
		include('gameSystem.php');
		if (doLike=='numLike') include('medalSystem.php');
	} else {
		exit("1".$error.' - '.$errorAlreadyRated);
	}
	mysql_close();
}
echo("0");
?>
