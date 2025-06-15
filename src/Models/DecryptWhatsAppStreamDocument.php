<?php

namespace WhatsApp\StreamEncryption\Models;

use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\DecryptWhatsAppStream;

class DecryptWhatsAppStreamDocument extends DecryptWhatsAppStream
{
    /**
     * Document decryption start point
     *
     * @param string $filenameKey Path to file Key
     *
     * @return void
     */
    public function exec(string $filenameKey): void
    {
        $this->setAppInfo(Cbc::MEDIA_TYPE_DOCUMENT);
        parent::exec($filenameKey);
    }
}