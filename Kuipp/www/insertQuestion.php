<?
include('miscFunctions.inc.php');
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$categoryID	= $_POST['cid'];
$question	= $_POST['qtn'];
$keywords	= $_POST['key'];
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$location	= $_POST['loc'];
$isPublic	= $_POST['pub']=='on'?1:0;
$directTo	= $_POST['dir'];
$FBAT		= $_POST['pfb'];
$tagFB		= $_POST['fbids'];
$publishTW	= $_POST['ptw'];
$tagTW		= $_POST['twids'];
$publishYP	= $_POST['pyp'];
$xml		= "";

$urlquestion = trim($question);
$question	= addslashes(trim($question));
$location	= addslashes(trim($location));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT * FROM Users WHERE userID='$userID'");
	if (!$results) exit("1".$error.' - '.$errorNoUser);
	$row = mysql_fetch_assoc($results);
	$pnUserID = $row['userID'];
	$pnName		= ucwords($row['firstName'].' '.substr($row['lastName'],0,1).'.');
	$local		= $row['local'];
	$picture	= $row['picture'];
	$picX		= $row['picX'];
	$picY		= $row['picY'];
	$picZ		= $row['picZ'];
	$fbid		= $row['facebookID'];
	
	// Ensure categoryID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Categories WHERE categoryID='$categoryID'"))) exit("1".$error.' - '.$errorNoCategory);
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO Questions (userID, categoryID, datetime, question, latitude, longitude, location, isPublic) 
VALUES ('$userID', '$categoryID', '$datetime', '$question', '$latitude', '$longitude', '$location', '$isPublic')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	$questionID = mysql_insert_id();
	
	/*
	 * Post to Facebook Wall if we have permission
	 */
	if ($FBAT!="") {
		toFacebook(1,$FBAT,$fbid,$tagFB=="0"?"":$tagFB,$urlquestion,$questionID);
		mysql_query("UPDATE Users SET facebookAT='$FBAT' WHERE userID='$userID'");
	}
	
	/*
	 * Ask on Yelp if we have permission
	 */
	if ($publishYP!="0") {
		mysql_close();
		toYelp($questionID,$datetime,$urlquestion,$publishYP,$latitude,$longitude,$local);
		include('dbconnect.inc.php');
	}
	
	/*
	 * Insert into Event Table ONLY if isPublic
	 */
	if ($isPublic==1) {
		$query = <<<QUERY
INSERT INTO Events
(userID, type, latitude, longitude, location, datetime, info1, info2, picture, picX, picY, picZ)
VALUES
('$userID', 1, '$latitude', '$longitude', '$location', '$datetime', '$question', '', '$picture', '$picX', '$picY', '$picZ')
QUERY;
		mysql_query($query);
	}
	
	/*
	 * Update corresponding tables
	 */
	// Update numQuestions for categoryID
	if (!mysql_query("UPDATE Categories SET numQuestions=numQuestions+1 WHERE categoryID='$categoryID'")) exit("1".$error.' - '.$errorServerFailure);
	
	/*
	 * Directing Question
	 */
	if ($directTo!="") {
		$directees = explode(",",$directTo);
		foreach ($directees as $directee) {
			mysql_query("INSERT INTO DirectedQuestions (questionID, userID, datetime) VALUES ('$questionID', '$directee', '$datetime')");
		}
	}
	
	/*
	 * Keywording Question
	 */
	$pnKeywords = "";
	if ($keywords!="") {
		$keys = explode(',',$keywords);
		foreach ($keys as $k) {
			$k = strtolower(addslashes(trim($k)));
			 if ($row = mysql_fetch_assoc(mysql_query("SELECT keywordID FROM Keywords WHERE keyword='$k'"))) {
				$id = $row['keywordID'];
			} else {
				mysql_query("INSERT INTO Keywords (keyword) VALUES ('$k')");
				$id = mysql_insert_id();
			}
			$pnKeywords .= " OR i.keywordID='$id'";
			mysql_query("INSERT INTO QuestionKeywords (questionID, keywordID) VALUES ('$questionID', '$id')");
			mysql_query("UPDATE Questions SET keywords=CONCAT(keywords,', ','$k') WHERE questionID='$questionID'");
		}
	}
	
	// GameSystem handling
	$type  = "Question";
	include('gameSystem.php');
	/*if ($isPublic==1) {
		$results = mysql_query("SELECT * FROM PartyMembers WHERE userID='$userID' AND accepted=1");
		if ($row = mysql_fetch_array($results)) {
			$partyID = $row['partyID'];
			include('partySystem.php');
		}
	}*/
	include('medalSystem.php');
	
	/*
	 * PushSystem handling
	 */
	 if ($isPublic==1) {
		$pnNoIDs = "u.userID!='$pnUserID'";
		// QMyI
		if ($pnKeywords!="") {
			$pnKeywords = substr($pnKeywords,4);
			$query = <<<QUERY
SELECT u.userID,deviceToken,badges,keyword
FROM Users AS u, Interests AS i, Keywords AS k
WHERE AMyQ=1
AND u.userID=i.userID
AND ($pnNoIDs)
AND k.keywordID=i.keywordID
AND ($pnKeywords)
GROUP BY u.userID
QUERY;
			$results = mysql_query($query);
			while ($row = mysql_fetch_assoc($results)) {
				$pnDevice = $row['deviceToken'];
				$pnID = $row['userID'];
				$pnKey = $row['keyword'];
				if (strlen($pnDevice)>0) {
					$pn2DArray['aps'] = array("alert"=>"$pnName has just asked: '$urlquestion'", "badge"=>$row['badges']+1, "sound"=>"default");
					$pn2DArray['kuipp'] = array("head"=>"Question", "questionID"=>(int)$questionID);
					$pay = json_encode($pn2DArray);
					$payLength = strlen($pay);
					if ($payLength>255) {
						$pn2DArray['aps'] = array("alert"=>"$pnName has just asked: '".substr($urlquestion,0,255-$payLength-3)."...'", "badge"=>$row['badges']+1, "sound"=>"default");
				}
					mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
					$pnNoIDs .= " AND u.userID!='$pnID'";
					include('pnQueue.php');
				}
			}
		}
		// QAMe
		/*
		 * Find $latitude and longitude constants
		 */
		$cosLat		= cos(deg2rad($latitude));
		$sinLat		= sin(deg2rad($latitude));
		$query = <<<QUERY
SELECT u.userID,deviceToken,badges
FROM Users AS u
WHERE QAMe=1
AND ($pnNoIDs)
AND u.local>(ACOS($sinLat*SIN(RADIANS(u.latitude))+$cosLat*COS(RADIANS(u.latitude))*COS(RADIANS(u.longitude)-RADIANS('$longitude')))*(3958.75587))
QUERY;
		$results = mysql_query($query);
		while ($row = mysql_fetch_assoc($results)) {
			$pnDevice = $row['deviceToken'];
			$pnID = $row['userID'];
			if (strlen($pnDevice)>0) {
				$pn2DArray['aps'] = array("alert"=>"$pnName has just asked: '$urlquestion'", "badge"=>$row['badges']+1, "sound"=>"default");
				$pn2DArray['kuipp'] = array("head"=>"Question", "questionID"=>(int)$questionID);
				$pay = json_encode($pn2DArray);
				$payLength = strlen($pay);
				if ($payLength>255) {
					$pn2DArray['aps'] = array("alert"=>"$pnName has just asked: '".substr($urlquestion,0,255-$payLength-3)."...'", "badge"=>$row['badges']+1, "sound"=>"default");
				}
				mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
				$pnNoIDs .= " AND u.userID!='$pnID'";
				include('pnQueue.php');
			}
		}
	}
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
		
	/*
	 * Return the New Question ID
	 */
	$xml .= "<Questions><questionID>$questionID</questionID></Questions>";
	$xml .= "</xmlstring>";

	mysql_close();
}
echo("0".$xml);
?>
