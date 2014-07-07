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