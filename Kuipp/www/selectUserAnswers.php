<?
include('checkSession.php');
include('defaultValues.php');

/*
 * Get Form Data
 */
$lastID		= $_POST['lid'];
$counts		= $_POST['cnt'];
if ($lastID=="0") {
	$count = $defaultCount*$counts;
} else {
	$count = 1000;
}
$uid		= $_POST['uid'];
$me = $userID;
$you = $uid=="0"?$userID:$uid;
if ($uid!="0") $userID = $uid;
$search		= $_POST['sch'];
$xml		= "";

/*
 * Verify Search, Add Slashes, Trim, Compile Where Clause
 */
if ($search!="") {
	$search = addslashes(addslashes(addcslashes($search,'^$.*+?{}[]()')));
	$searchArray = explode(' ',$search);
	$search = "";
	foreach ($searchArray as $a) {
		$s = trim($a);
		$search .= <<<SEARCH
AND (a.answer REGEXP '.*$s.*'
	)
SEARCH;
	}
}

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";

	/*
	 * Answered
	 */
	$query = <<<QUERY
SELECT a.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ
FROM Answers AS a, Questions AS q, DirectedQuestions AS dq, Users AS u
WHERE a.userID=$you
AND a.questionID=q.questionID
AND (q.isPublic=1
	OR (q.questionID=dq.questionID
		AND dq.userID=$me
		)
	OR q.userID=$me
	)
AND a.userID=u.userID
AND a.answerID>=$lastID
$search
GROUP BY a.answerID
ORDER BY a.answerID DESC
LIMIT $count
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