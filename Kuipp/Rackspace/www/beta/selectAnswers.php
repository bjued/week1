<?
include('checkSession.php');

$questionID	= $_POST['qid'];
$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
						
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	/*
	 * Question
	 */
	$query = <<<QUERY
SELECT q.*,c.category,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Questions AS q, Categories AS c, Users AS u
WHERE q.categoryID=c.categoryID
AND q.userID=u.userID
AND questionID=$questionID
QUERY;
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$xml .= "<Questions>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
	}
	$xml .= "</Questions>";
	
	/*
	 * Answers
	 */
	$query = <<<QUERY
SELECT a.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Answers AS a,Users AS u
WHERE a.questionID=$questionID
AND a.userID=u.userID
ORDER BY CAST(a.numLike AS SIGNED)-CAST(a.numDislike AS SIGNED) DESC, a.numLike+a.numDislike DESC, answerID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Answers>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Answers>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>