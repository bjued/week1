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
		/*$file = file_get_contents("https://graph.facebook.com/me/friends?access_token=$at");
		$array = json_decode($file,TRUE);
		$fbids = explode(',',$fbids);
		$to['data'] = array();
		foreach ($fbids as $fid) {
			foreach ($array['data'] as $per) if ((int)$per['id']==$fid) {
				array_push($to['data'],array("name"=>$per['name'], "id"=>$per['id']));
				break;
			}
		}
		print_r($fbids);
		print_r($to);
		print_r($fbids);
		
		echo $at;*/
		
		// publish to FB
		$postkeys = array(
			'access_token'	=> $at,
			'message'		=> $msg,
			'picture'		=> "http://www.kuipp.com/kuipp-logo.jpg",
			'name'			=> "I asked on Kuipp:",
			'link'			=> "http://kuipp.com",
			'caption'		=> "Powered by Kuipp",
			'description'	=> "What do you want to know?",
			'actions'		=> json_encode(array(
								'name'	=> ($t==1?'Answer':'View')." on Kuipp",
								'link'	=> 'http://www.kuipp.com/goto.php?q='.$t.str_pad(base10to85($id),5,'0',STR_PAD_LEFT))));
		/*
		$fbids = explode(',',$fbids);
		foreach ($fbids as $fid) {
			$url = "https://graph.facebook.com/$fid/feed";
			
			$try = 0;
			$success = false;
			while (!$success) {
				echo "tried";
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
				if(isset($result['id'])) {
					echo "andSuccess";
					$success = true;
				}
				if($try==3) {
					echo "failed$fid";
					$success = true;
				}
			}
		}*/
		
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
			if(isset($result['id'])) {
				//$objectID = $result['id'];
				//mysql_query("UPDATE Questions SET facebookID = '$objectID' WHERE questionID = '$id'");
				$success = true;
			}
			if ($try==3) {
				//echo "failedme";
				$success = true;
			}
		}
	}
}

function toTwitter($t,$question,$questionID) {
}
?>
