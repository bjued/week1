<?
$datetime	= date('Y-m-d H:i:s');

include('dbconnect.inc.php');
$results = mysql_query("SELECT userID FROM Users");

while ($row = mysql_fetch_assoc($results)) {
	
	$id = $row['userID'];
	
	/*
	 * Medals
	 */
	mysql_query("INSERT INTO Feats (userID, medalID, datetime) VALUES ($id, 35, '$datetime')");
		
	/*
	 * Updates
	 */
	mysql_query("INSERT INTO Updates (userID, type, stringValue, datetime) VALUES ($id, 5, 'Early Adopter', '$datetime')");
}
mysql_close();
echo("0");
?>
