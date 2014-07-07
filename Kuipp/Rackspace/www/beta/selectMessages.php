<?
include('checkSession.php');

$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * ToUser
	 */
	$query = <<<QUERY
SELECT m.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT msg.*,c.subject,c.questionID
	FROM Messages AS msg
	JOIN Chains AS c USING(chainID)
	) AS m,Users AS u
WHERE m.toUser=$userID
AND m.fromUser=u.userID
ORDER BY messageID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<To>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</To>";
	}
	
	/*
	 * FromUser
	 */
	$query = <<<QUERY
SELECT m.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT Messages.*,Chains.subject
	FROM Messages JOIN Chains USING(chainID)
	) AS m,Users AS u
WHERE m.fromUser=$userID
AND m.toUser=u.userID
ORDER BY messageID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<From>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</From>";
	}
	
	/*
	 * DirectedTo
	 */
	$query = <<<QUERY
SELECT dq.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT q.*,d.userID AS duserID
	FROM DirectedQuestions AS d
	JOIN Questions AS q USING(questionID)
	) AS dq,Users AS u
WHERE duserID=$userID
AND dq.userID=u.userID
ORDER BY questionID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<DirectedTo>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</DirectedTo>";
	}
	
	/*
	 * DirectedFrom
	 */
	$query = <<<QUERY
SELECT dq.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT q.*,d.userID AS duserID
	FROM DirectedQuestions AS d
	JOIN Questions AS q USING(questionID)
	) AS dq,Users AS u
WHERE dq.userID=$userID
AND duserID=u.userID
ORDER BY questionID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<DirectedFrom>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</DirectedFrom>";
	}
	
	/*
	 * Followers
	 */
	$query = <<<QUERY
SELECT u.userID,firstName,lastName,u.picture,u.picX,u.picY,u.picZ
FROM Users AS u,Followers as f
WHERE f.followingID=$userID
AND u.userID=f.userID
ORDER BY CONCAT(firstName,' ',lastName) DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Followers>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
		}
		$xml .= "</Followers>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);

?>