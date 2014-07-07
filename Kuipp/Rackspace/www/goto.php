<html>
<head>
<script type="text/javascript">
	function redirectToAppStore() {
		window.location.assign("itms-app://itunes.apple.com/us/app/kuipp/id436731294?mt=8");
	}
</script>
</head>
<body>
<?php
$q = $_GET['q'];

if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),"iphone")) {
	?>
	<script type="text/javascript">
	window.location.assign("kuipp://<?php echo "$q";?>");
	setTimeout("redirectToAppStore()",500);
	</script>
<?php }else{?>
	<script type="text/javascript">
	window.location.assign("http://itunes.apple.com/us/app/kuipp/id436731294?mt=8");
	</script>
<?php }?>
</body>
</html>