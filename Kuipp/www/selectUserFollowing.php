<?
include('checkSession.php');
include('defaultValues.php');

/*
 * Get Form Data
 */
$uid		= $_POST['uid'];
if ($uid!='0') $userID = $uid;
$lastID		= $_POST['lid'];
$counts		= $_POST['cnt'];
if ($lastID=="") {
	$lastID = "zzzzzzzzzz";
	$count = $defaultCount*$counts;
} else {
	$count = 1000;
}
$search		= $_POST['sch'];
$xml		= "";

/*
 * Verify Search, Add Slashes, Trim, Compile Where Clause
 */
if ($search!="") {
	$search = addslashes(addcslashes($search,'^$.*+?{}[]()'));
	$searchArray = explode(' ',$search);
	$search = "";
	foreach ($searchArray as $a) {
		$s = trim($a);
		$search .= <<<SEARCH
AND (u.firstName REGEXP '.*$s.*'
	OR u.lastName REGEXP '.*$s.*'
	OR u.email REGEXP '.*$s.*'
	)
SEARCH;
	}
}

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
SELECT u.userID,firstName,lastName,picture,picX,picY,picZ,class,level
FROM Followers AS f,Users AS u
WHERE f.userID=$userID
AND u.userID=followingID
AND CONCAT(firstName,' ',lastName)<='$lastID'
$search
ORDER BY CONCAT(firstName,' ',lastName)
LIMIT $count
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