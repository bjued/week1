<?
include('checkSession.php');

$userID		= $_POST['uid'];
$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";

	/*
	 * Asked
	 */
	$query = <<<QUERY
SELECT q.*,u.firstName,u.lastName,u.picture
FROM (
	SELECT Questions.*,c.category
	FROM Questions
	JOIN Categories AS c
	USING(categoryID)
	) AS q,Users AS u
WHERE q.userID=$userID
AND q.userID=u.userID
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

	/*
	 * Answered
	 */
	$query = <<<QUERY
SELECT a.*
FROM (
	SELECT Answers.*,u.firstName,u.lastName,u.picture
	FROM Answers
	JOIN Users AS u
	USING(userID)
	) AS a
WHERE a.userID=$userID
ORDER BY a.answerID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Answers>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".(($fn=="datetime"||$fn=="qdatetime")?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Answers>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>