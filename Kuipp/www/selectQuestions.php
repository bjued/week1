<?
include('checkSession.php');

/*
 * Get Form Data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$xml		= "";

/*
 * Find $latitude and longitude constants
 */
$cosLat		= cos(deg2rad($latitude));
$sinLat		= sin(deg2rad($latitude));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Categories
	 */
	$query = <<<QUERY
SELECT categoryID,category
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
	}
	
	/*
	 * Interests
	 */
	$query = <<<QUERY
SELECT keyword
FROM Interests AS i, Keywords AS k
WHERE i.keywordID=k.keywordID
AND i.userID=$userID
ORDER BY keyword
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Keywords>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "</Keywords>";
	}
	
	/*
	 * Questions
	 */
	$query = <<<QUERY
SELECT q.*,c.category,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT Questions.*,'1' d,'0' f,'0' k,'0' l
	FROM Questions
	LEFT JOIN DirectedQuestions USING(questionID)
	WHERE DirectedQuestions.userID=$userID
	UNION ALL
	SELECT Questions.*,'0' d,'1' f,'0' k,'0' l
	From Questions
	Left JOIN Followers ON(Questions.userID=followingID)
	WHERE Followers.userID=$userID AND isPublic=1
	UNION ALL
	SELECT Questions.*,'0' d,'0' f,'1' k,'0' l
	FROM Questions
	LEFT JOIN QuestionKeywords USING(questionID)
	LEFT JOIN Interests USING(keywordID)
	WHERE Interests.userID=$userID AND isPublic=1
	UNION ALL
	SELECT Questions.*,'0' d,'0' f,'0' k,'1' l
	FROM Questions,Users
	WHERE Users.userID=$userID
	AND local>(ACOS($sinLat*SIN(RADIANS(Questions.latitude))+$cosLat*COS(RADIANS(Questions.latitude))*COS(RADIANS(Questions.longitude)-RADIANS($longitude)))*(3958.75587))
	AND isPublic=1
) AS q, Categories AS c,Users AS u
WHERE q.categoryID=c.categoryID
AND q.userID=u.userID
AND TIMESTAMPDIFF(HOUR,q.datetime,NOW())<recent
ORDER BY questionID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Questions>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Questions>";
	}
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>