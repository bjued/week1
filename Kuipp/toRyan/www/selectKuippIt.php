<?
include('checkSession.php');

$xml = "";

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
	 * Following
	 */
	$query = <<<QUERY
SELECT u.userID,firstName,lastName,picture
FROM Users AS u,Followers AS f
WHERE f.followingID=$userID
AND u.userID=f.userID
ORDER BY CONCAT(firstName,' ',lastName)
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Followers>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "</Followers>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>