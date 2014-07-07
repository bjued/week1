#!/usr/bin/php -q
<?
$apnsHost = 'gateway.push.apple.com';
$apnsPort = 2195;
$apnsCert = '/opt/third-party/apple/apns-pro.pem';
$pnFilePath		= '/tmp/pn.txt';
$pnTempPath		= '/tmp/pnTemp.txt';

if (!file_exists($pnFilePath)) exit(date('Y-m-d H:i:s')." - "."Nothing in queue\n");

$streamContext = stream_context_create();
if (!stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert)) exit(date('Y-m-d H:i:s')." - "."Failed to set streamContext options\n");

$apns = stream_socket_client('ssl://'.$apnsHost.':'.$apnsPort,$error,$errorString,2,STREAM_CLIENT_CONNECT,$streamContext);
if(!$apns) exit(date('Y-m-d H:i:s')." - "."Failed to open stream: Error = $error String = $errorString\n");

// If there is a file and the connection succeeds, rename $pnFilePath to $pnTempPath to freeze the notifications to be sent without blocking new notifications from being written while we send these
if (!rename($pnFilePath,$pnTempPath)) exit(date('Y-m-d H:i:s')." - "."Failed to rename file\n");

$pnfile = file($pnTempPath);

$msg = "";
for ($i=0;$i<count($pnfile);$i++) {
// Loop through all files and send them in the correct format
	$line = explode("\t",trim($pnfile[$i])); //0=time 1=expire 2=token 3=payload
	$e = $line[0]+$line[1];
	if ($line[1]==0||$e>time()) { // Only send if the expiration hasn't past
		// Cmd1|Id4|Exp4|len(token)2|token32|len(payload)2|payload?
		$apnsMessage = chr(1).
		pack('N',$i).
		pack('N',$e).
		pack('n',32).
		pack('H*', str_replace(' ', '', $line[2])).
		pack('n',strlen(trim($line[3]))).
		trim($line[3]);
		echo date('Y-m-d H:i:s')." - "."Sent PN = ".bin2hex($apnsMessage)."\n";
		$msg .= $apnsMessage;
	} else {
		echo "date('Y-m-d H:i:s')." - ".Message $i expired, not sending to apns\n";
	}
}


if (!fwrite($apns,$msg)) exit(date('Y-m-d H:i:s')." - "."Failed to write to stream:Error = $error String = $errorString\n");
/*
while (fread($apns,1)==chr(8)) {
	switch ((int)fread($apns,1)) {
		case  1: $status = "Processing error";
		case  2: $status = "Missing device token";
		case  3: $status = "Missing topic";
		case  4: $status = "Missing payload";
		case  5: $status = "Invalid token size";
		case  6: $status = "Invalid topic size";
		case  7: $status = "Invalid payload size";
		case  8: $status = "Invalid token";
		default: $status = "No error";
	}
		
	$identifier = (int)fread($apns,4);
		
	echo date('Y-m-d H:i:s')." - "."Error = $status for ".$pnfile[$identifier]."\n";
}*/

// Close the connection
// socket_close($apns);
fclose($apns);
?>
