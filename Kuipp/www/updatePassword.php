<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID	= $uid;
$oldpwd		= $_POST['pwd'];
$newpwd		= $_POST['npw'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is in the Database
	$results = mysql_query("SELECT userID FROM Users WHERE userID='$userID'");
	if (mysql_num_rows($results)!=1) exit("1".$error.' - '.$errorNoUser);
	$row = mysql_fetch_assoc($results);
	$id = $row['userID'];
	// Ensure password matches
	if (mysql_num_rows(mysql_query("SELECT userID FROM Passwords WHERE userID=$id AND phrase=UNHEX(SHA1('$oldpwd'))"))!=1) exit("1".$error.' - '.$errorWrongPassword);
		
	/*
	 * Update password
	 */
	// Update numQuestions for categoryID
	if (!mysql_query("UPDATE Passwords SET phrase=UNHEX(SHA1('$newpwd')) WHERE userID=$id")) exit("1".$error.' - '.$errorServerFailure);

	mysql_close();
}
echo("0".$xmlstring);
?>
