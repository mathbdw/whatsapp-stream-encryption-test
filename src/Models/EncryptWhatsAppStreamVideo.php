<?php

namespace WhatsApp\StreamEncryption\Models;

use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\EncryptWhatsAppStream;

class EncryptWhatsAppStreamVideo extends EncryptWhatsAppStream
{
    /**
     * Video encryption start point
     *
     * @param string $filenameKey
     *
     * @return void
     */
    public function exec(string $filenameKey = ''): void
    {
        $this->setAppInfo(Cbc::MEDIA_TYPE_VIDEO);
        parent::exec($filenameKey);
    }
}