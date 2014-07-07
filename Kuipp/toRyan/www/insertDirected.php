<?
$file = "xml/newDirected.xml";

/*
	Read in form data and put into an xml on the filesystem
*/
$questionID	= $_POST['questionID'];
$userID		= $_POST['userID'];

/*
	Create an XML string
*/
$filestring =	"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n".
				"<newDirected>\n".
				"\t<questionID>$questionID</questionID>\n".
				"\t<userID>$userID</userID>\n".
				"</newDirected>\n";

echo $filestring;

/*
	Create an XML file on the filesystem,
	open it,
	write the XML string to it,
	and close it
*/
echo "\n<br><br>\n".file_put_contents($file,$filestring);

/*
	Read an XML from the filesystem
*/
if (file_exists($file)) {
	$xml = simplexml_load_file($file);
	echo "\n<br><br>\n";
	$xml?var_dump($xml):exit("Could not load $file");
} else {
	exit("\n<br><br>\n"."No file named $file");
}

include('dbconnect.inc.php');

$questionID	= $xml->questionID[0];
// Ensure questionID is in the Database
$results = mysql_fetch_assoc(mysql_query("SELECT * FROM Questions WHERE questionID = $questionID"));
if (!$results) {
	echo "\n<br><br>\n".(!unlink($file)?"Error deleting $file":"Deleted $file");			//XML File Management
	mysql_close();
	exit("\n<br><br>\n"."Error Inserting Directed Question: questionID=$questionID does not exist.");
}
$userID		= $xml->userID[0];
// Ensure userID is NOT receiving their own question
if ($results['userID']==$userID) {
	echo "\n<br><br>\n".(!unlink($file)?"Error deleting $file":"Deleted $file");			//XML File Management
	mysql_close();
	exit("\n<br><br>\n"."Error Inserting Directed Question: questionID=$questionID cannot direct to Asker, userID=$userID".".");
}
// Ensure userID is in the Database
if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID = $userID"))) {
	echo "\n<br><br>\n".(!unlink($file)?"Error deleting $file":"Deleted $file");			//XML File Management
	mysql_close();
	exit("\n<br><br>\n"."Error Inserting Directed Question: userID=$userID does not exist.");
}
$datetime	= date('Y-m-d H:i:s',time());

/*
	Remove XML file from filesystem
*/
echo "\n<br><br>\n".(!unlink($file)?"Error deleting $file":"Deleted $file");			//XML File Management

/*
	Insert XML contents into the database
*/
$query ="INSERT INTO DirectedQuestions ".
		"(questionID, userID, datetime) ".
		"VALUES ".
		"('$questionID', '$userID', '$datetime')";
echo "\n<br><br>\n".(mysql_query($query)?$query:"Error Inserting Directed Question: ".mysql_error());
mysql_close();
?>
