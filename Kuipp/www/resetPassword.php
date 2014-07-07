<?
$rid		= $_GET['token'];

include('dbconnect.inc.php');

// ensure there is a password request in the database with this id
$result = mysql_query("SELECT * FROM ForgotPasswords WHERE forgotID='$rid'");
if (!$result) exit(mysql_error());
$row = mysql_fetch_assoc($result);

$newPass = "";
for ($i=0;$i<8;$i++) {
	$n = rand(0,25);
	$newPass = chr(97+$n).$newPass;
}

$query = "UPDATE Passwords AS p,Users AS u SET phrase=UNHEX(SHA1('$newPass')) WHERE p.userID=u.userID AND email='".$row['email']."'";
if (!mysql_query($query)) exit(mysql_error());

mail($row['email'],"Response from Kuipp","Your password has been reset to = $newPass","From: Kuipp Team <team@kuipp.com>","-f team@kuipp.com");

mysql_query("DELETE FROM ForgotPasswords WHERE forgotID='$rid'");

exit ("Your password has been reset and sent to your email!");
?>
