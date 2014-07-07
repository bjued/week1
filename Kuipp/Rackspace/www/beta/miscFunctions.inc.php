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

function toFacebook($t,$at,$fbid,$fbids,$msg,$id) {
	/*
	 * Post to Facebook Wall if we have their access token and flag
	 */
	if ($at!="0"&&$fbid!="0") {
		// get to list
		$file = file_get_contents("https://graph.facebook.com/me/friends?access_token=$at");
		$array = json_decode($file,TRUE);
		$fbids = explode(',',$fbids);
		foreach ($fbids as $fid) {
			foreach ($array['data'] as $person) {
				if ($person['id']==$fid) {
					$to['data'] = $person;
					break;
				}
			}
		}
		
		// publish to FB
		$postkeys['access_token']	= $at;
		$postkeys['to']				= json_encode($to);
		$postkeys['message']		= $msg;
		$postkeys['picture']		= "http://www.kuipp.com/kuipp-logo.jpg";
		//$postkeys['name']			= "I asked on Kuipp:";
		//$postkeys['link']			= "http://kuipp.com";
		$postkeys['caption']		= "Powered by Kuipp";
		$postkeys['description']	= "What do you want to know?";
		$postkeys['actions']		= '{"name": "'.($t==1?'Answer':'View').' on Kuipp", "link": "http://www.kuipp.com/goto.php?q='.$t.str_pad(base10to85($id),5,"0",STR_PAD_LEFT).'"}';
		$postkeys['privacy']		= '{"value": "CUSTOM", "friends": "SOME_FRIENDS", "allow": "1223191,1201146,1209623"}';
			
		//$poststring = "";
		//foreach ($postkeys as $key => $value) $poststring .= "&$key=$value";
		//$poststring = substr($poststring,1);
			
		$url = "https://graph.facebook.com/me/feed";
			
		$try = 0;
		$success = false;
		while (!$success) {
			$try++;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curl, CURLOPT_POST,true);
			curl_setopt($curl, CURLOPT_POSTFIELDS,$postkeys);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			$result = curl_exec($curl);
			curl_close($curl);
			
			$result = json_decode($result, TRUE);
			if(isset($result['id'])||$try==3) $success = true;
		}
	}
}

function toTwitter($t,$question,$questionID) {
}
?>
