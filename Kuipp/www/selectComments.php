<?
include('checkSession.php');

$answerID	= $_POST['aid'];
$xml		= "";

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	/*
	 * Answers
	 */
	$query = <<<QUERY
SELECT a.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Answers AS a, Users AS u
WHERE a.userID=u.userID
AND answerID=$answerID
QUERY;
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$xml .= "<Answers>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
	}
	$xml .= "</Answers>";
	
	/*
	 * Comments
	 */
	$query = <<<QUERY
SELECT c.*,u.lastName,u.firstName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Comments AS c,Users AS u
WHERE c.answerID=$answerID
AND c.userID=u.userID
ORDER BY commentID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Comments>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Comments>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>