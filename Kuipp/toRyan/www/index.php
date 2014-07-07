<?php

	$siteTitle = "What do you want to know?";
	include("top.inc");
	include("dbconnect.inc.php");
	
?>

<div class="column-left">

<p class="title">Recent Activity</p>

<?php

	$divbar = 0;

	$query = mysql_query("SELECT e.userID, e.type, e.location, e.datetime, e.info1, e.info2, u.picture, u.picX, u.picY, u.picZ, u.firstName, u.lastName FROM Events AS e, Users AS u WHERE e.userID=u.userID ORDER BY e.eventID DESC LIMIT 0, 10");
	if (!$query) {
		echo("database error: " . mysql_error() . "");
		exit();
	}

	while($result = mysql_fetch_assoc($query)) {
		$userid = $result["userID"];
		$type = $result["type"];
		$location = $result["location"];
		$datetime = $result["datetime"];
		$info1 = nl2br($result["info1"]);
		$info2 = nl2br($result["info2"]);
		$picture = $result["picture"];
		$picX = $result["picX"];
		$picY = $result["picY"];
		$picZ = $result["picZ"];
		$firstName = $result['firstName'];
		$lastName = $result['lastName'];
		
		// Create the name (First L.)
		$name = ucwords(strtolower($firstName)).' '.strtoupper(substr($lastName,0,1)).'.';
		
		// Verify location otherwise don't include
		$location = ($location!="(null)"&&$location!="Unknown")?" | $location":"";
		
		// Get time past
		$datetime = strtotime($datetime);
		$datetime = time()-$datetime;
		
		// Create the time since

		if ($datetime>60*60*24) {
			$days = $datetime/60/60/24%365;
			$time = "$days hour".($days==1?" ":"s ");
		} elseif ($datetime>60*60) {
			$hrs = $datetime/60/60%24;
			$time = "$hrs hour".($hrs==1?" ":"s ");
		} elseif ($datetime>60) {
			$mins = $datetime/60%60;			
			$time = "$mins minute".($mins==1?" ":"s ");
		} else {
			$secs = $datetime%60;
			$time = "$secs second".($secs==1?"":"s");
		}

		if ($divbar) {
			echo("<hr class='recent'>");
		} else {
			$divbar = 1;
		}
		
		$default = "http://kuipp.com/profilepictures/default.png";
		
		echo("<div class='recent'>");
		if ($picture==$default) echo "<img src='$picture' class='recent'>";
		else echo "<img src='http://kuipp.com/profilepictures/imgDsp.php?src=$picture&to=80&from=130&dbx=$picX&dby=$picY&dbz=$picZ' class='recent' onError='this.src=$default' onAbort='this.src=$default'>";
		echo("<p class='recent'>");
		
		if ($type == 1) {
		
		echo("<font class='recent-name'>$name</font> just asked:<br>");
		echo("$info1<br>");
		
		} elseif ($type == 2) {
		
		echo("<font class='recent-name'>$name</font> just answered:<br>");
		echo("$info2<br>");
		echo("<font class='recent-addl'>in response to: $info1</font><br>");
		
		} elseif ($type == 3) {
		
		echo("<font class='recent-name'>$name</font><br>");
		echo("has achieved the <b>$info1</b> level!<br>");
		
		} else {
		
		echo("<font class='recent-name'>$name</font><br>");
		echo("has earned the <b>$info1</b> medal!<br>");
		
		}
		
		echo("<font class='recent-details'>$time ago$location</font>");

		echo("</p></div>");

		}

?>


</div>
<div class="column-right">

<p class="side-body"><a href="http://itunes.apple.com/us/app/kuipp/id436731294"><img src="http://www.kuipp.com/getonappstore.png"></a></p>

<p class="side-title">What is Kuipp?</p>

<p class="side-body">Kuipp is a location-based, question & answer platform that provides users with a fun, fast way to ask and answer questions to discover what's currently happening in a specific area. Kuipp is ideal for local communities, organizations, tourists and those new to the area.</p>

<p class="side-title">Share with Your Friends!</p>

<p class="side-body">Help us make Kuipp even more useful in your community and share with your friends on Facebook and Twitter!</p>
			   	 
<p><a href='http://www.facebook.com/sharer.php' name='fb_share' share_url='http://www.kuipp.com' style='display:block;float:left;margin01px 0px 0px 0px;' type='button' type='button'>Share</a>
<script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script><br>
<br>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<a href='http://twitter.com/share' class='twitter-share-button' data-url='http://www.kuipp.com' data-text='@KuippTeam - Ask and answer questions in your local community!  Download and sign-up for Kuipp at' data-count='none'>Tweet</a></p>

<p class="side-title">In the Press</p>

<p class="side-body"><a href="http://thenextweb.com/apps/2011/05/18/kuipp-for-iphone-lets-you-feel-like-a-local-wherever-you-are/"><img src="http://www.kuipp.com/presslogos/logo_thenextweb.png" alt="The Next Web"></a><br>
<a href="http://www.killerstartups.com/Mobile/kuipp-com-ask-questions-and-get-local-answers"><img src="http://www.kuipp.com/presslogos/logo_killerstartups.jpg" alt="Killer Startups"></a></p>

</div>


<?php include("bottom.inc"); ?>