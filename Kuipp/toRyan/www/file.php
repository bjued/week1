<?php
include('miscFunctions.inc.php');

$term	= $_GET['term'];
$lat	= $_GET['lat'];
$long	= $_GET['long'];
$radius	= $_GET['radius'];
$limit	= $_GET['limit'];

toYelp(1,date('Y-m-d H:i:s'),$term,3,$lat,$long,$radius);
?>
