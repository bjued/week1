<?
$pwd		= $_POST['pwd'];
$to			= $_POST['to'];
$subject	= $_POST['subject'];
$body		= $_POST['body'];

if (strlen($subject)==0||strlen($body)==0||strlen($to)==0) exit("all fields must have data");
if ($pwd!="clrjbj0330") exit("wrong password");

session_start();

$_SESSION['pwd'] = $pwd;
$_SESSION['to'] = $to;
$_SESSION['subject'] = $subject;

$from = "team@kuipp.com";
$headers = "From: $from";
$headers .= "\r\nMIME-Version: 1.0";
$headers .= "\r\nContent-type: text/html; charset=iso-8859-1";
$_SESSION['from'] = $from;
$_SESSION['headers'] = $headers;

$name = " ifUserThenNameElseBlank";
$userID = "EmailHash";

$top = "<html><head><title>Kuipp - What do you want to know?</title></head>
<body link='#ffbb88'><table width=100% border=0 cellpadding=0 cellspacing=5 bgcolor='#333333'>
<tr><td align='center'><a href='http://kuipp.com'><img src='http://kuipp.com/kuipp-logo.jpg' width=200 alt='Kuipp'></a></td></tr>

<tr><td align='center'>
<table width='100%' border=0 cellpadding=10 cellspacing=0 bgcolor='#ffffff'><tr><td>
<font>";
$greet = "Hey";
//$name
$break = ",<br><br>";
//$body
$sign = "<br><br>- Kuipp Team";
$share = "<br><br><center>
	<a href='http://www.facebook.com/sharer.php?u=http%3A%2F%2Fwww.kuipp.com&amp;src=sp'>
		<img src='http://healingherald.org/wp-content/uploads/2010/03/facebook-icon-743200.jpg' alt='Facebook' width='50px' />
	</a>
	&nbsp;
	<a href='http://twitter.com/share?_=1306617839251&amp;count=none&amp;original_referer=http%3A%2F%2Fkuipp.com%2F&amp;text=%40KuippTeam%20-%20Ask%20and%20answer%20questions%20in%20your%20local%20community!%20%20Download%20and%20sign-up%20for%20Kuipp%20at&amp;url=http%3A%2F%2Fwww.kuipp.com'>
		<img src='http://www.leesburgmag.com/Images/twitter_icon.gif' alt='Twitter' width='50px' />
	</a>
	&nbsp;
	<a href='http://youtu.be/dqUdmqOsL7c'>
		<img src='http://cncyouth.org/images/social_icons/youtube_icon.png' alt='YouTube' width='50px' />
	</a>
</center>";
$unsubscribe1 = "<br><br>If you wish to no longer receive these emails or if you received this email in error, please use this <a href='http://www.kuipp.com/emailUnsubscribe.php?userID=";
//$userID
$unsubscribe2 = "'>link</a>.";
$bottom = "</td></tr></table></td></tr><tr><td></td></tr></font></table></body></html>";

$b = $top.$greet.$name.$break.$body.$sign.$share.$unsubscribe1.$userID.$unsubscribe2.$bottom;

$_SESSION['top'] = $top;
$_SESSION['greet'] = $greet;
$_SESSION['break'] = $break;
$_SESSION['body'] = $body;
$_SESSION['sign'] = $sign;
$_SESSION['share'] = $share;
$_SESSION['unsubscribe1'] = $unsubscribe1;
$_SESSION['unsubscribe2'] = $unsubscribe2;
$_SESSION['bottom'] = $bottom;

echo "to: $to<p>subject: $subject<p>body: $b<p>";

echo "<p>Please confirm this is what you want sent and to the right people, else hit your browser's back button and try again.";

echo "<p><a href='emailConfirm.php'>Confirm</a>";
?>