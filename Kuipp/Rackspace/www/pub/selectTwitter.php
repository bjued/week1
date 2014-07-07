<?
include('checkSession.php');

$twitterSN	= $_POST['at'];
$xml		= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * If twitterID = 0, insert twitterID and twitterSN from user/show
	 */
	$row = mysql_fetch_assoc(mysql_query("SELECT twitterID FROM Users WHERE userID=$userID"));
	if ($row[0]==0) {
		$file = file_get_contents("http://api.twitter.com/1/users/show.json?screen_name=$twitterSN");
		$array = json_decode($file,TRUE);
		$tid = $array['id'];
		mysql_query("UPDATE Users SET twitterID=$tid, twitterSN='$twitterSN' WHERE userID=$userID");
	}
	
	/*
	 * Get friends/ids
	 */
	$file = fopen("http://api.twitter.com/1/friends/ids.json?screen_name=$twitterSN",'r');
	
	$ids = fgets($file);
	$ids = substr($ids,1,-1);
	$ids = explode(',',$ids);
	
	fclose($file);
	
	/*
	 * Find User's Follower's IDs
	 */
	$following = array();
	$results = mysql_query("SELECT followingID FROM Followers WHERE userID=$userID");
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
SELECT userID,firstName,lastName,class,level,twitterID,picture,picX,picY,picZ
FROM Users
WHERE userID!=$userID
ORDER BY CONCAT(firstName,' ',lastName)
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		if (!in_array($row['userID'],$following)) {
			if ($row['twitterID']!="0"&&in_array($row['twitterID'],$ids)) {
				$xml .= "<Friends>";
				for ($i=0;$i<mysql_num_fields($results);$i++) {
					$fn = mysql_field_name($results,$i);
					$xml .= "<$fn>".$row[$i]."</$fn>";
				}
				$xml .= "</Friends>";
			}
		}
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>