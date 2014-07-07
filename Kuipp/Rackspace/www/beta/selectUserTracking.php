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
	OR u.firstName REGEXP '.*$s.*'
	OR u.lastName REGEXP '.*$s.*'
	OR u.email REGEXP '.*$s.*'
	OR c.category REGEXP '.*%s.*'
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
	 * Tracking
	 */
	$query = <<<QUERY
SELECT q.*,c.category,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Questions AS q, Tracking AS t, Users AS u, Categories AS c
WHERE $userID=t.userID
AND t.questionID=q.questionID
AND c.categoryID=q.categoryID
AND q.userID=u.userID
AND q.questionID>=$lastID
$search
ORDER BY q.questionID DESC
LIMIT $count
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Tracking>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Tracking>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>