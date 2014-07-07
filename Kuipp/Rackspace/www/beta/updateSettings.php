<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['userID'];
if ($uid!="0") $userID	= $uid;
$keys		= $_POST['keys'];
$vals		= $_POST['vals'];
$xml		= "";

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	if (mysql_num_rows(mysql_query("SELECT * FROM Users WHERE userID=$userID"))!=1) exit("1".$error.' - '.$errorNoUser);	//userID not found
	
	/*
	 * Create update string
	 */
	$k = explode(',',$keys);
	$v = explode(',',$vals);
	$update = $k[0]."=".$v[0]." ";
	for ($i=1;$i<count($k);$i++) {
		$update .= ", ".$k[$i]."='".$v[$i]."' ";
	}
	
	/*
	 * Update Settings
	 */
	$query = <<<QUERY
UPDATE Users
SET $update
WHERE userID=$userID
QUERY;

	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);	//Settings failed to update

	/*
	 * Get Updated User
	 */
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * User
	 */
	$results = mysql_query("SELECT * FROM Users WHERE userID=$userID");
	$row = mysql_fetch_array($results);
	$xml .= "<Users>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		$xml .= "<$fn>".$row[$i]."</$fn>";
	}
	$xml .= "</Users>";
	
	$xml .= "</xmlstring>";
	mysql_close();
}
echo("0".$xml);
?>
