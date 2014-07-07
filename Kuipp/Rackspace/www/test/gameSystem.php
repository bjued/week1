<?
include('points.inc.php');

function givePoints ($gsUserID,$gsPoints,$gsReason,$gsDatetime) {
	/*
	 * Insert Points
	 */
	mysql_query("INSERT INTO Points (userID, points, reason, datetime) VALUES ('$gsUserID', '$gsPoints', '$gsReason', '$gsDatetime')");
	
	/*
	 * Update User
	 */
	mysql_query("UPDATE Users SET points=points+'$gsPoints' WHERE userID='$gsUserID'");
}

function giveCoins ($gsUserID,$gsCoins,$gsReason,$gsDatetime) {
	/*
	 * Insert Points
	 */
	mysql_query("INSERT INTO Coins (userID, coins, reason, datetime) VALUES ('$gsUserID', '$gsCoins', '$gsReason', '$gsDatetime')");
	
	/*
	 * Update User
	 */
	mysql_query("UPDATE Users SET coins =coins+'$gsCoins' WHERE userID='$gsUserID'");
}

function checkLevel ($gsUserID,$gsDatetime) {
	/*
	 * Get Current Level
	 */
	$row = mysql_fetch_assoc(mysql_query("SELECT level,points,class FROM Users WHERE userID='$gsUserID'"));
	$pts = (int)$row['points'];
	$lvl = (int)$row['level'];
	$cls = $row['class'];
	
	/*
	 * Get New Level (can be the same level)
	 */
	$row = mysql_fetch_assoc(mysql_query("SELECT * FROM Level WHERE minPoints<='$pts' AND maxPoints>'$pts'"));
	$nlvl = ((int)$row['levelID'])-1;
	$ncls = $row['className'];
	$nNte = $row['note'];
	
	/*
	 * If New > Current, then Update and Push
	 */
	if ($nlvl>$lvl) {
		/*
		 * Insert Update
		 */
		mysql_query("INSERT INTO Updates (userID, type, intValue, stringValue, datetime) VALUES ('$gsUserID', 2, '$nlvl', '$ncls', '$gsDatetime')");
		
		/*
		 * Insert into Event Table
		 */
		$results = mysql_query("SELECT latitude,longitude,location,picture,picX,picY,picZ FROM Users WHERE userID='$gsUserID'");
		$row = mysql_fetch_array($results);
		$ulatitude = $row['latitude'];
		$ulongitude = $row['longitude'];
		$ulocation = $row['location'];
		$upicture = $row['picture'];
		$upicX = $row['picX'];
		$upicY = $row['picY'];
		$upicZ = $row['picZ'];
		
		$query = <<<QUERY
INSERT INTO Events
(userID, type, latitude, longitude, location, datetime, info1, info2, picture, picX, picY, picZ)
VALUES
('$gsUserID', 3, '$ulatitude', '$ulongitude', '$ulocation', '$gsDatetime', '$ncls', '', '$upicture', '$upicX', '$upicY', '$upicZ')
QUERY;
		mysql_query($query);
		
	$picture = $row['picture'];
	$picX = $row['picX'];
	$picY = $row['picY'];
	$picZ = $row['picZ'];
		
		/*
		 * Push Note
		 */
		$results = mysql_query("SELECT userID,deviceToken,badges FROM Users WHERE userID='$gsUserID'");
		while ($row = mysql_fetch_assoc($results)) {
			$pnDevice = $row['deviceToken'];
			if (strlen($pnDevice)>0) {
				$pn2DArray['aps'] = array("alert"=>$nNte, "badge"=>$row['badges']+1, "sound"=>"default");
				if ($nlvl==1) $pn2DArray['kuipp'] = array("head"=>"Profile");
				else $pn2DArray['kuipp'] = array("head"=>"Profile", "rate"=>"1");
				mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$gsUserID'");
				include('pnQueue.php');
			}
		}
	}
	
	/* 
	 * Update User Level, Class (regardless of change, ensures correctness)
	 */
	mysql_query("UPDATE Users SET level='$nlvl', class='$ncls' WHERE userID='$gsUserID'");
}

