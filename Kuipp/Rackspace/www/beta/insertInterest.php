<?
include('errorCodes.inc.php');
include('checkSession.php');

/*
 * Read in form data
 */
$uid		= $_POST['uid'];
if ($uid!="0") $userID = $uid;
$interests	= $_POST['int'];

if ($sessionCode==0&&$userID!="0") {
	include('dbconnect.inc.php');

	// Ensure userID is in the Database
	if (!mysql_fetch_assoc(mysql_query("SELECT * FROM Users WHERE userID=$userID"))) exit("1".$error.' - '.$errorNoUser);		//userID not found
	
	/*
	 * Loop through comma separated interests
	 */
	$interests = explode(',',$interests);
	foreach ($interests as $interest) {
		$interest	= strtolower(addslashes(trim($interest)));
		/*
		 * Get keywordID -if interests="":0, if db:id, if !db:insert
		 */
		if (strlen($interest)!=0) {
			if ($row = mysql_fetch_assoc(mysql_query("SELECT keywordID FROM Keywords WHERE keyword='$interest'"))) {
				$id = $row['keywordID'];
			} else {
				if (!mysql_query("INSERT INTO Keywords (keyword) VALUES ('$interest')")) exit("1".$error.' - '.$errorServerFailure);
				$id = mysql_insert_id();
			}
		
			/*
			 * Can't follow C:K if already following C:*, *:K, or C:K - exit
			 */
			$query = <<<QUERY
SELECT *
FROM Interests
WHERE userID=$userID
AND keywordID=$id
QUERY;
			if (mysql_num_rows(mysql_query($query))==0) {
			
				/*
				 * Insert into the database
				 */
				if (!mysql_query("INSERT INTO Interests (userID, keywordID) VALUES ($userID, $id)")) exit("1".$error.' - '.$errorServerFailure);
			}
		
		}
	}

	mysql_close();
}
echo("0".$id);
?>
