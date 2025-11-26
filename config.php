<?php

		//Include Google Client Library for PHP autoload file
		require_once 'vendor/autoload.php';

		//Make object of Google API Client for call Google API
		$google_client = new Google_Client();


		//Set the OAuth 2.0 Client ID
		$google_client->setClientId('791299343633-anfmci5klkpu5d8qu2ns3jr1k1uv8l7c.apps.googleusercontent.com');

		//Set the OAuth 2.0 Client Secret key
		$google_client->setClientSecret('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');

		//Set the OAuth 2.0 Redirect URI
		$google_client->setRedirectUri('http://isulan.sksu.edu.ph:8082/oracle/verify.php');
		
		//http://isulan.sksu.edu.ph:8194/share/verify.php
		 // $google_client->setRedirectUri('http://localhost/oracle/verify.php');
		//$google_client->setRedirectUri('http://localhost/tracer.sksualumni.com/verify.php');
		// http://www.sksu-orms.net/researchub/verify.php

		// to get the email and profile 
		$google_client->addScope('email');
		$google_client->addScope('profile');



?>