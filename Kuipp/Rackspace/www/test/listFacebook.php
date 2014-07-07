<?
include('checkSession.php');

$at			= $_POST['at'];
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
	mysql_query("UPDATE Users SET facebookAT='$at' WHERE userID='$userID'");
	
	/*
	 * Get me/friends
	 */
	$file = file_get_contents("https://graph.facebook.com/me/friends?access_token=$at");
	 
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Output to xml
	 */
	$array = json_decode($file,TRUE);
	
	function facebookSort($a,$b) {
		$r = strcasecmp($a['name'],$b['name']);
		return $r==0?0:($r<0?-1:1);
	}
	
	uasort($array['data'],"facebookSort");
	
	foreach ($array['data'] as $person) {
		$results = mysql_query("SELECT userID,twitterID FROM Users WHERE facebookID='".$person['id']."'");
		$xml .= "<FB>";
		$xml .= "<facebookID>".$person['id']."</facebookID>";
		if ($row = mysql_fetch_assoc($results)) {
			$xml .= "<userID>".$row['userID']."</userID>";
			$xml .= "<twitterID>".$row['twitterID']."</twitterID>";
		}
		$xml .= "<name>".$person['name']."</name>";
		$xml .= "</FB>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>