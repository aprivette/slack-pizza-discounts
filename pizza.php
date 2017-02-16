<?php
	// OPTIONS
	$slack_token = '';
	$location = ''; // dc, baltimore, nyc, philly
		
	include('simple_html_dom.php');
	
	if ($_GET['token'] != $slack_token) {
		$err = 'Token does not match script.';
		die($err);
		echo $err;
	} else {
		
		$ch = curl_init($_GET['response_url']);
		
		$html = file_get_html('http://ispizzahalfprice.com/dc/');
		$verdict = $html->find('.verdict p', 0);
		$verdict = $verdict->innertext();
		if(substr( $verdict, 0, 3 ) == 'Yes') {
			$code = $html->find('.verdict p strong', 0); 
			$code = $code->innertext();
			$link = $html->find('.verdict p a', 0);
			$link = $link->getAttribute('href');
			
			$text = "Pizza is half price!  Use code " . $code . ".";
			
			$title = "Click to order";
			
			$json = [
			    "response_type" => "in_channel",
			    "text" => $text,
			    "attachments" => [[
			   		"title" => $title,
					"title_link" => $link,
					"image_url" => "http://aprivette.pw/slack/pizza.gif"
				]]
			];
		} else {
			$text = "No half price pizza today!";
			$title = "Click to order";
			$link = "https://www.dominos.com/en/";
			$json = [
			    "response_type" => "in_channel",
			    "text" => $text,
			    "attachments" => [[
			   		"title" => $title,
			   		"title_link" => $link,
					"image_url" => "http://aprivette.pw/slack/pizza.gif"
				]]
			];
		}
		
		$json_enc = json_encode($json);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_enc);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		$result = curl_exec($ch);
	}
?>