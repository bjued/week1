<?
$dbUN= 'Server01';
$dbPW= 'kuipp2007sv';
$db= 'Beta';

mysql_connect('50.56.28.50',$dbUN,$dbPW);
@mysql_select_db($db) or die("Unable to select database");
?>