<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Email Test</title>
</head>

<body>
<center>Mass Mail</center>
<form action="emailPreview.php" method="post">
Password: <input type="password" name="pwd"><br><br>
To Emails:<br><textarea name="to" rows="2" cols="100"></textarea><br><br>
Subject: <input type="text" name="subject"><br><br>

Body:<br>
please replace all<br>
&amp; (ampersand) with &amp;amp;<br>
&quot; (double quote) with &amp;quot;<br>
&#039; (single quote) with &amp;#039;<br>
$lt; (less than) with &amp;lt;<br>
&gt; (greater than) with &amp;gt;<br>
<textarea name="body" rows="20" cols="100"></textarea><br><br>
<input type="Submit">
</form>
</body>
</html>
