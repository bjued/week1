<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$pic		= $_POST['url'];
$pcx		= $_POST['pcx'];
$pcy		= $_POST['pcy'];
$pcz		= $_POST['pcz'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
		
	/*
	 * Update corresponding tables
	 */
	if ($pic=="") {
		// Update picture origins for userID
		if (!mysql_query("UPDATE Users SET picX='$pcx',picY='$pcy',picZ='$pcz' WHERE userID='$userID'")) exit("1".$error.' - '.$errorServerFailure);
	} else {
		// Update picture url and origins for userID
		if (!mysql_query("UPDATE Users SET picture='$pic',picX='$pcx',picY='$pcy',picZ='$pcz' WHERE userID='$userID'")) exit("1".$error.' - '.$errorServerFailure);
	}

	mysql_close();
}
echo("0");
?>
