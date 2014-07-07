<?
session_start();

$from			= $_SESSION['from'];
$pwd			= $_SESSION['pwd'];
$to				= $_SESSION['to'];
$subject		= $_SESSION['subject'];

$headers		= $_SESSION['headers'];

$top			= $_SESSION['top'];
$greet			= $_SESSION['greet'];
$break			= $_SESSION['break'];
$body			= $_SESSION['body'];
$sign			= $_SESSION['sign'];
$share			= $_SESSION['share'];
$unsubscribe1	= $_SESSION['unsubscribe1'];
$unsubscribe2	= $_SESSION['unsubscribe2'];
$unsubscribe3	= $_SESSION['unsubscribe3'];
$bottom			= $_SESSION['bottom'];

if ($pwd!="clrjbj0330") exit("wrong password");

// Determine to
$sendDict = array();
$to = trim($to);
if (substr($to,0,3)=="ALL") {
	if ($to == "ALL"||$to == "ALL Users") {
		include('dbconnect.inc.php');
		$results = mysql_query("SELECT userID,firstName,email FROM Users WHERE active=1");
		while ($row = mysql_fetch_array($results)) {
			if (!isset($sendDict[$row['email']])) {
				$sendDict[$row['email']] = array(	'email'	=>$row['email'],
													'name'	=>" ".$row['firstName'],
													'id'	=>$row['userID'],
													'type'	=>'1');
			}
		}
		
		mysql_close();
	}
	if ($to == "ALL"||$to == "ALL MailingList") {
		include('dbconnect-stealth.inc.php');
		$results = mysql_query("SELECT id,email FROM MailingList WHERE subscribed=0");
		while ($row = mysql_fetch_array($results)) {
			if (!isset($sendDict[$row['email']])) {
				$sendDict[$row['email']] = array(	'email'	=>$row['email'],
													'name'	=>'',
													'id'	=>$row['id'],
													'type'	=>'0');
			}
		}
		
		mysql_close();
	}
} else {
	include('dbconnect.inc.php');
	$toArray = explode(",",$to);
	foreach ($toArray as $rec) {
		$rec = trim($rec);
		$results = mysql_query("SELECT userID,firstName,email FROM Users WHERE email='$rec' AND active=1");
		if ($row=mysql_fetch_array($results)) {
			if (!isset($sendDict[$row['email']])) {
				$sendDict[$row['email']] = array(	'email'	=>$row['email'],
													'name'	=>" ".$row['firstName'],
													'id'	=>$row['userID'],
													'type'	=>'1');
			}
		}
	}
	mysql_close();
	include('dbconnect-stealth.inc.php');
	foreach($toArray as $rec) {
		$rec = trim($rec);
		if (!isset($sendDict[$rec])) {
			$results = mysql_query("SELECT id,email FROM MailingList WHERE email='$rec' AND subscribed=0");
			if ($row=mysql_fetch_array($results)) {
				$sendDict[$row['email']] = array(	'email'	=>$row['email'],
													'name'	=>'',
													'id'	=>$row['id'],
													'type'	=>'0');
			}
		}
	}
	mysql_query();
}

foreach ($sendDict as $rec) {
	$email	= $rec['email'];
	$name	= $rec['name'];
	$userID	= $rec['id'];
	$type	= $rec['type'];
	
	$b = $top.$greet.$name.$break.$body.$sign.$share.$unsubscribe1.$type.$unsubscribe2.$userID.$unsubscribe3.$bottom;
	
	mail("$email","$subject","$b","$headers","-f $from");
	echo "Sent to: $email<br>";
}

echo "from: $from<p>to: $to<p>subject: $subject<p>body: $b<p>";
?>