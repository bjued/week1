<?
include('checkSession.php');

$uid 		= $_POST['userID'];
$xml		= "";

$self = $userID;
if ($uid!="0") $userID = $uid;

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * User
	 */
	$query = <<<QUERY
SELECT userID,firstName,lastName,picture,picX,picY,picZ,bio,level,class,points
FROM Users
WHERE userID=$userID
QUERY;
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$xml .= "<Users>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
	}
	/*
	 * Following
	 * 0 = Not following
	 * 1 = Following
	 * 2 = Self
	 */
	$followNumber = $userID==$self?2:(mysql_num_rows(mysql_query("SELECT * FROM Followers WHERE userID=$self AND followingID=$userID"))>0?1:0);
	$xml .= "<followNumber>$followNumber</followNumber>";
	/*
	 * numInterests
	 */
	$query = <<<QUERY
SELECT COUNT(keywordID)
FROM Interests
WHERE userID=$userID
QUERY;
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<numInterests>".$row[0]."</numInterests>";
	/*
	 * Count Questions
	 */
	$query = <<<QUERY
SELECT COUNT(questionID)
FROM Questions
WHERE userID=$userID
QUERY;
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<qCount>".$row[0]."</qCount>";
	/*
	 * Count Answers
	 */
	$query = <<<QUERY
SELECT COUNT(answerID)
FROM Answers
WHERE userID=$userID
QUERY;
	$answers = $row[0];
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<aCount>".$row[0]."</aCount>";
	
	/*
	 * Count Following
	 */
	$query = <<<QUERY
SELECT COUNT(userID)
FROM Followers
WHERE userID=$userID
QUERY;
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<numFollowing>".$row[0]."</numFollowing>";
	/*
	 * Count Followers
	 */
	$query = <<<QUERY
SELECT COUNT(followingID)
FROM Followers
WHERE followingID=$userID
QUERY;
	$followers = $row[0];
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<numFollowers>".$row[0]."</numFollowers>";
	/*
	 * Count Likes
	 */
	$query = <<<QUERY
SELECT COUNT(doLike)
FROM Answers AS a, LikeDislike AS ld
WHERE a.answerID=ld.answerID
AND a.userID=$userID
AND doLike=1
QUERY;
	$likes = $row[0];
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<likeCount>".$row[0]."</likeCount>";
	/*
	 * Count Dislikes
	 */
	$query = <<<QUERY
SELECT COUNT(doLike)
FROM Answers AS a, LikeDislike AS ld
WHERE a.answerID=ld.answerID
AND a.userID=$userID
AND doLike=0
QUERY;
	$dislikes = $row[0];
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<dislikeCount>".$row[0]."</dislikeCount>";
	/*
	 * Count Medals
	 */
	$query = <<<QUERY
SELECT COUNT(userID)
FROM Feats
WHERE userID=$userID
QUERY;
	$row = mysql_fetch_array(mysql_query($query));
	$xml .= "<numMedals>".$row[0]."</numMedals>";
	/*
	 * Influence
	 */
	$query = <<<QUERY
SELECT COUNT(categoryID)
FROM Answers AS a,Questions AS q
WHERE a.questionID=q.questionID
AND a.userID=$userID
GROUP BY categoryID
QUERY;
	$results = mysql_query($query);
	$categories = 0;
	while ($row = mysql_fetch_array($results)) {
		$categories += max(25,(int)$row[0])/25/11;
	}
	$quality = .5*(($like+$dislike)-$dislike/($followers+$answers))/($like+$dislike);
	$reach = .25*$followers/500 + .25*$categories;
	$influence = $quality + $reach;
	$xml .= "<influence>$influence</influence>";
	$xml .= "</Users>";
	
	/*
	 * Asked
	 *//*
	$query = <<<QUERY
SELECT q.*,u.firstName,u.lastName,u.class
FROM (
	SELECT *
	FROM Questions
	JOIN Categories
	USING(categoryID)
) AS q,Users AS u
WHERE q.userID=u.userID
AND q.userID=$userID
ORDER BY questionID DESC
LIMIT 1
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Questions>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Questions>";
	}*/
	
	/*
	 * Answered
	 *//*
	$query = <<<QUERY
SELECT a.*,
	q.question AS qquestion,
	q.userID AS quserID,
	q.firstName AS qfirstName,
	q.lastName AS qlastName,
	q.class AS qclass,
	q.datetime AS qdatetime
FROM (
	SELECT Answers.*,u1.firstName,u1.lastName,u1.class
	FROM Answers
	JOIN Users AS u1
	USING(userID)
	) AS a,(
	SELECT Questions.*,u2.firstName,u2.lastName,u2.class
	FROM Questions
	JOIN Users AS u2
	USING(userID)
	) AS q
WHERE a.questionID=q.questionID
AND a.userID=$userID
ORDER BY answerID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Answers>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".(($fn=="datetime"||$fn=="qdatetime")?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Answers>";
	}*/
	
	/*
	 * Level
	 */
	$query = <<<QUERY
SELECT l.maxPoints
FROM Level AS l,Users AS u
WHERE u.userID=$userID
AND u.level=l.levelID - 1
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Level>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "</Level>";
	}
	
	/*
	 * Recent Medal
	 */
	$query = <<<QUERY
SELECT *
FROM Feats AS f, Medals AS m
WHERE f.userID=$userID
AND f.medalID=m.medalID
ORDER BY f.datetime DESC
LIMIT 1
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Medals>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "</Medals>";
	}
	
	/*
	 * Categories
	 *//*
	$query = <<<QUERY
SELECT category
FROM Categories
ORDER BY categoryID
QUERY;
	$results = mysql_query($query);			
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Categories>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "</Categories>";
	}*/
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>