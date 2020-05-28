<?php

$key = openssl_pkey_new(array('private_key_bits' => 2048));

$private_key = openssl_pkey_get_details($key);
openssl_pkey_export($key, $privKey);
$public_key = $private_key['key'];

file_put_contents("key.priv", base64_encode($privKey));
print "\nPrivate Key: \n".$privKey."\n\n";

file_put_contents("key.pub", base64_encode($public_key));
print "\Public Key: \n".$public_key."\n\n";

?>


