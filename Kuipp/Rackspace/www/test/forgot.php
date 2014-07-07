<?
include('errorCodes.inc.php');

$email		= $_POST['eml'];

include('dbconnect.inc.php');

// ensure there is an email in the database with this name
if (!mysql_query("SELECT * FROM Users WHERE email='$email'")) exit("1".$error.' - '.$errorNoEmail);

$query = <<<QUERY
INSERT INTO ForgotPasswords (email)
VALUES ('$email')
QUERY;
if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);

mail("$email","Auto Reply","http://kuipp.com/resetPassword.php?token=".mysql_insert_id(),"From: Kuipp Team <team@kuipp.com>","-f team@kuipp.com");

exit ("0");
?>
