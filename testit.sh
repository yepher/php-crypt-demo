echo "Generating Key Files (key.pub, key.priv)"
php generate_key_files.php


echo "Encrypting Data"
php encrypt.php


echo "Decrypting Data"
php decrypt.php
