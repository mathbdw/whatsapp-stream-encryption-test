<?php

namespace WhatsApp\StreamEncryption\Models;

use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\DecryptWhatsAppStream;

class DecryptWhatsAppStreamImage extends DecryptWhatsAppStream
{
    /**
     * Image decryption start point
     *
     * @param string $filenameKey Path to file Key
     *
     * @return void
     */
    public function exec(string $filenameKey): void
    {
        $this->setAppInfo(Cbc::MEDIA_TYPE_IMAGE);
        parent::exec($filenameKey);
    }
}