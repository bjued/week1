<?
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
AND i.userID=$userID
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
WHERE userID=$userID
QUERY;
$results = mysql_query($query);
$xml .= "<Following>";
while ($row = mysql_fetch_array($results)) {
	$fn = $row['followingID'];
	$xml .= "<$fn>$fn</$fn>";
}
$xml .= "</Following>";
?>