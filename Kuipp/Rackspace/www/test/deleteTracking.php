<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Get Form Data
 */
$questionID	= $_POST['qid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure userID is tracking questionID
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Tracking WHERE userID=$userID AND questionID=$questionID"))) exit("1".$error.' - '.$errorNoTracking);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Delete from the database
	 */
	$query = <<<QUERY
DELETE FROM Tracking
WHERE userID=$userID
AND questionID=$questionID
LIMIT 1
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);

	mysql_close();
}
echo("0");
?>