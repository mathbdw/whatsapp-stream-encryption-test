<?php

namespace WhatsApp\StreamEncryption\Models;

use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\EncryptWhatsAppStream;

class EncryptWhatsAppStreamAudio extends EncryptWhatsAppStream
{
    /**
     * Audio encryption start point
     *
     * @param string $filenameKey
     *
     * @return void
     */
    public function exec(string $filenameKey = ''): void
    {
        $this->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        parent::exec($filenameKey);
    }
}