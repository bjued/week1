<?php
class cropImage {
	var $imgSrc,$myImage,$cropHeight,$cropWidth,$x,$y,$thumb;

	function setImage($image,$orig,$dbx,$dby,$dbz) {
		//Your Image
		$this->imgSrc = $image; 

		//getting the image dimensions
		list($width, $height) = getimagesize($this->imgSrc); 

		//create image from the jpeg
		$this->myImage = imagecreatefromjpeg($this->imgSrc) or die("Error: Cannot find image!"); 

		//Scale the image
		$this->cropWidth   = $orig/$dbz; 
		$this->cropHeight  = $orig/$dbz; 

		//getting the top left coordinate
		$this->x = $dbx*-1/$dbz;
		$this->y = $dby*-1/$dbz;
	}
	
	function createThumb($size) {
		$this->thumb = imagecreatetruecolor($size, $size); 

		imagecopyresampled($this->thumb, $this->myImage, 0, 0,$this->x, $this->y, $size, $size, $this->cropWidth, $this->cropHeight); 
	}
	
	function renderImage() {
		header('Content-type: image/jpeg');
		imagejpeg($this->thumb);
		imagedestroy($this->thumb); 
	}
}

$src	= $_GET['src'];
$to		= $_GET['to'];
$from	= $_GET['from'];
$dbx	= $_GET['dbx'];
$dby	= $_GET['dby'];
$dbz	= $_GET['dbz'];

$image = new cropImage();
$image->setImage($src,$from,$dbx,$dby,$dbz);
$image->createThumb($to);
$image->renderImage();
?>
