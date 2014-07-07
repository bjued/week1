<?
include('checkSession.php');

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	$LoginAtBack = TRUE;
	
	include('initialLogin.php');
	
	$xml .= "</xmlstring>";
	mysql_close();
}
	
exit($xml);
?>