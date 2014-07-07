<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$answerID	= $_POST['aid'];
$uid		= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$comment	= $_POST['com'];
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$location	= $_POST['loc'];

$comment	= addslashes(trim($comment));
$location	= addslashes(trim($location));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure answerID is in the Database
	$results = mysql_query("SELECT * FROM Answers WHERE answerID=$answerID");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoAnswer);
	$pnUserID = $row['userID'];
	$pnQuestionID = $row['questionID'];
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID=$userID");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoUser);
	$pnName = ucfirst($row['firstName']).' '.ucfirst(substr($row['lastName'],0,1)).'.';
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO Comments (answerID, userID, datetime, comment, latitude, longitude, location)
VALUES ($answerID, $userID, '$datetime', '$comment', '$latitude', '$longitude', '$location')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
		
	/*
	 * Update corresponding tables
	 */
	// Update numComments for answerID
	if (!mysql_query("UPDATE Answers SET numComments = numComments + 1 WHERE answerID=$answerID")) exit("1".$error.' - '.$errorServerFailure);	//answers failed to update

	/*
	 * PushSystem handling
	 */
	// CMyA
	$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM Users
WHERE CMyA=1
AND userID=$pnUserID
AND userID!=$userID
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $row['userID'];
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"$pnName has just made a comment on your answer!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Comment", "questionID"=>(int)$pnQuestionID, "answerID"=>(int)$answerID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID=$pnID");
			include('pnQueue.php');
		}
	}

	mysql_close();
}
echo("0");
?>
