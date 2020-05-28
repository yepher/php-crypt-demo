<?php
	//////////////////
	// TEST CODE - Usage Demo
	//////////////////
	$testMessage = array (
		'id' => '0001',
		'type' => 'donut',
		'name' => 'Cake',
		'ppu' => 0.55,
		'batters' => 
		array (
		  'batter' => 
		  array (
			0 => 
			array (
			  'id' => '1001',
			  'type' => 'Regular',
			),
			1 => 
			array (
			  'id' => '1002',
			  'type' => 'Chocolate',
			),
			2 => 
			array (
			  'id' => '1003',
			  'type' => 'Blueberry',
			),
			3 => 
			array (
			  'id' => '1004',
			  'type' => 'Devil\'s Food',
			),
		  ),
		),
		'topping' => 
		array (
		  0 => 
		  array (
			'id' => '5001',
			'type' => 'None',
		  ),
		  1 => 
		  array (
			'id' => '5002',
			'type' => 'Glazed',
		  ),
		  2 => 
		  array (
			'id' => '5005',
			'type' => 'Sugar',
		  ),
		  3 => 
		  array (
			'id' => '5007',
			'type' => 'Powdered Sugar',
		  ),
		  4 => 
		  array (
			'id' => '5006',
			'type' => 'Chocolate with Sprinkles',
		  ),
		  5 => 
		  array (
			'id' => '5003',
			'type' => 'Chocolate',
		  ),
		  6 => 
		  array (
			'id' => '5004',
			'type' => 'Maple',
		  ),
		),
	  );

	$publicKey =  base64_decode(file_get_contents("key.pub"));
	//print "pubKey:\n".$publicKey."\n\n";

	$dataToEncrypt = json_encode($testMessage);
	print "\nWill Encrypt: \n".$dataToEncrypt."\n\n";
	$encryptedMessage = encryptMessage($dataToEncrypt, $publicKey);
	file_put_contents("encrypted.dat", $encryptedMessage);

	//////////////////
	// END TEST CODE
	//////////////////
?>



	
<?php

////////
// Encrypt data using an asymetric key
function encryptMessage($dataToEncrypt, $publicKey, $keyLen=128) {

	// Generate new randome symetric key
	$symetricKey = base64_encode(openssl_random_pseudo_bytes($keyLen));
		
	// Encrypt our symetric key
	openssl_public_encrypt($symetricKey, $encryptedKey, $publicKey, OPENSSL_PKCS1_PADDING);
	
	// Encrypte Data
	$ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
	$iv = openssl_random_pseudo_bytes($ivlen);
	$inputData = base64_encode($dataToEncrypt);
	$ciphertext_raw = openssl_encrypt($inputData, $cipher, $symetricKey, $options=OPENSSL_RAW_DATA, $iv);
	$hmac = hash_hmac('sha256', $ciphertext_raw, $symetricKey, $as_binary=true);
	$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
	
	
	$encodedKey = base64_encode($encryptedKey);
	$len = strlen($encodedKey);
	$len = dechex($len);
	$len = str_pad($len,3,'0',STR_PAD_LEFT);

	$message = $len.$encodedKey.$ciphertext;

	return $message;
}

?>
