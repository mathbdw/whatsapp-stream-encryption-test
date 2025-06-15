<?php

namespace WhatsApp\StreamEncryption\Algorithms\WhatsAppV1;

use WhatsApp\StreamEncryption\Algorithms\CipherMethod;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;

class CbcEncrypt extends Cbc implements CipherMethod
{
    /**
     * Returns a string encrypted by aes-cbc
     *
     * @param string $data
     * @param bool   $eof
     *
     * @return string
     */
    public function execute(string $data, bool $eof = false): string
    {
        // Last block PKCS7 padding
        if ($eof) {
            $pad = self::BLOCK_SIZE - (strlen($data) % self::BLOCK_SIZE);
            $data .= str_repeat(chr($pad), $pad);
        }

        $encrypted = openssl_encrypt(
            $data,
            self::SSL_ALGO,
            $this->mediaKeyExpanded['cipherKey'],
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->mediaKeyExpanded['iv']
        );

        if ($encrypted === false) {
            throw new WhatsAppException('Encryption failed - ' . openssl_error_string() . PHP_EOL);
        }

        // Update IV for nuxt block (CBC)
        $this->mediaKeyExpanded['iv'] = substr($encrypted, -self::BLOCK_SIZE);

        $this->hashUpdate($encrypted);

        if($eof) {
            $hash = $this->getHash();
            $encrypted .= $hash;
        }

        return $encrypted;
    }
}