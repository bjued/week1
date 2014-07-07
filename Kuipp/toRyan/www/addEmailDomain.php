<?php
$aName		= $_POST['aName'];
$aPassword	= $_POST['aPassword'];
$domain		= $_POST['domain'];
$dest		= $_POST['dest'];

mysql_connect(localhost,$aName,$aPassword);
@mysql_select_db("maildb") or die("Unable to select database");

if (!mysql_query("INSERT INTO domains (domain) VALUES ('$domain')")) exit("1 - ".mysql_error());

if (!mysql_query("INSERT INTO aliases (mail,destination) VALUES ('@$domain','$dest'), ('postmaster@$domain','$dest'), ('abuse@$domain','$dest')")) exit("2 - ".mysql_error());

exit ("Added Domain=$domain with Destination=$dest");
?>
