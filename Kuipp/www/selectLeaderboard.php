<?
include('checkSession.php');

$xml = "";

if ($sessionCode==0&&$userID!="0") {
	
	include('dbconnect.inc.php');
	
	/*
	 * Create an XML string
	 */
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>"."<xmlstring>";
	
	/*
	 * User
	 */
	mysql_query("SET @rank=0");
	$query = <<<QUERY
SELECT rank,k.userID,k.firstName,k.lastName,k.picture,k.picX,k.picY,k.picZ,k.level,k.class,k.points
FROM (
	SELECT @rank:=@rank+1 AS rank,u.*
	FROM (
		SELECT p.*
		FROM Users AS u, Followers AS f, Users AS p
		WHERE u.userID=f.userID
		AND p.userID=f.followingID
		AND u.userID=$userID
		UNION SELECT *
		FROM Users
		WHERE userID=$userID
	) AS u
	ORDER BY points DESC
) AS k
WHERE userID=$userID
QUERY;
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$xml .= "<Self>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		$xml .= "<$fn>".$row[$i]."</$fn>";
	}
	$xml .= "<board>2</board>";
	$xml .= "</Self>";
	
	/*
	 * Active City
	 */
	$query = <<<QUERY
SELECT u.userID,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.level,u.class,u.points
FROM Users AS u, Users AS me
WHERE u.location=me.location
AND me.userID=$userID
ORDER BY points DESC
LIMIT 20
QUERY;

	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Users>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "<board>1</board>";
		$xml .= "</Users>";
	}
	
	/*
	 * Following
	 */
	$query = <<<QUERY
SELECT u.userID,u.firstName,u.lastName,u.picture,u.picX,u.picY,u.picZ,u.level,u.class,u.points
FROM (
	SELECT p.*
	FROM Users AS u, Followers AS f, Users AS p
	WHERE u.userID=f.userID
	AND p.userID=f.followingID
	AND u.userID=$userID
	UNION SELECT *
	FROM Users
	WHERE userID=$userID
) AS u
ORDER BY points DESC
LIMIT 20
QUERY;

	$results = mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
		$xml .= "<Users>";
		for ($i=0;$i<mysql_num_fields($results);$i++) {
			$fn = mysql_field_name($results,$i);
			$xml .= "<$fn>".$row[$i]."</$fn>";
		}
		$xml .= "<board>2</board>";
		$xml .= "</Users>";
	}
	
	$xml .= "</xmlstring>";
	mysql_close();			
}
exit($xml);
?>