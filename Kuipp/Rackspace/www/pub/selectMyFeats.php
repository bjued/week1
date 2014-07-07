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
$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";

	/*
	 * Updates
	 */
	$query = <<<QUERY
SELECT u.updateID,u.userID,u.userID2,u.type,u.intValue,u.stringValue,u.datetime,a.firstName,a.lastName,a.picture,a.picX,a.picY,a.picZ,u2.firstName AS firstName2,u2.lastName AS lastName2,u2.picture AS picture2,u2.picX AS picX2,u2.picY AS picY2,u2.picZ AS picZ2
FROM Updates AS u
LEFT JOIN Users AS a USING(userID)
LEFT JOIN Users AS u2 ON(u.userID2=u2.userID)
LEFT JOIN Followers AS f ON(u.userID=f.followingID)
WHERE (f.userID='$userID'
	OR u.userID='$userID'
	)
AND u.updateID>='$lastID'
GROUP BY u.updateID
ORDER BY u.updateID DESC
LIMIT $count
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Updates>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Updates>";
	}

	/*
	 * Medals
	 */
	$query = <<<QUERY
SELECT m.*
FROM Medals AS m, Users AS u, Feats AS f
WHERE u.userID=f.userID
AND m.medalID=f.medalID
AND u.userID=$userID
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
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>