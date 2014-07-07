<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Get Form Data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$interest	= $_POST['int'];

$interest = strtolower(addslashes(trim($interest)));

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	
	// Ensure keyword is in the Database
	$row = mysql_fetch_assoc(mysql_query("SELECT keywordID FROM Keywords WHERE keyword='$interest'"));
	if (!$row) exit("1".$error.' - '.$errorNoInterest);
	$id = $row['keywordID'];

	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$userID"))) exit("1".$error.' - '.$errorNoUser);

	
	/*
	 * Delete from the database
	 */
	$query = <<<QUERY
DELETE FROM Interests
WHERE userID=$userID
AND keywordID=$id
LIMIT 1
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);

	mysql_close();
}
echo("0");
?>