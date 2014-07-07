<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$questionID	= $_POST['qid'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	
	// Ensure questionID is in the Database
	$results = mysql_fetch_assoc(mysql_query("SELECT * FROM Questions WHERE questionID=$questionID"));
	if (!$results) exit("1".$error.' - '.$errorNoQuestion);

	// Ensure userID is NOT answering their own question
	if ($results['userID']==$userID) exit("1".$error.' - '.$errorFlagOwnQuestion);

	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$userID"))) exit("1".$error.' - '.$errorNoUser);
	
	$datetime	= date('Y-m-d H:i:s');
	
	/*
	 * Insert into the database
	 */
	$query = <<<QUERY
INSERT INTO QuestionFlags (questionID, userID, datetime) 
VALUES ($questionID, $userID, '$datetime')
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorAlreadyFlaggedQuestion);

	mysql_close();
}
echo("0");
?>
