<?
$dbUN= 'root';
$dbPW= 'kuipprackspace';
$db= 'Stealth';

mysql_connect('50.56.28.50',$dbUN,$dbPW);
@mysql_select_db($db) or die("Unable to select database");
?>