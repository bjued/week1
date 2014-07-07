<?
$userID		= $_GET['userID'];

$userID = addslashes($userID);

include('/var/www/profilepictures/dbconnect.inc.php');
mysql_query("UPDATE Users SET active=0 WHERE SHA1(email)='$userID'");
mysql_close();

include('/var/www/profilepictures/dbconnect-stealth.inc.php');
mysql_query("UPDATE MailingList SET subscribed=1 WHERE SHA1(email)='$userID'");
mysql_close();

echo "You have been successfully unsubscribed!";
?>