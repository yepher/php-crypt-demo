<?php

$key = openssl_pkey_new(array('private_key_bits' => 2048));

$bob_key = openssl_pkey_get_details($key);
openssl_pkey_export($key, $privKey);
$bob_public_key = $bob_key['key'];

file_put_contents("key.priv", base64_encode($privKey));

file_put_contents("key.pub", base64_encode($bob_public_key));

?>


