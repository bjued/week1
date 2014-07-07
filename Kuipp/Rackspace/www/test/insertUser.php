<?
include('errorCodes.inc.php');

/*
 * Read in form data
 */
$password	= $_POST['pwd'];
$firstName	= $_POST['fnm'];
$lastName	= $_POST['lnm'];
$email		= $_POST['wml'];
$facebookID	= $_POST['fbid'];
$at			= $_POST['at'];

$firstName	= trim(addslashes($firstName));
$lastName	= trim(addslashes($lastName));

/*
 * Insert form data into the database
 */
include('dbconnect.inc.php');
if (mysql_num_rows(mysql_query("SELECT * FROM Users WHERE email='$email'"))>0) exit("1".$error.' - '.$errorAlreadyEmail);
if ($facebookID!="0"&&mysql_num_rows(mysql_query("SELECT * FROM Users WHERE facebookID='$facebookID'"))>0) exit("1".$error.' - '.$errorAlreadyFacebook);

$datetime	= date('Y-m-d H:i:s');

$query = <<<QUERY
INSERT INTO Users (since, firstName, lastName, email, facebookID)
VALUES ('$datetime', '$firstName', '$lastName', '$email', '$facebookID')
QUERY;
if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);

$id = mysql_insert_id();

if ($facebookID!="0") {
	$query = <<<QUERY
UPDATE Users
SET picture=CONCAT('http://graph.facebook.com/',facebookID,'/picture?type=large'),picX=0,picY=0,picZ=1
WHERE userID='$id'
QUERY;
	if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
	if ($at) mysql_query("UPDATE Users SET facebookAT='$at' WHERE userID='$userID'");
}

$query = <<<QUERY
INSERT INTO Passwords (userID, phrase)
VALUES ($id, UNHEX(SHA1('$password')))
QUERY;
if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);

mysql_close();
include('dbconnect-beta.inc.php');
if ($row = mysql_fetch_array(mysql_query("SELECT email FROM Users WHERE email='$email'"))) {
	mysql_close();
	include('dbconnect.inc.php');
	/*
	 * Medals
	 */
	mysql_query("INSERT INTO Feats (userID, medalID, datetime) VALUES ($id, 35, '$datetime')");
	
	/*
	 * Updates
	 */
	mysql_query("INSERT INTO Updates (userID, type, stringValue, datetime) VALUES ($id, 5, 'Early Adopter', '$datetime')");
} else {
	mysql_close();
	include('dbconnect-stealth.inc.php');
	if ($row = mysql_fetch_array(mysql_query("SELECT refNum FROM MailingList WHERE email='$email'"))) {
		$referred = (int)$row['refNum'];
		if ($referred>2) {
			mysql_close();
			include('dbconnect.inc.php');
			/*
			 * Medals
			 */
			mysql_query("INSERT INTO Feats (userID, medalID, datetime) VALUES ($id, 35, '$datetime')");
			
			/*
			 * Updates
			 */
			mysql_query("INSERT INTO Updates (userID, type, stringValue, datetime) VALUES ($id, 5, 'Early Adopter', '$datetime')");
		}
	}
}
		
mysql_close();
echo("0");
?>