$points = 0;
$timePt = 0;
$distPt = 0;

$coins = 0;
$timeCn = 0;
$distCn = 0;

$reason = "";
$timeRn = "";
$distRn = "";

if ($type=="Question") {
	$points += $pointsQuestion;
	$reason = "Asked a question";
} else if ($type=="Answer") {
	$points += $pointsAnswer;
	$reason = "Gave an answer";
	
	$time = (strtotime($datetime) - strtotime($time))/60;
	$dist = $_POST['dist'];
	
	if ($time<2) {
		$timePt += $pointsAnswerUnder2;
		$timeCn += $coinsAnswerUnder2;
		$timeRn = "Gave an answer in under 2 minutes";
	} else if ($time<5) {
		$timePt += $pointsAnswerUnder5;
		$timeCn += $coinsAnswerUnder5;
		$timeRn = "Gave an answer in under 5 minutes";
	} else if ($time<15) {
		$timePt += $pointsAnswerUnder15;
		$timeCn += $coinsAnswerUnder15;
		$timeRn = "Gave an answer in under 15 minutes";
	} else if ($time<30) {
		$timePt += $pointsAnswerUnder30;
		$timeRn = "Gave an answer in under 30 minutes";
	} else if ($time<60) {
		$timePt += $pointsAnswerUnder60;
		$timeRn = "Gave an answer in under 60 minutes";
	}
	
	if ($dist<1) {
		$distPt += $pointsAnswerWithin1;
		$distCn += $coinsAnswerWithin1;
		$distRn = "Gave an answer within 1 mile";
	} else if ($dist<3) {
		$distPt += $pointsAnswerWithin3;
		$distRn = "Gave an answer within 3 miles";
	} else if ($dist<10) {
		$distPt += $pointsAnswerWithin10;
		$distRn = "Gave an answer within 10 miles";
	}
} else if ($type=="LikeDislike") {
	$points += $pointsLikeDislike;
	$reason = "Rated an answer";
	
	$time = (strtotime($datetime) - strtotime($time))/60;
	$dist = $_POST['dist'];
	
	if ($time<5) {
		$timePt += $pointsLikeDisUnder5;
		$timeCn += $coinsLikeDisUnder5;
		$timeRn = "Rated an answer in under 5 minutes";
	} else if ($time<15) {
		$timePt += $pointsLikeDisUnder15;
		$timeCn += $coinsLikeDisUnder15;
		$timeRn = "Rated an answer in under 15 minutes";
	} else if ($time<30) {
		$timePt += $pointsLikeDisUnder30;
		$timeRn = "Rated an answer in under 30 minutes";
	} else if ($time<60) {
		$timePt += $pointsLikeDisUnder60;
		$timeRn = "Rated an answer in under 60 minutes";
	}
	
	if ($dist<3) {
		$distPt += $pointsLikeDisWithin3;
		$distRn = "Rated an answer within 3 miles";
	} else if ($dist<10) {
		$distPt += $pointsLikeDisWithin10;
		$distRn = "Rated an answer within 10 miles";
	}
	
	givePoints($answerUID,$doLike=='numLike'?$pointsGainLike:$pointsGainDislike,($doLike=='numLike'?"Gained":"Lost")." approval",$datetime);
	checkLevel($answerUID,$datetime);
} else if ($type=="GainFollower") {
	$points += $pointsGainFollower;
	$coins	+= $coinsGainFollower;
	$reason = "Gained a follower";
} else if ($type=="LoseFollower") {
	$points += $pointsLoseFollower;
	$reason = "Lost a follower";
}

if ($points!=0) givePoints($userID,$points,$reason,$datetime);
if ($timePt!=0) givePoints($userID,$timePt,$timeRn,$datetime);
if ($distPt!=0) givePoints($userID,$distPt,$distRn,$datetime);

if ($coins!=0) giveCoins($userID,$coins,$reason,$datetime);
if ($timeCn!=0) giveCoins($userID,$timeCn,$timeRn,$datetime);
if ($distCn!=0) giveCoins($userID,$distCn,$distRn,$datetime);

checkLevel($userID,$datetime);

?>
