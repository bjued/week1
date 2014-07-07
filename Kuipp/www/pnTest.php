<?
$pnDevice			= $_POST['deviceToken'];
$pnExpire			= $_POST['expire'];
$pn2DArray['aps']	= array('alert'=>$_POST['alert'], 'badge'=>(int)$_POST['badge'], 'sound'=>$_POST['sound']);

include('pnQueue.php');

$file = fopen($pnFilePath,'r');
while (!feof($file)) echo nl2br(fgets($file));
?>
