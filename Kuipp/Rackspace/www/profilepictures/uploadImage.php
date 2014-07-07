<?
include('/var/www/pub/checkSession.php');

$uploaddir = '/var/www/profilepictures/';
$file = basename($_FILES['userfile']['name']);
echo "<> ".$file." <>";
echo "<> ".$_FILES['userfile']['tmp_name']." <>";
$uploadfile = $uploaddir . $file;

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		echo "0".$uploadfile;
}// else echo "1"."Oops! - Failed to upload your photo";
?>
