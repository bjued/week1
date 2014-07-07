<?
include('checkSession.php');

$at			= $_POST['at'];
$patch		= $_POST['pch'];
$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * If facebookID = 0, insert facebookID from me
	 */
	$row = mysql_fetch_assoc(mysql_query("SELECT facebookID,facebookAT FROM Users WHERE userID='$userID'"));
	if ((int)$row['facebookID']==0) {
		$file = file_get_contents("https://graph.facebook.com/me?access_token=$at");
		$array = json_decode($file,TRUE);
		$fid = $array['id'];
		mysql_query("UPDATE Users SET facebookID='$fid',picture=CONCAT('http://graph.facebook.com/','$fid','/picture?type=large'),picX=0,picY=0,picZ=1 WHERE userID='$userID'");
	}
	if ($patch) mysql_query("UPDATE Users SET facebookAT='$at' WHERE userID='$userID'");
	
	/*
	 * Get me/friends
	 */
	$file = file_get_contents("https://graph.facebook.com/me/friends?access_token=$at");
	
	/*
	 * Parse into two Arrays (ids, names)
	 */
	$array = json_decode($file,TRUE);
	
	$ids = array();
	$nms = array();
	foreach ($array['data'] as $person) {
		array_push($ids,$person['id']);
		array_push($nms,(string)strtolower($person['name']));
	}

	/*
	 * Find User's Follower's IDs
	 */
	$following = array();
	$results = mysql_query("SELECT followingID FROM Followers WHERE userID='$userID'");
	while($row = mysql_fetch_row($results)) {
		array_push($following,$row[0]);
	}
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Friends & Possible
	 */
	$query = <<<QUERY
SELECT userID,firstName,lastName,class,level,facebookID,picture,picX,picY,picZ
FROM Users
WHERE userID!='$userID'
ORDER BY CONCAT(firstName,' ',lastName)
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		if (!in_array($row['userID'],$following)) {
			if ($row['facebookID']!="0"&&in_array($row['facebookID'],$ids)) {
				$xml .= "<Friends>";
				for ($i=0;$i<mysql_num_fields($results);$i++) {
					$fn = mysql_field_name($results,$i);
					$xml .= "<$fn>".$row[$i]."</$fn>";
				}
				$xml .= "</Friends>";
			} elseif (in_array(strtolower($row['firstName'].' '.$row['lastName']),$nms)) {
				$xml .= "<Possible>";
				for ($i=0;$i<mysql_num_fields($results);$i++) {
					$fn = mysql_field_name($results,$i);
					$xml .= "<$fn>".$row[$i]."</$fn>";
				}
				$xml .= "</Possible>";
			}
		}
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>