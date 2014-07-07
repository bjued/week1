<?
include('miscFunctions.inc.php');
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$questionID	= $_POST['qid'];
$uid		= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$answer		= $_POST['ans'];
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$location	= $_POST['loc'];
$FBAT		= $_POST['pfb'];
$publishTW	= $_POST['ptw'];

$urlanswer	= trim($answer);
$answer		= addslashes(trim($answer));
$location	= addslashes(trim($location));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure questionID is in the Database
	$results = mysql_query("SELECT * FROM Questions WHERE questionID='$questionID'");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoQuestion);
	$pnUserID = $row['userID'];
	$question = $row['question'];
	$isPublic = (int)$row['isPublic'];
	$urlquestion = $question;
	$question = addslashes(trim($question));

	// Ensure userID is NOT answering their own question
	if ($row['userID']==$userID) exit("1".$error.' - '.$errorAnswerOwnQuestion);

	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$userID'");
	$row = mysql_fetch_assoc($results);
	if (!$row) exit("1".$error.' - '.$errorNoUser);
	$pnName		= ucwords($row['firstName'].' '.substr($row['lastName'],0,1).'.');
	$picture	= $row['picture'];
	$picX		= $row['picX'];
	$picY		= $row['picY'];
	$picZ		= $row['picZ'];
	$fbid		= $row['facebookID'];
	$at			= $row['facebookAT'];
	
	$datetime	= date('Y-m-d H:i:s');
	$time 		= $results['datetime'];
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO Answers (questionID, userID, datetime, answer, latitude, longitude, location)
VALUES ('$questionID', '$userID', '$datetime', '$answer', '$latitude', '$longitude', '$location')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorAlreadyAnswered);
	
	/*
	 * Post to Facebook Wall if we have permission
	 */
	$fbmsg = $urlanswer."\n\nIn Response to: ".$urlquestion;
	if ($FBAT!="") {
		toFacebook(2,$FBAT,$fbid,"",$fbmsg,$questionID);
		mysql_query("UPDATE Users SET facebookAT='$FBAT' WHERE userID='$userID'");
	}
	
	/*
	 * Insert into Event Table ONLY if q.isPublic
	 */
	if ($isPublic==1) {
		$query = <<<QUERY
INSERT INTO Events
(userID, type, latitude, longitude, location, datetime, info1, info2, picture, picX, picY, picZ)
VALUES
('$userID', 2, '$latitude', '$longitude', '$location', '$datetime', '$question', '$answer', '$picture', '$picX', '$picY', '$picZ')
QUERY;
		mysql_query($query);
	}
	
	/*
	 * Update corresponding tables
	 */
	// Update numAnswers for questionID
	if (!mysql_query("UPDATE Questions SET numAnswers = numAnswers + 1 WHERE questionID='$questionID'")) exit("1".$error.' - '.$errorServerFailure);
	
	/*
	 * GameSystem handling
	 */
	$type  = "Answer";
	include('gameSystem.php');
	/*if ($isPublic==1) {
		$results = mysql_query("SELECT * FROM PartyMembers WHERE userID='$userID' AND accepted=1");
		if ($row = mysql_fetch_array($results)) {
			$partyID = $row['partyID'];
			$results = mysql_query("SELECT * FROM PartyMembers WHERE userID='$pnUserID' AND accepted=1");
			$row = mysql_fetch_array($results);
			$askerPartyID = $row['partyID'];
			if ($partyID!=$askerPartyID) include('partySystem.php');
		}
	}*/
	include('medalSystem.php');

	/*
	 * PushSystem handling
	 */
	// AMyQ
	$query = <<<QUERY
SELECT userID,deviceToken,badges
FROM Users
WHERE AMyQ=1
AND userID='$pnUserID'
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $row['userID'];
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"$pnName has just answered your question!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Answer", "questionID"=>(int)$questionID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
			include('pnQueue.php');
		}
	}
	// AFQ
	$query = <<<QUERY
SELECT u.userID,deviceToken,badges
FROM Users AS u,Tracking AS t
WHERE AFQ=1
AND questionID='$questionID'
AND u.userID = t.userID
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_assoc($results)) {
		$pnDevice = $row['deviceToken'];
		$pnID = $row['userID'];
		if (strlen($pnDevice)>0) {
			$pn2DArray['aps'] = array("alert"=>"$pnName has just answered a question you're tracking!", "badge"=>$row['badges']+1, "sound"=>"default");
			$pn2DArray['kuipp'] = array("head"=>"Answer", "questionID"=>(int)$questionID);
			mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
			include('pnQueue.php');
		}
	}

	mysql_close();
}
echo("0");
?>
