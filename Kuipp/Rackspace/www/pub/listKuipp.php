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
SELECT u.userID,facebookID,twitterID,firstName,lastName,picture,picX,picY,picZ,class,level
FROM Followers AS f
LEFT JOIN Users AS u USING(userID)
WHERE f.followingID='$userID'
ORDER BY CONCAT(firstName,' ',lastName)
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