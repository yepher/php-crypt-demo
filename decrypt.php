<?php
	//////////////////
	// TEST CODE - Usage Demo
	//////////////////
	$privateKey =  base64_decode(file_get_contents("key.priv"));
	//print "privKey:\n".$privKey."\n\n";


	$encryptedData = file_get_contents("encrypted.dat");

	$decryptedMessage = decryptMessage($encryptedData, $privateKey);
	print "\n\n=================\nDecypted:\n".$decryptedMessage . "\n\n";
	
	//////////////////
	// END TEST CODE
	//////////////////

?>


<?php

function decryptMessage($encryptedMessage, $privateKey) {

	// Decode/Decrypt Embedded Symtric Key
	$len = substr($encryptedMessage,0,3); 
	$len = hexdec($len);                  
	$encodedSymKey = substr($encryptedMessage,3,$len);
	openssl_private_decrypt(base64_decode($encodedSymKey), $symetricKey, $privateKey, OPENSSL_PKCS1_PADDING);

	$encryptedMessage = substr($encryptedMessage,3);
	$ciphertext = substr($encryptedMessage,$len);

	// Decypt Symetric Key
	$c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $rawCipher = substr($c, $ivlen+$sha2len);
	$decryptedMessage = openssl_decrypt($rawCipher, $cipher, $symetricKey, $options=OPENSSL_RAW_DATA, $iv);
	//$calcmac = hash_hmac('sha256', $rawCipher, $symetricKey, $as_binary=true);
    //if (hash_equals($hmac, $calcmac)) {
    //print "hmac: ".$decryptedMessage."\n";
    //}

	$decryptedMessage = base64_decode($decryptedMessage);

	return $decryptedMessage;
}

?>
