<?
include('errorCodes.inc.php');

$email		= $_POST['eml'];
$password	= $_POST['pwd'];
$deviceID	= $_GET['did'];

include('dbconnect.inc.php');
$query = <<<QUERY
SELECT userID,firstName,lastName,email,facebookAT,
	level,class,
	local,recent,forward,visible,
	AMyQ,AFQ,CMyA,FMe,MMe,QMyI,QAMe,QFQ,
	Rmnd
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
$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
$xml .= "<Login>";
for ($i=0;$i<mysql_num_fields($results);$i++) {
	$fn = mysql_field_name($results,$i);
	if ($fn=="facebookAT") $fn = $row['email'].$fn;
	$xml .= "<$fn>".$row[$i]."</$fn>";
}
$xml .= "<sessionID>".mysql_insert_id()."</sessionID>";
$xml .= "</Login>";
	
$id = mysql_insert_id();
mysql_query("DELETE FROM Sessions WHERE deviceID='$deviceID' AND sessionID!='$id'");

include('initialLogin.php');

$xml .= "</xmlstring>";
	
exit("0".$xml);	//Successful Login
?>