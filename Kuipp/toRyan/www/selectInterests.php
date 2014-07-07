<?
include('checkSession.php');

/*
 * Get Form Data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$xml		= "";

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Interests
	 */
	$query = <<<QUERY
SELECT keyword
FROM Interests AS i, Keywords AS k
WHERE i.keywordID=k.keywordID
AND i.userID=$userID
ORDER BY keyword
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Keywords>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "</Keywords>";
	}
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>