<?
function base10to85($base10) {
	$base85 = "";
	
	$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@&%?,=[]_:-+.*\$#!'^();~";
	
	$base10 = (int)$base10;
	
	while ($base10!=0) {
		$digit = $base10%85;
		$ch = substr($chars,$digit,1);
		$base85 = "$ch$base85";
		$base10 = (int)($base10/85);
	}
	
	return $base85;
}
?>
