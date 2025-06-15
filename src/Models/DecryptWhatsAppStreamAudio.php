<?php

namespace WhatsApp\StreamEncryption\Models;

use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\Cbc;
use WhatsApp\StreamEncryption\DecryptWhatsAppStream;

class DecryptWhatsAppStreamAudio extends DecryptWhatsAppStream
{
    /**
     * Audio decryption start point
     *
     * @param string $filenameKey Path to file Key
     *
     * @return void
     */
    public function exec(string $filenameKey): void
    {
        $this->setAppInfo(Cbc::MEDIA_TYPE_AUDIO);
        parent::exec($filenameKey);
    }
}