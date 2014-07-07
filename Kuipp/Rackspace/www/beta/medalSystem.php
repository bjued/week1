<?
function giveMedal ($pmnTo,$pmnMedalInfo,$pmnDatetime) {
	$medalID = $pmnMedalInfo['medalID'];
	$medalNm = $pmnMedalInfo['name'];
	$results = mysql_query("SELECT * FROM Feats WHERE userID='$pmnTo' AND medalID='$medalID'");
	if (mysql_num_rows($results)==0) {
		mysql_query("INSERT INTO Feats (userID, medalID, datetime) VALUES ('$pmnTo', '$medalID', '$pmnDatetime')");
		
		/*
		 * Push Notification
		 */
		$results = mysql_query("SELECT userID,deviceToken,badges FROM Users WHERE userID='$pmnTo'");
		while ($row = mysql_fetch_assoc($results)) {
			$pnDevice = $row['deviceToken'];
			$pnID = $row['userID'];
			if (strlen($pnDevice)>0) {
				$pn2DArray['aps'] = array("alert"=>"You just got the $medalNm medal!", "badge"=>$row['badges']+1, "sound"=>"default");
				$pn2DArray['kuipp'] = array("head"=>"Medal", "rate"=>"1");
				mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$pnID'");
				include('pnQueue.php');
			}
		}
		
		/*
		 * Updates
		 */
		$query = "INSERT INTO Updates (userID, type, stringValue, datetime)"
		."VALUES ('$pmnTo', 5, '$medalNm', '$pmnDatetime')";
		mysql_query($query);
		
		/*
		 * Insert into Event Table
		 */
		$results = mysql_query("SELECT latitude,longitude,location,picture,picX,picY,picZ FROM Users WHERE userID='$pmnTo'");
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
('$pmnTo', 4, '$ulatitude', '$ulongitude', '$ulocation', '$pmnDatetime', '$medalNm', '', '$upicture', '$upicX', '$upicY', '$upicZ')
QUERY;
		mysql_query($query);
	}
}

function determineAdvisor ($pmnTo,$pmnDatetime) {
	$query = <<<QUERY
SELECT c.categoryID,c.category,COUNT(*) AS cnt
FROM Answers AS a
LEFT JOIN Questions AS q USING(questionID)
LEFT JOIN Categories AS c USING(categoryID)
WHERE a.userID='$pmnTo'
GROUP BY categoryID
QUERY;
	$results = mysql_query($query);
	$a1 = array();
	$a2 = array();
	while ($row = mysql_fetch_array($results)) {
		if ((int)$row['cnt']>=50) {
			array_push($a1,(int)$row['categoryID']);
			array_push($a2,$row['category']);
		}
	}
	$give = array_combine($a1,$a2);
	$query = <<<QUERY
SELECT c.categoryID,COUNT(*) AS cnt
FROM LikeDislike AS ld
LEFT JOIN Answers AS a USING(answerID)
LEFT JOIN Questions AS q USING(questionID)
LEFT JOIN Categories AS c USING(categoryID)
WHERE a.userID='$pmnTo'
AND ld.doLike=1
GROUP BY categoryID
QUERY;
	$likes = array_fill(0,11,0);
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$index = (int)$row['categoryID'];
		$likes[$index] = $row['cnt'];
	}
	$query = <<<QUERY
SELECT c.categoryID,COUNT(*) AS cnt
FROM LikeDislike AS ld
LEFT JOIN Answers AS a USING(answerID)
LEFT JOIN Questions AS q USING(questionID)
LEFT JOIN Categories AS c USING(categoryID)
WHERE a.userID='$pmnTo'
AND ld.doLike=0
GROUP BY categoryID
QUERY;
	$dislikes = array_fill(0,11,0);
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$index = (int)$row['categoryID'];
		$dislikes[$index] = $row['cnt'];
	}
	foreach (array_keys($give) as $ag) if ($likes[(int)$ag]-$dislikes[(int)$ag]>=100) {
		$mi = mysql_query("SELECT medalID,name FROM Medals WHERE name='".$give[(int)$ag]." Advisor'");
		giveMedal($pmnTo,$mi,$pmnDatetime);
	}
}

$medalSel = "SELECT medalID,name FROM Medals WHERE name=";
$medalInfo = "";

