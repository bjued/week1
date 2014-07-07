<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>base Test</title>
</head>

<body>

<form name="enter" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<br>accesstoken: <input type="text" name="at" />
	<br>facebookID: <input type="text" name="facebookID" />
	<br>question: <input type="text" name="question" />
	<br>questionID: <input type="text" name="questionID" />
	<input type="submit" name="Submit" value="Enter" />
</form>

<?php
$at = $_POST['at'];
$question = $_POST['question'];
$questionID = $_POST['questionID'];
$facebookID = $_POST['facebookID'];

if ($at&&$question&&$questionID) {
	include("/var/www/test/miscFunctions.inc.php");
	
	echo "base 10 = $questionID<br>base 85 = ".base10to85($questionID)."<br>";
	
	$postkeys['access_token']	= $at;
	$postkeys['message']		= $question;
	$postkeys['picture']		= "http://www.kuipp.com/kuipp-logo.jpg";
	//$postkeys['name']			= "I asked on Kuipp:";
	//$postkeys['link']			= "http://kuipp.com";
	$postkeys['caption']		= "Powered by Kuipp";
	$postkeys['description']	= "What do you want to know?";
	$postkeys['actions']		= '{"name": "Answer on Kuipp", "link": "http://www.kuipp.com/goto.php?q=1'.str_pad(base10to85($questionID),5,"0",STR_PAD_LEFT).'"}';
	$postkeys['privacy']		= '{"value": "CUSTOM", "friends": "SOME_FRIENDS", "allow": "1201146,1209623"}';
	
	$poststring = "";
	foreach ($postkeys as $key => $value) $poststring .= "&$key=$value";
	$poststring = substr($poststring,1);
	
	echo $poststring."<br>";
	
	$url = "https://graph.facebook.com/me/feed";
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	//curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_POST,true);
	curl_setopt($curl, CURLOPT_POSTFIELDS,$postkeys);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	$result = curl_exec($curl);
	print_r(curl_getinfo($curl));
	echo "<br>".curl_errno($curl).'-'.curl_error($curl);
	curl_close($curl);
	
	$result = json_decode($result, TRUE);
	if( isset($result['id']) ) {
		// We successfully posted on FB
		echo "<br>".$result['id'];
	}
	
	/*$poststring = "?".$poststring;
	$postlength = strlen($poststring);
	$con = fsockopen("graph.facebook.com/$facebookID/feed");
	fwrite($con, "POST /target_url.php HTTP/1.1\r\n");
	fwrite($con, "Host: graph.facebook.com \r\n");
	fwrite($con, "Content-Type: application/x-www-form-urlencoded\r\n");
	fwrite($con, "Content-Length: $postlength\r\n");
	fwrite($con, "Connection: close\r\n\r\n");
	fwrite($con, $poststring);
	fclose($con);*/
}
?>
</body>
</html>
