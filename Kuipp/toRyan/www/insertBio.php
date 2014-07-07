<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$bio		= $_POST['bio'];

$bio		= addslashes(trim($bio));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
		
	/*
	 * Update corresponding tables
	 */
	// Update bio for userID
	if (!mysql_query("UPDATE Users SET bio='$bio' WHERE userID=$userID")) exit("1".$error.' - '.$errorServerFailure);

	mysql_close();
}
echo("0");
?>
