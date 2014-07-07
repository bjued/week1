<?
include('checkSession.php');
include('defaultValues.php');

$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Following
	 */
	$query = <<<QUERY
SELECT Parties.*,accepted
FROM PartyMembers
LEFT JOIN Parties USING(partyID)
WHERE userID='$userID'
ORDER BY name
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Following>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
		}
		$xml .= "</Following>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>