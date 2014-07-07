<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Kuipp Statistics</title>
</head>
<body>
<?php
$statsname = "kuipp";
$statspw = "kuipp2007";

if ($_POST['user']!=$statsname||$_POST['pass']!=$statspw) {
?>
<h1>Login</h1>

<form name="login" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<p><label for="user">Name: </label><input type="text" title="Enter username" name="user" /></p>
	<p><label for="pass">Pass: </label><input type="password" title="Enter password" name="pass" /></p>
	<p><label for="dys">Number of Days to list: </label><input type="text" name="dys" /></p>
	<p><label for="tic">Ticks to mark 100% (default:400): </label><input type="text" name="tic" /></p>
	<p><label for="med">More than x Users per Medals: </label><input type="text" name="med" /></p>
	<p><label for="loc">More than x Users per Location: </label><input type="text" name="loc" /></p>
	<p><input type="submit" name="Submit" value="Login" /></p>
</form>

<?php
} else {
	include("/var/www/dbconnect.inc.php");
	
	$days			= $_POST['dys'];
	$ticks			= $_POST['tic'];
	$minMedals		= $_POST['med'];
	$minLocations	= $_POST['loc'];
	
	if (!$days||$days=="") $days = 0;
	if (!$ticks||$ticks=="0") $ticks = 400;
	
	/*
	// * # Total questionsm
	// * # Total answers
	// * Average (# total questions / # total answers)
	// * # Total users
	// * # Sign-ups /day
	// * # Likes
	// * # Dislikes
	// * # Total likes /# total dislikes
	// * # Achieved medals
	// * # Avg medals / user
	// * # messages sent
	// * # private questions sent to followers
	// * Avg number of followers / users
	// * Avg number of following / user
	// * Avg # additional keywords used when asking question
	// * Ranking of the top main Keywords by number of questions
	// * Ranking of the top main Keywords by number of answers
	 * 
	 * Average response time to answer question
	 //* Over all main keywords
	 //* Over each specific category
	 */
	
	/*
	 * User Info
	 */
	$uCount = 0; //Number of user accounts on Kuipp
	$fbCount = 0; //Number of Facebook accounts linked to on Kuipp
	$twCount = 0; //Number of Twitter accounts linked to on Kuipp
	$location = array(); //Array of locations and their counts
	$perDay = array(); //Array of signups per day
	$active = array(); //Array of time since last active
	$results = mysql_query("SELECT facebookID,twitterID,location,since,datetime FROM Users");
	while ($row = mysql_fetch_array($results)) {
		$uCount++;
		if ($row['facebookID']!=0) $fbCount++;
		if ($row['twitterID']!=0) $twCount++;
		$date = explode(" ",$row['since']);
		$perDay[$date[0]] += 1;
		$location[$row['location']] += 1;
		$date = explode(" ",$row['datetime']);
		$active[$date[0]] += 1;
	}
	/*
	 * Question Info
	 */
	$qCount = 0; //Number of questions
	$pqCount = 0; //Number of private questions
	$results = mysql_query("SELECT isPublic FROM Questions");
	while ($row = mysql_fetch_array($results)) {
		$qCount++;
		if ($row['isPublic']==0) $pqCount++;
	}
	/*
	 * Answer Info
	 */
	$aCount = 0; //Number of answers
	$AdQs = array(); //Number of Questions with Answers
	$results = mysql_query("SELECT questionID FROM Answers");
	while ($row = mysql_fetch_array($results)) {
		$aCount++;
		$AdQs[$row['questionID']] = 1;
	}
	$AdQs = count($AdQs);
	
	/*
	 * Ratings Info
	 */
	$lCount = 0; //Number of Likes
	$dCount = 0; //Number of Dislikes
	$results = mysql_query("SELECT doLike FROM LikeDislike");
	while ($row = mysql_fetch_array($results)) {
		if ($row['doLike']==1) $lCount++;
		else $dCount++;
	}
	/*
	 * Medals Info
	 */
	$mCount = 0; //Number of Medals
	$medal = array(); //Array of medals earned
	$results = mysql_query("SELECT m.name FROM Feats AS f,Medals AS m WHERE f.medalID=m.medalID");
	while ($row = mysql_fetch_array($results)) {
		$mCount++;
		$medal[$row['name']] += 1;
	}
	/*
	 * Directed Question Info
	 */
	$drCount = 0; //Number of Directed Recipients
	$results = mysql_query("SELECT questionID FROM DirectedQuestions");
	while ($row = mysql_fetch_array($results)) {
		$drCount++;
	}
	
	$dqCount = 0; //Number of Directed Questions
	$results = mysql_query("SELECT questionID FROM DirectedQuestions GROUP BY questionID");
	while ($row = mysql_fetch_array($results)) {
		$dqCount++;
	}
	/*
	 * Chain Info
	 */
	$cCount = 0; //Number of Chains
	$results = mysql_query("SELECT chainID FROM Chains");
	while ($row = mysql_fetch_array($results)) {
		$cCount++;
	}
	/*
	 * Message Info
	 */
	$eCount = 0; //Number of Messages
	$results = mysql_query("SELECT messageID FROM Messages");
	while ($row = mysql_fetch_array($results)) {
		$eCount++;
	}
	/*
	 * Follower Info
	 */
	$fCount = 0; //Number of Follower-Following Pairs
	$results = mysql_query("SELECT followingID FROM Followers");
	while ($row = mysql_fetch_array($results)) {
		$fCount++;
	}
	/*
	 * Keywords Info
	 */
	$kCount = 0; //Number of Keywords Used
	$results = mysql_query("SELECT * FROM QuestionKeywords GROUP BY keywordID");
	while ($row = mysql_fetch_array($results)) {
		$kCount++;
	}
	/*
	 * Question Keywords Info
	 */
	$qkCount = 0; //Number of Question Keywords
	$results = mysql_query("SELECT * FROM QuestionKeywords");
	while ($row = mysql_fetch_array($results)) {
		$qkCount++;
	}
	/*
	 * Interests Info
	 */
	$iCount = 0; //Number of Interests Tracked
	$results = mysql_query("SELECT * FROM Interests");
	while ($row = mysql_fetch_array($results)) {
		$iCount++;
	}
	/*
	 * Main Keywords Ranking by # of Questions
	 */
	$qKeys = array(); //Array of Questions split into Primary Keywords
	$results = mysql_query("SELECT c.category FROM Questions AS q,Categories AS c WHERE q.categoryID=c.categoryID GROUP BY q.questionID");
	while ($row = mysql_fetch_array($results)) {
		$key = $row['category'];
		if ($qKeys[$key]) $qKeys[$key] = $qKeys[$key]+1;
		else $qKeys[$key] = 1;
	}
	/*
	 * Main Keywords Ranking by # of Answers
	 */
	$aKeys = array(); //Array of Answers split into Primary Keywords
	$results = mysql_query("SELECT c.category FROM Answers AS a,Questions AS q,Categories AS c WHERE a.questionID=q.questionID AND q.categoryID=c.categoryID");
	while ($row = mysql_fetch_array($results)) {
		$aKeys[$row['category']] += 1;
	}
	/*
	 * Response time to all Questions
	 */
	$resCount = 0; //Number of Questions with responses
	$response = array('2'=>0,'5'=>0,'15'=>0,'30'=>0,'60'=>0,'infinite'=>0); //Array of response broken into time slots
	$keyRespond = array(); //Array of avg response for each category
	$numRespond = array(); //Array of num response for each category
	$results = mysql_query("SELECT c.category,TIMESTAMPDIFF(SECOND,q.datetime,a.datetime) AS response FROM Answers AS a LEFT JOIN Questions AS q USING(questionID) LEFT JOIN Categories AS c USING(categoryID) GROUP BY q.questionID");
	while ($row = mysql_fetch_array($results)) {
		$resCount++;
		$responseTime = (int)$row['response'];
		if ($responseTime<2*60) $response['2'] += 1;
		elseif ($responseTime<5*60) $response['5'] += 1;
		elseif ($responseTime<15*60) $response['15'] += 1;
		elseif ($responseTime<30*60) $response['30'] += 1;
		elseif ($responseTime<60*60) $response['60'] += 1;
		else $response['infinite'] += 1;
		$keyRespond[$row['category']] += $responseTime;
		$numRespond[$row['category']] += 1;
	}
	foreach ($numRespond as $key => $val) {
		$keyRespond[$key] /= $val;
	}
	
	?>
	<table border=1px>
	<tr><td>Users</td><td>Kuipp Accounts</td><td>Facebook Links</td><td>Twitter Links</td></tr><tr><td></td>
	<?php echo("<td>$uCount</td><td>$fbCount of $uCount</td><td>$twCount of $uCount</td></tr>");?>
	<tr><td>Questions and Answers</td><td>Questions Asked</td><td>Questions Answered</td><td>Questions Unanswered</td><td>Answers Given</td><td>Answers per Question</td></tr><tr><td></td>
	<?php echo("<td>$qCount</td><td>$AdQs</td><td>".($qCount-$AdQs)."</td><td>$aCount</td><td>".$aCount/($qCount==0?1:$qCount)."</td></tr>");?>
	<tr><td>Ratings</td><td>Answers Liked</td><td>Likes per Dislike</td><td>Answers Disliked</td></tr><tr><td></td>
	<?php echo("<td>$lCount</td><td>".$lCount/($dCount==0?1:$dCount)."</td><td>$dCount</td></tr>");?>
	<tr><td>Medals</td><td>Medals Awarded</td><td>Medals per User</td></tr><tr><td></td>
	<?php echo("<td>$mCount</td><td>".$mCount/($uCount==0?1:$uCount)."</td></tr>");?>
	<tr><td>Directed Questions</td><td>Private Directed Questions</td><td>Public Directed Questions</td><td>Recipients of Directed Questions</td></tr><tr><td></td>
	<?php echo("<td>$pqCount</td><td>".($dqCount-$pqCount)."</td><td>$drCount</td></tr>");?>
	<tr><td>Messages and Chains</td><td>Messages Sent</td><td>Messages per Chain</td><td>Chains Created</td></tr><tr><td></td>
	<?php echo("<td>$eCount</td><td>".$eCount/($cCount==0?1:$cCount)."</td><td>$cCount</td></tr>");?>
	<tr><td>Followers and Following</td><td>Follow Pairs</td><td>Follow per User</td></tr><tr><td></td>
	<?php echo("<td>$fCount</td><td>".$fCount/($uCount==0?1:$uCount)."</td></tr>");?>
	<tr><td>Keywords and Interests</td><td>Unique Keywords</td><td>Interests Input</td><td>Interests per User</td></tr><tr><td></td>
	<?php echo("<td>$kCount of $qkCount</td><td>$iCount</td><td>".$iCount/($uCount==0?1:$uCount)."</td></tr>");?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Average Category Response Times</td><td>Hours</td><td>Mins</td><td>Secs</td></tr>
	<?php
	ksort($keyRespond);
	foreach ($keyRespond as $key => $val) {
		$hours = (int)($val/60/60);
		$minutes = (int)($val/60%60);
		$seconds = (int)($val%60);
		echo("<tr><td>$key</td><td>$hours</td><td>$minutes</td><td>$seconds</tr>");
	}
	?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Primary Keywords Count</td><td>Questions per Primary Keyword</td><td>Answers per Primary Keyword</td></tr>
	<?php
	ksort($qKeys);
	foreach ($qKeys as $key => $val) {
		$pkaCount = $aKeys[$key];
		echo("<tr><td>$key</td><td>".str_repeat('|',$val)." ($val)</td><td>".str_repeat('|',$pkaCount)." ($pkaCount)</td></tr>");
	}
	?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Last Active</td></tr>
	<?php
	krsort($active);
	foreach ($active as $key => $val) {
		echo("<tr><td>$key</td><td>".str_repeat('|',$val)." ($val)</td></tr>");
		$days--;
		if ($days==0) break;
	}
	?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Sign Ups per Day</td></tr>
	<?php
	krsort($perDay);
	foreach ($perDay as $key => $val) {
		echo("<tr><td>$key</td><td>".str_repeat('|',$val)." ($val)</td></tr>");
		$days--;
		if ($days==0) break;
	}
	?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Response Time Distribution</td></tr>
	<?php
	foreach ($response as $key => $val) {
		$resPer = (int)($val/$resCount*$ticks);
		echo("<tr><td>Respone in < $key minutes: </td><td>".str_repeat('|',$resPer)." ($val::".($resPer/$ticks*100)."%)</td></tr>");
	}
	?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Medals Achieved Distribution</td></tr>
	<?php
	arsort($medal);
	$other = 0; //Number of medals with few owners
	$null = 0;
	foreach ($medal as $key => $val) {
		$medPer = (int)($val/$mCount*$ticks);
		if ($key=="(null)") $null = $val;
		else if ($minMedals>=$val) $other += $val;
		else echo("<tr><td>$key</td><td>".str_repeat('|',$medPer)." ($val::".($medPer/$ticks*100)."%)</td></tr>");
	}
	$otherPer = (int)($other/$mCount*$ticks);
	if ($other>0) echo("<tr><td>Other</td><td>".str_repeat('|',$otherPer)." ($other::".($otherPer/$ticks*100)."%)</td></tr>");
	$nullPer = (int)($null/$mCount*$ticks);
	if ($null>0) echo("<tr><td>NULL</td><td>".str_repeat('|',$nullPer)." ($null::".($nullPer/$ticks*100)."%)</td></tr>");
	?>
	</table>
	
	<br>
	<table border=1px>
	<tr><td>Current Location Counts</td></tr>
	<?php
	arsort($location);
	$other = 0; //Amount of users in sparsely populated locations
	$null = 0;
	foreach ($location as $key => $val) {
		$locPer = (int)($val/$uCount*$ticks);
		if ($key=="(null)") $null = $val;
		else if ($minLocations>=$val) $other += $val;
		else echo("<tr><td>$key</td><td>".str_repeat('|',$locPer)." ($val::".($locPer/$ticks*100)."%)</td></tr>");
	}
	$otherPer = (int)($other/$uCount*$ticks);
	if ($other>0) echo("<tr><td>Other</td><td>".str_repeat('|',$otherPer)." ($other::".($otherPer/$ticks*100)."%)</td></tr>");
	$nullPer = (int)($null/$uCount*$ticks);
	if ($null>0) echo("<tr><td>NULL</td><td>".str_repeat('|',$nullPer)." ($null::".($nullPer/$ticks*100)."%)</td></tr>");
}
?>
</table>
</body>
</html>