if ($type=="Question") {
	// Ask x questions at least x miles apart
	// Ask or Answer more than x questions in a day
	$qInDay = mysql_num_rows(mysql_query("SELECT * FROM QUESTIONS WHERE userID='$userID' and TIMESTAMPDIFF(DAY,datetime,NOW())<1"));
	$aInDay = mysql_num_rows(mysql_query("SELECT * FROM Answers WHERE userID='$userID' and TIMESTAMPDIFF(DAY,datetime,NOW())<1"));
	if ($qInDay+$aInDay>=20) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Focused'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($qInDay+$aInDay>=50) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Rapid'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($qInDay+$aInDay>=100) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Realtime'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
} else if ($type=="Answer") {
	// Get x responses for 5 consecutive questions
	$row = mysql_fetch_assoc(mysql_query("SELECT * FROM Questions WHERE questionID='$questionID'"));
	$pmnUserID = $row['userID'];
	$pmnRows = mysql_num_rows(mysql_query("SELECT * FROM Questions WHERE userID='$pmnUserID'"));
	if ($pmnRows>4) {
		$query = <<<QUERY
SELECT MIN(count) AS min
FROM (
	SELECT COUNT(*) AS count
	FROM (
		SELECT questionID
		FROM Questions
		WHERE userID='$pmnUserID'
		ORDER BY questionID DESC
		LIMIT 5
	) AS q, Answers AS a
	WHERE q.questionID=a.questionID
	GROUP BY a.questionID
) AS m
QUERY;
		$row = mysql_fetch_assoc(mysql_query($query));
		$minLast5 = $row['min'];
		if ($minLast5>=5) {
			$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Appeal'"));
			giveMedal($pmnUserID,$medalInfo,$datetime);
		}
		if ($minLast5>=10) {
			$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Public Figure'"));
			giveMedal($pmnUserID,$medalInfo,$datetime);
		}
		if ($minLast5>=15) {
			$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Debater'"));
			giveMedal($pmnUserID,$medalInfo,$datetime);
		}
	}
	// Respond to 25 questions within x minutes
	$query = "SELECT * FROM Answers WHERE userID='$userID' AND TIMESTAMPDIFF(MINUTE,datetime,NOW())<";
	if (mysql_num_rows(mysql_query($query."1440"))>=25) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Responder'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if (mysql_num_rows(mysql_query($query."60"))>=25) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Rapid Fire'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if (mysql_num_rows(mysql_query($query."15"))>=25) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Answer Fiend'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	// Respond to x questions within 2 minutes of posting
	$results = mysql_query("SELECT * FROM Coins WHERE userID='$userID' AND reason='Gave an answer in under 2 minutes'");
	$numResp = mysql_num_rows($results);
	if ($numResp>=1) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Cowboy'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($numResp>=5) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Volleyer'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($numResp>=10) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Slingshot'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($numResp>=15) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'911 Dispatcher'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	// Ask or Answer more than x questions in a day
	$qInDay = mysql_num_rows(mysql_query("SELECT * FROM Questions WHERE userID='$userID' and TIMESTAMPDIFF(DAY,datetime,NOW())<1"));
	$aInDay = mysql_num_rows(mysql_query("SELECT * FROM Answers WHERE userID='$userID' and TIMESTAMPDIFF(DAY,datetime,NOW())<1"));
	if ($qInDay+$aInDay>=20) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Focused'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($qInDay+$aInDay>=50) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Rapid'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	if ($qInDay+$aInDay>=100) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Realtime'"));
		giveMedal($userID,$medalInfo,$datetime);
	}
	// Advisor from answering 50
	determineAdvisor($userID,$datetime);
} else if ($type=="GainFollower") {
	// Obtain x Followers
	$results = mysql_query("SELECT * FROM Followers WHERE followingID='$followingID'");
	$numFols = mysql_num_rows($results);
	if ($numFols>=10) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Entourage'"));
		giveMedal($followingID,$medalInfo,$datetime);
	}
	if ($numFols>=50) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Crew'"));
		giveMedal($followingID,$medalInfo,$datetime);
	}
	if ($numFols>=100) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Crowd'"));
		giveMedal($followingID,$medalInfo,$datetime);
	}
	if ($numFols>=250) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Popular'"));
		giveMedal($followingID,$medalInfo,$datetime);
	}
	if ($numFols>=500) {
		$medalInfo = mysql_fetch_assoc(mysql_query($medalSel."'Celebrity'"));
		giveMedal($followingID,$medalInfo,$datetime);
	}
} else if ($type=="LikeDislike") {
	// Advisor from getting the 100th net like
	determineAdvisor($answerUID,$datetime);
}
?>
