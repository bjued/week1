<?
include('checkSession.php');

/*
 * Read in form data
 */
$lastID		= $_POST['lid'];
$counts		= $_POST['cnt'];
if ($lastID=="") {
	$count = $defaultCount*$counts;
} else {
	$count = 1000;
}
$chainID	= $_POST['cid'];
$search		= $_POST['sch'];
$xml		= "";

/*
 * Verify Search, Add Slashes, Trim, Compile Where Clause
 */
if ($search!="") {
	$search = addslashes(addslashes(addcslashes($search,'^$.*+?{}[]()')));
	$searchArray = explode(' ',$search);
	$search = "";
	foreach ($searchArray as $a) {
		$s = trim($a);
		$search .= <<<SEARCH
AND (m.message REGEXP '.*$s.*'
	OR u.firstName REGEXP '.*$s.*'
	OR u.lastName REGEXP '.*$s.*'
	OR u.email REGEXP '.*$s.*'
	OR c.subject REGEXP '.*$s.*'
	)
SEARCH;
	}
}

if ($sessionCode==0&&$userID!="0") {	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	$query = <<<QUERY
SELECT m.*,c.subject,u.userID,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM Messages AS m,Users AS u,Chains AS c
WHERE m.chainID=$chainID
AND c.chainID=m.chainID
AND m.fromUser=u.userID
AND messageID>=$lastID
$search
ORDER BY messageID DESC
LIMIT $count
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Message>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</Message>";
	}
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);

?>