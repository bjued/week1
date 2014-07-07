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
AND (q.question REGEXP '.*$s.*'
	OR c.category REGEXP '.*$s.*'
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
	 * Asked
	 */
	$query = <<<QUERY
SELECT q.*,c.category,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ
FROM (
	SELECT a.*
	FROM Questions AS a,DirectedQuestions AS b
	WHERE a.userID=$you
	AND (a.isPublic=1
		OR (a.questionID=b.questionID
			AND b.userID=$me
			)
		OR a.userID=$me
		)
	GROUP BY a.questionID
	) AS q, Categories AS c, Users AS u
WHERE q.categoryID=c.categoryID
AND q.userID=u.userID
AND q.questionID>=$lastID
$search
ORDER BY questionID DESC
LIMIT $count
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