<?
include('checkSession.php');

$emails		= $_POST['eml'];
$firsts		= $_POST['frt'];
$lasts		= $_POST['lst'];
$xml	= "";

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create Arrays
	 */
	$eml = explode(',',$emails);
	$frt = explode(',',$firsts);
	$lst = explode(',',$lasts);
	$nms = array();
	for ($i=0;$i<count($frt);$i++) {
		$em = explode(' ',$eml[$i]);
		array_splice($eml,$i,1,array(array_flip($em)));
		$keys = array_keys($eml[$i]);
		foreach ($keys as $k) $eml[$i][$k] = $i;
		array_push($nms,strtolower($frt[$i].' '.$lst[$i]));
	}

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
SELECT userID,firstName,lastName,class,level,picture,picX,picY,picZ,email
FROM Users
WHERE userID!=$userID
ORDER BY firstName,lastName
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		if (!in_array($row['userID'],$following)) {
			// !following so check if you know them
			$b = false;
			foreach ($eml as $e) if (isset($e[$row['email']])) {
				$b = true;
				break;
			}
			if ($b) {
				$xml .= "<Friends>";
				for ($i=0;$i<mysql_num_fields($results);$i++) {
					$fn = mysql_field_name($results,$i);
					$xml .= "<$fn>".$row[$i]."</$fn>";
				}
				$xml .= "</Friends>";
				$xml .= "<Invite><i>".$e[$row['email']]."</i></Invite>";
			} elseif (in_array(strtolower($row['firstName'].' '.$row['lastName']),$nms)) {
				$xml .= "<Possible>";
				for ($i=0;$i<mysql_num_fields($results);$i++) {
					$fn = mysql_field_name($results,$i);
					$xml .= "<$fn>".$row[$i]."</$fn>";
				}
				$xml .= "</Possible>";
			}
		} else {
			// already following so remove
			$b = false;
			foreach ($eml as $e) if (isset($e[$row['email']])) {
				$b = true;
				break;
			}
			if ($b) {
				$xml .= "<Invite><i>".$e[$row['email']]."</i></Invite>";
			}
		}
	}
	
	
	$xml .= "</xmlstring>";
	mysql_close();
}
exit($xml);
?>