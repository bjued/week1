<?
include('checkSession.php');
include('defaultValues.php');

/*
 * Get Form Data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$lastID		= $_POST['lid'];
$counts		= $_POST['cnt'];
if ($lastID=="0") {
	$count = $defaultCount*$counts;
} else {
	$count = 1000;
}
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
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
	OR c.category REGEXP '.*$s.*'
	)
SEARCH;
	}
}

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
	 * User Local
	 */
	$row = mysql_fetch_array(mysql_query("SELECT local FROM Users WHERE userID='$userID'"));
	$local = $row['local'];
	
	/*
	 * Questions
	 */
	$query = <<<QUERY
SELECT DISTINCT q.*,c.category,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Questions AS q
LEFT JOIN DirectedQuestions AS dq USING(questionID)
LEFT JOIN Categories AS c USING(categoryID)
LEFT JOIN Users AS u ON(q.userID=u.userID)
WHERE (dq.userID='$userID'
	OR isPublic=1
	)
AND $local>(ACOS($sinLat*SIN(RADIANS(q.latitude))+$cosLat*COS(RADIANS(q.latitude))*COS(RADIANS(q.longitude)-RADIANS($longitude)))*(3958.75587))
AND q.questionID>='$lastID'
$search
ORDER BY q.questionID DESC
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