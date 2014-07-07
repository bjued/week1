<?
include('checkSession.php');

$uid 		= $_POST['userID'];
$xml		= "";

if ($uid!="0") $userID = $uid;

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Party
	 */
	$query = <<<QUERY
SELECT Parties.*
FROM PartyMembers
LEFT JOIN Parties USING(partyID)
LEFT JOIN Users USING(userID)
WHERE userID='$userID'
AND accepted=1
QUERY;
	$results = mysql_query($query);
	if (mysql_num_rows($results)==1) {
		$row = mysql_fetch_array($results);
		
		$xml .= "<Party>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
		}
		$xml .= "</Party>";
		
		$partyID = $row['partyID'];
		
		$query = <<<QUERY
SELECT PartyMembers.*, firstName,lastName,picture,picX,picY,picZ,Users.level,class,Users.points
FROM PartyMembers
LEFT JOIN Parties USING(partyID)
LEFT JOIN Users USING(userID)
WHERE partyID='$partyID'
AND accepted=1;
QUERY;
		$results = mysql_query($query);
		while ($row=mysql_fetch_array($results)) {
			$xml .= "<User>";
			for ($i=0;$i<mysql_num_fields($results);$i++) {
				$fn = mysql_field_name($results,$i);
				$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
			}
			$xml .= "</User>";
		}
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>