<?
include('checkSession.php');

$uid		= $_POST['userID'];
if ($uid!='0') $userID = $uid;
$fields		= $_POST['f'];
$search		= $_POST['k'];
$xml = "";

if ($sessionCode==0&&$userID!="0") {

	$sf = "";
	$search = addslashes(trim($search));
	if (strlen($fields)>0&&strlen($search)) {
		$fields = explode(',',$fields);
		$first = 0;$last = 0;
		foreach ($fields as $field) {
			$field = trim($field);
			if ($field=="firstName") $first = 1;
			if ($field=="lastName") $last = 1;
			$sf .= " OR LOWER($field)=LOWER('$search')";
		}
		if ($first==1&&$last==1) {
			$sf .= " OR CONCAT(LOWER(firstName),' ',LOWER(lastName))=LOWER('$search')";
		}
		$sf = "AND (".substr($sf,4).")";
	}
	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * Users
	 */
	$query = <<<QUERY
SELECT userID,firstName,lastName,email,picture,picX,picY,picZ,level,class
FROM Users
WHERE userID!=$userID
$sf
ORDER BY CONCAT(firstName,' ',lastName)
QUERY;

	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Users>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
		}
		$xml .= "</Users>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>