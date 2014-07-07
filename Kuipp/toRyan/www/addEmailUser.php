<?
$aName		= $_POST['aName'];
$aPassword	= $_POST['aPassword'];
$name		= $_POST['name'];
$password	= $_POST['password'];

mysql_connect(localhost,$aName,$aPassword);
@mysql_select_db("maildb") or die("Unable to select database");

if (!mysql_query("INSERT INTO users (id,name,maildir,crypt) VALUES ('$name@kuipp.com','$name','$name/',encrypt('$password'))")) exit("1 - ".mysql_error());

if (!mysql_query("INSERT INTO aliases (mail,destination) VALUES ('$name@kuipp.com','$name@kuipp.com')")) exit("2 - ".mysql_error());

mail("$name@kuipp.com","Welcome","Welcome to Kuipp Mail Server!","From: webmaster@kuipp.com");

exit ("Added User=$name with Email=$name@kuipp.com");
?>
