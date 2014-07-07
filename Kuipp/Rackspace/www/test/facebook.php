<?
include('checkSession.php');

/*
 * Read in form data
 */
$facebookID	= $_POST['facebookID'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');

	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID='$userID'"))) exit("1");	//userID not found
		
	/*
	 * Update table
	 */
	// Update facebookID for userID
	if (!mysql_query("UPDATE Users SET facebookID = '$facebookID' WHERE userID='$userID'")) exit("2");	//users failed to update

	mysql_close();
}
echo("0");
?>
