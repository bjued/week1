<?
include('checkSession.php');
include('defaultValues.php');

/*
 * Get Form Data
 */
$lastID		= $_POST['lid'];
$counts		= $_POST['cnt'];
if ($lastID=="0") {
	$count = $defaultCount*$counts;
} else {
	$count = 1000;
}
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
AND (m.main REGEXP '.*$s.*'
	OR u.firstName REGEXP '.*$s.*'
	OR u.lastName REGEXP '.*$s.*'
	OR u.email REGEXP '.*$s.*'
	OR m.subject REGEXP '.*$s.*'
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
	
	/*
	 * ToUser (to me)
	 */
	$query = <<<QUERY
SELECT m.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT a.message AS main,
	a.toUser AS me,
	a.fromUser As you,
	a.datetime,
	a.messageID,
	a.chainID,
	'0' AS questionID,
	c.subject
	FROM Messages AS a
	JOIN Chains AS c USING(chainID)
	UNION ALL
	SELECT q.question AS main,
	d.userID AS me,
	q.userID AS you,
	q.datetime,
	'0' AS messageID,
	'0' AS chainID,
	q.questionID,
	'' AS subject
	FROM DirectedQuestions AS d
	JOIN Questions AS q USING(questionID)
	) AS m,Users AS u
WHERE m.me='$userID'
AND m.you=u.userID
AND m.datetime>=$lastID
$search
ORDER BY m.datetime DESC
LIMIT $count
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<To>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</To>";
	}
	
	/*
	 * FromUser
	 *//*
	$query = <<<QUERY
SELECT m.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT Messages.*,Chains.subject
	FROM Messages JOIN Chains USING(chainID)
	) AS m,Users AS u
WHERE m.fromUser=$userID
AND m.toUser=u.userID
ORDER BY messageID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<From>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</From>";
	}
	
	*//* DirectedTo
	 *//*
	$query = <<<QUERY
SELECT dq.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT q.*,d.userID AS duserID
	FROM DirectedQuestions AS d
	JOIN Questions AS q USING(questionID)
	) AS dq,Users AS u
WHERE duserID=$userID
AND dq.userID=u.userID
ORDER BY questionID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<DirectedTo>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</DirectedTo>";
	}*/
	
	/*
	 * DirectedFrom
	 *//*
	$query = <<<QUERY
SELECT dq.*,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.class
FROM (
	SELECT q.*,d.userID AS duserID
	FROM DirectedQuestions AS d
	JOIN Questions AS q USING(questionID)
	) AS dq,Users AS u
WHERE dq.userID=$userID
AND duserID=u.userID
ORDER BY questionID DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<DirectedFrom>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".($fn=="datetime"?strtotime($row[$i]):$row[$i])."</$fn>";
		}
		$xml .= "</DirectedFrom>";
	}
	
	*//* Followers
	 */
	$query = <<<QUERY
SELECT u.userID,firstName,lastName,u.picture,u.picX,u.picY,u.picZ
FROM Users AS u,Followers as f
WHERE f.followingID=$userID
AND u.userID=f.userID
ORDER BY CONCAT(firstName,' ',lastName) DESC
QUERY;
	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Followers>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<".$fn.">".$row[$i]."</".$fn.">";
		}
		$xml .= "</Followers>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);

?>