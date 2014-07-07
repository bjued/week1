<?
// pnArray and pnDevice must be set before calling this script
if (!$pn2DArray) exit("Must set alert array before calling this script");
if (!$pnDevice) exit("Must set device token before calling this script");
// if no expiration is set, set it to a day;
if (!$pnExpire) $pnExpire = 86400;

// file to store the queue
$pnFilePath		= '/tmp/pn.txt';

$pnTime = time();

$payload = json_encode($pn2DArray);

$pnfile = fopen($pnFilePath,'a');

fwrite($pnfile,$pnTime."\t".$pnExpire."\t".$pnDevice."\t".$payload."\n");

fclose($pnfile);
?>
