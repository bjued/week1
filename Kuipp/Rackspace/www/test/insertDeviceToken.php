<?
include('checkSession.php');

/*
 * Read in form data
 */
$uid			= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$deviceToken	= $_POST['dvt'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	/*
	 * Update user table
	 */
	// Update the device Token
	mysql_query("UPDATE Users SET deviceToken='$deviceToken',badges=0 WHERE userID=$userID");
	
	mysql_close();
}

echo("0");
?>
