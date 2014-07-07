<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$answerID	= $_POST['aid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure answerID is in the Database
	$results = mysql_fetch_assoc(mysql_query("SELECT * FROM Answers WHERE answerID=$answerID"));
	if (!$results) exit("1".$error.' - '.$errorNoAnswer);

	// Ensure userID is NOT answering their own question
	if ($results['userID']==$userID) exit("1".$error.' - '.$errorFlagOwnAnswer);

	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$userID"))) exit("1".$error.' - '.$errorNoUser);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO AnswerFlags (answerID, userID, datetime) 
VALUES ($answerID, $userID, '$datetime')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorAlreadyFlaggedAnswer);
	
	mysql_close();
}
echo("0");
?>
