<h1>Installation</h1>

<pre>composer require mathbdw/whatsapp-stream-encryption-test</pre>

<h1>Usage</h1>
<h2>Encrypt stream</h2>
<pre>
  
$pathIn = './static/IMAGE.original';
$pathOut = './static/IMAGE.encrypted';
$key = './samples/IMAGE.key';

$encryption = new \WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamImage($pathIn, $pathOut);
$encryption->exec($key);
</pre>

The key is not required. If there is no key after encryption, the key file will be added to the output file folder.
<pre>
$pathIn = './static/IMAGE.original';
$pathOut = './static/IMAGE.encrypted';

$encryption = new \WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamImage($pathIn, $pathOut);
$encryption->exec();
</pre>


<h2>Decrypt stream</h2>
<pre>
$pathIn = './static/IMAGE.encrypted';
$pathOut = './static/IMAGE.original';
$key = './samples/IMAGE.key';

$decryption = new \WhatsApp\StreamEncryption\Models\DecryptWhatsAppStreamImage($pathIn, $pathOut);
$decryption->exec($key);
</pre>

Type file encryption
<pre>
  //Video
  $enStream = new \WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamImage($pathIn, $pathOut);
  $deStream = new \WhatsApp\StreamEncryption\Models\DecryptWhatsAppStreamImage($pathIn, $pathOut);
  //Audio
  $enStream = new \WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamAudio($pathIn, $pathOut);
  $deStream = new \WhatsApp\StreamEncryption\Models\DecryptWhatsAppStreamAudio($pathIn, $pathOut);
  //Image
  $enStream = new \WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamImage($pathIn, $pathOut);
  $deStream = new \WhatsApp\StreamEncryption\Models\DecryptWhatsAppStreamImage($pathIn, $pathOut);
  //Document
  $enStream = new \WhatsApp\StreamEncryption\Models\EncryptWhatsAppStreamDocument($pathIn, $pathOut);
  $deStream = new \WhatsApp\StreamEncryption\Models\DecryptWhatsAppStreamDocument($pathIn, $pathOut);
</pre>
