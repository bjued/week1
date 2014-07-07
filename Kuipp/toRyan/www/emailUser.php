<?
$from		= $_POST['from'];
$pwd		= $_POST['pwd'];
$to			= $_POST['to'];
$subject	= $_POST['subject'];
$body		= $_POST['body'];

if ($pwd!="clrjbj0330") exit("wrong password");

if (strlen($subject)==0||strlen($body)==0||strlen($from)==0||strlen($to)==0) exit("all fields must have data");

$name = "Brandon Jue";
$userID = 5123123;


$headers = "From: $from";
$headers .= "\r\nMIME-Version: 1.0";
$headers .= "\r\nContent-type: text/html; charset=iso-8859-1";


$unsubscribeURL = "http://www.kuipp.com/unsubscribeEmail.php?uid=$userID";
$b = "
<html><head><title>Kuipp - What do you want to know?</title></head>
<body link='#ffbb88'><table width=100% border=0 cellpadding=0 cellspacing=5 bgcolor='#333333'>
<tr><td align='center'><a href='http://kuipp.com'><img src='http://kuipp.com/kuipp-logo.jpg' width=200 alt='Kuipp'></a></td></tr>

<tr><td align='center'>
<table width='100%' border=0 cellpadding=10 cellspacing=0 bgcolor='#ffffff'><tr><td>
<font>
Hi $name
<br>
<br>
$body
<br>
<br>
Thank you all for being the early adopters of Kuipp, we really appreciate your support in helping to build the local communities. If you're having fun so far, help us spread the word -
<br>
<br>
<center>
	<a href='http://www.facebook.com/sharer.php?u=http%3A%2F%2Fwww.kuipp.com&amp;src=sp'>
		<img src='http://healingherald.org/wp-content/uploads/2010/03/facebook-icon-743200.jpg' alt='Facebook' width='100px' />
	</a>
	<a href='http://twitter.com/share?_=1306617839251&amp;count=none&amp;original_referer=http%3A%2F%2Fkuipp.com%2F&amp;text=%40KuippTeam%20-%20Ask%20and%20answer%20questions%20in%20your%20local%20community!%20%20Download%20and%20sign-up%20for%20Kuipp%20at&amp;url=http%3A%2F%2Fwww.kuipp.com'>
		<img src='http://www.leesburgmag.com/Images/twitter_icon.gif' alt='Twitter' width='100px' />
	</a>
	<a href='http://youtu.be/dqUdmqOsL7c'>
		<img src='http://cncyouth.org/images/social_icons/youtube_icon.png' alt='YouTube' width='100px' />
	</a>
</center>

If you wish to unsubscribe to this emailing or received this email in error, please use this link: <a href='$unsubscribeURL'>Unsubscribe</a>

</td></tr></table>
</td></tr>

<tr><td>
</td></tr>
</font>
</table></body></html>
";



/*$to = explode(',',$to);
foreach ($to as $rec) {
	$r = trim($rec);
	mail("$r","$subject","$b","$headers","-f $from");
	echo "Sent to: $r<br>";
	sleep(5);
}*/

echo "from: $from<p>to: $to<p>subject: $subject<p>body: $b<p>";

echo "<p>Please confirm this is what you want sent and to the right people, else hit your browser's back button and try again.";

?>

<form action="emailConfirm.php" method="post">
From Email: <input type="text" name="from"><br><br>
Password: <input type="password" name="pwd"><br><br>
To Emails: <textarea name="to" rows="4" col="200"></textarea><br><br>
Subject: <input type="text" name="subject"><br><br>
Body: <textarea name="body" rows="4" col="200"></textarea><br><br>
<input type="Submit">
</form>