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

function yelpSort($a, $b) {
	if ($a['is_closed']!=""&&$b['is_closed']!="") return 0;
	else if ($a['is_closed']!="") return 1;
	else if ($b['is_closed']!="") return -1;
	
	$aRate = $a['avg_rating']+min(.499,$a['review_count']/1000.0);
	$bRate = $b['avg_rating']+min(.499,$b['review_count']/1000.0);
	
	if ($aRate>$bRate) return -1;
	else if ($aRate<$bRate) return 1;
	else {
		if ((float)$a['distance']<(float)$b['distance']) return -1;
		else if ((float)$a['distance']>(float)$b['distance']) return 1;
		else return 0;
	}
}

function toYelp($qid,$date,$term,$returns,$lat,$long,$radius) {
	$limit = 20;
	$YWSID = "sEIIaYhODchybQTbBMscRw";
	$term = urlencode($term);
	$radius = min($radius,25);
	$file = file_get_contents("http://api.yelp.com/business_review_search?term=$term&lat=$lat&long=$long&radius=$radius&limit=$limit&ywsid=$YWSID");
	$array = json_decode($file,TRUE);
	$message = $array['message'];
	if ($message['code']==0) {
		$businesses = $array['businesses'];
		
		$bestBusiness = array();
		
		usort($businesses, "yelpSort");
		
		include('errorCodes.inc.php');
		include('dbconnect.inc.php');
		for ($i=$returns;$i>0;$i--) {
			$best		= $businesses[$i-1];
			
			$name		= $best['name'];
			$rating		= $best['avg_rating'];
			$reviews	= $best['review_count'];
			$ratingURL	= $best['rating_img_url_small'];
			$url		= $best['mobile_url'];
			$location	= $best['city'];
			$latitude	= $best['latitude'];
			$longitude	= $best['longitude'];
		
			$answer = "$name with $rating stars and $reviews reviews!";
			$answer = addslashes(trim($answer));
			
			$query = <<<QUERY
INSERT INTO Answers (questionID, userID, type, yelpRating, yelpRatingURL, yelpReviews, yelpMobileURL, datetime, answer, latitude, longitude, location)
VALUES ('$qid', '500', '1', '$rating', '$ratingURL', '$reviews', '$url', '$date', '$answer', '$latitude', '$longitude', '$location')
QUERY;
			if (!mysql_query($query)) exit("1".$error.' - '.$errorServerFailure);
			mysql_query("UPDATE Questions SET numAnswers = numAnswers + 1 WHERE questionID = '$qid'");
		}
		mysql_close();
	}
}
?>
