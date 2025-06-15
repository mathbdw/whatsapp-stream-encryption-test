<h1>Installation</h1>

<pre>composer require mathbdw/whatsapp-stream-encryption-test</pre>

<h1>Usage</h1>
<h2>Encrypt stream</h2>
<pre>
  
$pathIn = './static/IMAGE.original';
$pathOut = './static/IMAGE.encrypted';
$key = './samples/IMAGE.key';

$encryption = new \WhatsApp\Stream\Encryption\Models\EncryptStreamImage($stream1, $stream2);
$encryption->exec($key);
</pre>

The key is not required. If there is no key after encryption, the key file will be added to the output file folder.
<pre>
$pathIn = './static/IMAGE.original';
$pathOut = './static/IMAGE.encrypted';

$encryption = new \WhatsApp\Stream\Encryption\Models\EncryptStreamImage($pathIn, $pathOut);
$encryption->exec();
</pre>


<h2>Decrypt stream</h2>
<pre>
$pathIn = './static/IMAGE.encrypted';
$pathOut = './static/IMAGE.original';
$key = './samples/IMAGE.key';

$enStream = new \WhatsApp\Stream\Encryption\Models\DecryptStreamImage($pathIn, $pathOut);
$enStream->exec($key);
</pre>

Type file encryption
<pre>
  //Video
  $enStream = new \WhatsApp\Stream\Encryption\Models\EncryptStreamVideo($pathIn, $pathOut);
  $deStream = new \WhatsApp\Stream\Encryption\Models\DecryptStreamVideo($pathIn, $pathOut);
  //Audio
  $enStream = new \WhatsApp\Stream\Encryption\Models\EncryptStreamAudio($pathIn, $pathOut);
  $deStream = new \WhatsApp\Stream\Encryption\Models\DecryptStreamAudio($pathIn, $pathOut);
  //Image
  $enStream = new \WhatsApp\Stream\Encryption\Models\EncryptStreamImage($pathIn, $pathOut);
  $deStream = new \WhatsApp\Stream\Encryption\Models\DecryptStreamImage($pathIn, $pathOut);
  //Document
  $enStream = new \WhatsApp\Stream\Encryption\Models\EncryptStreamDocument($pathIn, $pathOut);
  $deStream = new \WhatsApp\Stream\Encryption\Models\DecryptStreamDocument($pathIn, $pathOut);
</pre>
