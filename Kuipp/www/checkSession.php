<?
$sessionID	= $_GET['sid'];
$deviceID	= $_GET['did'];
$device		= $_GET['dev'];
$iOS		= $_GET['iOS'];
$version	= $_GET['ver'];

$userID		= "0";

// Major.Minor.Build
$currentVersion = "1.0.1.5000";

include('dbconnect.inc.php');

mysql_query("DELETE FROM Sessions WHERE deviceID='$deviceID' AND sessionID!=$sessionID");
$results = mysql_query("SELECT * FROM Sessions WHERE sessionID=$sessionID AND deviceID='$deviceID'");

if (!$results) {
	$sessionCode = 1;		//Device and Session has never been used
} else if ($row = mysql_fetch_assoc($results)) {
	$userID = $row['userID'];
	mysql_query("UPDATE Sessions SET device='$device', os='$iOS', version='$version' WHERE sessionID=$sessionID AND deviceID='$deviceID'");
	mysql_query("UPDATE Users SET badges=0 WHERE userID=$userID");
	$sessionCode = 0;		//old session will still work
	
	// Check whether version and build are uptodate
	$version = explode('.',$version);
	$current = explode('.',$currentVersion);
	for ($i=0;$i<count($current)-1;$i++) {
		if ($i==count($version)-1&&$current[$i]>0) {
			$sessionCode .="<versionUpdate>$currentVersion</versionUpdate>";
			break;
		} else if ($version[$i]>$current[$i]) {
			break; // More recent so get out
		} else if ($version[$i]<$current[$i]) {
			$sessionCode .="<versionUpdate>$currentVersion</versionUpdate>";
			break;
		}
	}
} else {
	$sessionCode = 1;
}

echo "$sessionCode";
mysql_close();

?>
