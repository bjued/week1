<?
include('errorCodes.inc.php');

$email		= $_POST['eml'];
$password	= $_POST['pwd'];
$deviceID	= $_GET['did'];

include('dbconnect.inc.php');
$query = <<<QUERY
SELECT userID
FROM Users
LEFT JOIN Passwords USING(userID)
WHERE email='$email'
AND phrase=UNHEX(SHA1('$password'))
QUERY;
$results = mysql_query($query);
if (mysql_num_rows($results)==0) exit("1".$error.' - '.$errorNoEmailPassword);
$row = mysql_fetch_array($results);
$userID		= $row['userID'];
	
$datetime	= date('Y-m-d H:i:s');
				
$query = <<<QUERY
INSERT INTO Sessions (deviceID, userID, datetime)
VALUES ('$deviceID', '$userID', '$datetime')
QUERY;
if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
$sessionID = mysql_insert_id();

mysql_query("DELETE FROM Sessions WHERE deviceID='$deviceID' AND sessionID!='$sessionID'");

$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
$LoginAtBack = FALSE;

include('initialLogin.php');
	
$xml .= "</xmlstring>";

exit("0".$xml);	//Successful Login
?>