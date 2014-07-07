<?
function updatePartyPoints ($psPartyID,$psPoints) {
	/*
	 * Update Party
	 */
	mysql_query("UPDATE Parties SET points=points+'$psPoints' WHERE partyID='$psPartyID'");
}

function givePartyPoints ($psUserID,$psPoints,$psReason,$psDatetime) {
	/*
	 * Insert Points
	 */
	mysql_query("INSERT INTO Points (userID, points, reason, datetime) VALUES ('$psUserID', '$psPoints', '$psReason', '$psDatetime')");
	
	/*
	 * Update User
	 */
	mysql_query("UPDATE Users SET points=points+'$psPoints' WHERE userID='$psUserID'");
}

function checkPartyLevel ($psUserID,$psDatetime) {
	/*
	 * Get Current Level
	 */
	$row = mysql_fetch_assoc(mysql_query("SELECT level,points,class FROM Users WHERE userID='$psUserID'"));
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
		mysql_query("INSERT INTO Updates (userID, type, intValue, stringValue, datetime) VALUES ('$psUserID', 2, '$nlvl', '$ncls', '$psDatetime')");
		
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
('$psUserID', 3, '$ulatitude', '$ulongitude', '$ulocation', '$psDatetime', '$ncls', '', '$upicture', '$upicX', '$upicY', '$upicZ')
QUERY;
		mysql_query($query);
		
		$picture = $row['picture'];
		$picX = $row['picX'];
		$picY = $row['picY'];
		$picZ = $row['picZ'];
		
		/*
		 * Push Note
		 */
		$results = mysql_query("SELECT userID,deviceToken,badges FROM Users WHERE userID='$psUserID'");
		while ($row = mysql_fetch_assoc($results)) {
			$pnDevice = $row['deviceToken'];
			if (strlen($pnDevice)>0) {
				$pn2DArray['aps'] = array("alert"=>$nNte, "badge"=>$row['badges']+1, "sound"=>"default");
				if ($nlvl==1) $pn2DArray['kuipp'] = array("head"=>"Profile");
				else $pn2DArray['kuipp'] = array("head"=>"Profile", "rate"=>"1");
				mysql_query("UPDATE Users SET badges=badges+1 WHERE userID='$psUserID'");
				include('pnQueue.php');
			}
		}
	}
	
	/* 
	 * Update User Level, Class (regardless of change, ensures correctness)
	 */
	mysql_query("UPDATE Users SET level='$nlvl', class='$ncls' WHERE userID='$psUserID'");
}

function contributePartyPoint ($psUserID,$psPoints) {
	/*
	 * Update PartyMembers
	 */
	mysql_query("UPDATE PartyMembers SET points=points+'$psPoints' WHERE userID='$psUserID'");
}

$points = 1;
$reason = "party".$type;

$results = mysql_query("SELECT * FROM PartyMembers WHERE partyID='$partyID' AND userID!='$userID' AND accepted=1");
while ($row=mysql_fetch_array($results)) {
	$psUserID = $row['userID'];
	givePartyPoints($psUserID,$points,$reason,$datetime);
	checkPartyLevel($psUserID,$datetime);
}
updatePartyPoints($partyID,$points);
contributePartyPoint($userID,$points);

?>
