<?
if (!$LoginAtBack) {
	$query = <<<QUERY
SELECT userID,firstName,lastName,email,facebookID,facebookAT,
	level,class,
	local,recent,forward,visible,
	AMyQ,AFQ,CMyA,FMe,MMe,QMyI,QAMe,QFQ,
	Rmnd
FROM Users
WHERE userID='$userID'
QUERY;
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$xml .= "<Login>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		if ($fn=="facebookID") $fn = $row['email'].$fn;
		if ($fn=="facebookAT") $fn = $row['email'].$fn;
		$xml .= "<$fn>".$row[$i]."</$fn>";
	}
	if ($sessionID!=0) $xml .= "<sessionID>".$sessionID."</sessionID>";
	$xml .= "</Login>";
}

$query = <<<QUERY
SELECT categoryID,category
FROM Categories
QUERY;
$results = mysql_query($query);
$xml .= "<Categories>";
while ($row = mysql_fetch_array($results)) {
	$fn = $row['category'];
	$xml .= "<$fn>".$row['categoryID']."</$fn>";
}
$xml .= "</Categories>";

$query = <<<QUERY
SELECT k.keywordID,keyword
FROM Interests AS i, Keywords AS k
WHERE i.keywordID=k.keywordID
AND i.userID='$userID'
QUERY;
$results = mysql_query($query);
$xml .= "<Interests>";
while ($row = mysql_fetch_array($results)) {
	$fn = $row['keyword'];
	$xml .= "<$fn>".$row['keywordID']."</$fn>";
}
$xml .= "</Interests>";

$query = <<<QUERY
SELECT followingID
FROM Followers
WHERE userID='$userID'
QUERY;
$results = mysql_query($query);
$xml .= "<Following>";
while ($row = mysql_fetch_array($results)) {
	$fn = $row['followingID'];
	$xml .= "<$fn>$fn</$fn>";
}
$xml .= "</Following>";

if ($LoginAtBack) {
	$query = <<<QUERY
SELECT userID,firstName,lastName,email,facebookID,facebookAT,
	level,class,
	local,recent,forward,visible,
	AMyQ,AFQ,CMyA,FMe,MMe,QMyI,QAMe,QFQ,
	Rmnd
FROM Users
WHERE userID='$userID'
QUERY;
	$results = mysql_query($query);
	$row = mysql_fetch_array($results);
	$xml .= "<Login>";
	for ($i=0;$i<mysql_num_fields($results);$i++) {
		$fn = mysql_field_name($results,$i);
		if ($fn=="facebookID") $fn = $row['email'].$fn;
		if ($fn=="facebookAT") $fn = $row['email'].$fn;
		$xml .= "<$fn>".$row[$i]."</$fn>";
	}
	if ($sessionID!=0) $xml .= "<sessionID>".$sessionID."</sessionID>";
	$xml .= "</Login>";
}
?>