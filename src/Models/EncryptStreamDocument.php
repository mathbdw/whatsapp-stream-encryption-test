<?php

namespace WhatsApp\Stream\Encryption\Models;

use WhatsApp\Stream\Encryption\Algorithms\WhatsAppV1\Algorithm;
use WhatsApp\Stream\Encryption\EncryptStream;

class EncryptStreamDocument extends EncryptStream
{
    public function exec(string $filenameKey = '')
    {
        $this->index(Algorithm::MEDIA_TYPE_DOCUMENT, $filenameKey);
    }
}