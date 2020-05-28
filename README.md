# Demonstration of using openssl crypt in PHP

This is a brief example of using PHP openssl to encrypt/decrypt data. This method can be helpful where you want to have something encrypted by anyone but only able to be decrypted by those whom have the private key.

Some of the openssl functions used:

| Function | Description | 
|---|---|
| `openssl_pkey_new` | Generates a new private key | 
| `openssl_public_encrypt` | Encrypts data with public key |
| `openssl_encrypt` | Encrypts data |
| `openssl_private_decrypt` | Decrypts data with private key |
| `openssl_decrypt` | Decrypts data |


## Usage

The `testit.sh` shell script will do the following:

1. `php generate_key_files.php` Generates asymmetric encryption key files
	a. `key.pub` is base64 of the public key
	b. `key.priv` is base64 of the private key
	
2. Encrypt data with `php encrypt.php` which will generate `encrypt.dat` file
3. Decrypt `encrypt.dat` by running `php decrypt.php`


## Encrypt Data `php encrypt.php`

The test code in the demo converts JSON data to a string then passes that to the `encryptMessage` method. The `encryptMessage` method uses two types of encryption because pure asymmetric encryption is slow and there is a limited number of bytes that can be encrypted this way.

For asymmetric encryption this way `For a 2048 bit key length => max number of chars (bytes) to encrypt = 2048/8 - 11(when padding used) = 245 chars (bytes).`. There are various ways to work around that constraint but this code demonstrates one such way.


How it works:

1. Generate a symmetric key
2. Encrypt data with symmetric key
3. Encrypt symmetric key with passed asymmetric public key
4. Base64 encrypted data
5. Pack output value with following format

|byte range | What |Description | 
|:-:|---|---|
| 0 - 3 | symmetric key length | This is how much pace the base64 encrypted key takes up in the output | 
| 4 - `symmetric key length` | symmetric key data | This is the encoded symmetric key data that encrypted the message | 
| (`symmetric key length`+3) - end | encrypted message |This is the encrypted message data | 


## Decrypt Data `php decrypt.php`

The `decryptMessage` method is essentially the reverse of the encrypt method but using the private key instead of the public key.

How it works:

1. Read first 3 byte to get encoded symmetric key length
2. read `symmetric key length` bytes
3. Decrypt symmetric key with passed in asymmetric private key
4. Read rest of content and use symmetric key to decrypt data
5. Un-Base64 data and return it



## Asymmetric Key Generation `generate_key_files.php`

	
There are a lot of ways to generate these keys but this is a simple demo of creating the asymmetric keys and writing them to a file. The keys are Base64 encoded so they are a little more portable and can be used as a single line string.


### Example Public Key (un-base64 encoded)

```
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsiWkp7UnK4FIrShszj+n
PEF7H1TL94Ty1cskW78AFK6veSwqyBZDlmxnEYwaub8D6FTBbcCnAxf9G0yIubhB
Yel6EK2nLitsM64nxUlrXgVNZSOrIoUojShkvzU+jOJpmsmvHMJc2fAaV/8JqBJs
4LT6K6QO1WjJsd8AP2d6Zimlqyu5KN2kkDGJ/hDhUZfZbIQD0zFCcGeoJrzblIFM
gapoda9S6Qt79QxWsUJrjRmU3amPFfxS4Jdh7fYZ9KiHWD1bdyGE1xlfEnzKauMD
kEbdCvkK8Yagi7wlGty/05no5UtGPJ5mm61kT/gi47IUuJpWCmakSXepYm7zDy8a
bQIDAQAB
-----END PUBLIC KEY-----
```

### Example Private Key (un-base64 encoded)

```
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCyJaSntScrgUit
KGzOP6c8QXsfVMv3hPLVyyRbvwAUrq95LCrIFkOWbGcRjBq5vwPoVMFtwKcDF/0b
TIi5uEFh6XoQracuK2wzrifFSWteBU1lI6sihSiNKGS/NT6M4mmaya8cwlzZ8BpX
/wmoEmzgtPorpA7VaMmx3wA/Z3pmKaWrK7ko3aSQMYn+EOFRl9lshAPTMUJwZ6gm
vNuUgUyBqmh1r1LpC3v1DFaxQmuNGZTdqY8V/FLgl2Ht9hn0qIdYPVt3IYTXGV8S
fMpq4wOQRt0K+QrxhqCLvCUa3L/TmejlS0Y8nmabrWRP+CLjshS4mlYKZqRJd6li
bvMPLxptAgMBAAECggEAWxeCfTrVH5rI9bnRARltQxNciKXMcfFqVkW8fqlIukqk
cTpTWeKAht9BSKiyVb5FmjwUeDFldOCETLwywXxk+lLvVq3k6WfMuRRZQ7kKDJZ7
f7bgYBNkq+E2usrYBCQVyc9NGlCMN+hdIBfJ8UYSpEgGD+CgsSEW2TvlHYalAK15
yIRcANIZp7JLDb9Gn8nqb4ZmUHJqqwVjHISE0O7VEh1LKDSNv/BQpAYZeRtjdwx9
8seFLu9bVeYQ8stx3omVysGrI1VlSlUMq7gjxpIO8/LT+EsV7YtjdbCbexyVXOlD
RCB78OU5tER6OtUGVGDqWkiKcqu6GYzUTH6LZFMXBQKBgQDpQQKNtQ5WpHJMmxnp
Czfydbx4RObHVKUN+B2qiJ3cyPaMFNw8vCQNH7akIs3AElbxxOVLiG9UCu5srv0r
EbnFtfUpBC37uQsa31cCREEsCTakQDP6yepO+UfwhfvSYsDApafL37rrmaddh4oU
nL6K29gH17E+isuzBtvMF1d2nwKBgQDDhO93XNCTlcZiafPAN6RXQBw2aNhhM0YB
KwLjqilphsT6eSKNACzwN5+uHdDJnuzMXqWxFtEgzfbTjNgf/+HOJu9E3BQ8D+Hb
9qs4loyILaWxUuDiOPjoHmucIlmaBB0TpHissAKm3nA5WFBYac64H9M4i/bxuZOt
nyNIjx6PcwKBgQDmADuzXfhAiTFHxpz7Bhvp0hzA/zgND7Mdni4qjUIUhnlOUfeF
UAPAiSgAm20E21CuX/e1zlfwqELIGpj7kiP8B9sx0bRCBgokOlxCmOkmsgMWXVSk
E9weeYJtcsCIiOYGUJKv3vIjBUVaXZ9Tief9ZqCTwyU8RYJtCvNLQSz2gwKBgEuf
Kw+7smCi2WxPFpwN6V5lyYOx4Z8WucjR3fg4ZHQQUDegiqCGpNr0aKprlcml0mjp
YqSv8osBnqoMG7ukuK2HmZvEk373laACNA9bC1fQb/m52IknB/6fZXeqQYW0eZqT
TqlcGmp4Nme3pLvNkMreZE/Gsuijol7Q4lC6KlPJAoGBALbn1UPZ2t1ofyTgmVrX
zVGm/XhwuVo3p2jd1rvrxCvAlCjK0Bt9GNezaeA55LiOVxE9WXtoPpZigJXr2N0l
mSiK4X/+QS00zBye5vyzHxnffWfHTcRjLDsEZlFN3/bgtyJANlzrw1OfhYTUf+8n
GoFAutVr9gCFlvnDFOxzxrSw
-----END PRIVATE KEY-----

```
