<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$latitude	= $_POST['lat'];
$longitude	= $_POST['lon'];
$location	= $_POST['loc'];

$location	= addslashes(trim($location));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	if (!mysql_query("SELECT * FROM Users WHERE userID=$userID")) exit("1".$error.' - '.$errorNoUser);
	
	$datetime	= date('Y-m-d H:i:s');
	
	$set = "";
	if ($latitude!="") $set .= "latitude='$latitude',";
	if ($longitude!="") $set .= "longitude='$longitude',";
	if ($location!=""&&$location!="(null)") $set .= "location='$location',";
	$set .= "datetime='$datetime'";
	
	/*
	 * Insert into the database
	 */
	if (!mysql_query("UPDATE Users SET $set WHERE userID=$userID")) exit("1".$error.' - '.$errorServerFailure);

	mysql_close();
}
echo("0");
?>
