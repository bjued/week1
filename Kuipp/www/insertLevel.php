<?
$file = "xml/newLevel.xml";

/*
	Read in form data and put into an xml on the filesystem
*/
$levelID	= $_POST['levelID'];
$minPoints	= $_POST['minPoints'];
$className	= $_POST['className'];
$numInLevel	= $_POST['numInLevel'];

$className	= addslashes($className);

/*
	Create an XML string
*/
$filestring =	"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n".
				"<newLevel>\n".
				"\t<levelID>$levelID</levelID>\n".
				"\t<minPoints>$minPoints</minPoints>\n".
				"\t<className>$className</className>\n".
				"\t<numInLevel>$numInLevel</numInLevel>\n".
				"</newLevel>\n";

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

$levelID	= $xml->levelID[0];
// Ensure levelID is NOT in the Database
if (mysql_fetch_assoc(mysql_query("SELECT * FROM Level WHERE levelID = $levelID"))) {
	echo "\n<br><br>\n".(!unlink($file)?"Error deleting $file":"Deleted $file");			//XML File Management
	mysql_close();
	exit("\n<br><br>\n"."Error Inserting Level: levelID=$levelID already exists.");
}
$minPoints	= $xml->minPoints[0];
$className	= $xml->className[0];
$numInLevel	= $xml->numInLevel[0];

/*
	Remove XML file from filesystem
*/
echo "\n<br><br>\n".(!unlink($file)?"Error deleting $file":"Deleted $file");			//XML File Management

/*
	Insert XML contents into the database
*/
$query ="INSERT INTO Level ".
		"(levelID, minPoints, className, numInLevel) ".
		"VALUES ".
		"('$levelID', '$minPoints', '$className', '$numInLevel')";
echo "\n<br><br>\n".(mysql_query($query)?$query:"Error Inserting Level: ".mysql_error());
mysql_close();
?>
