<?php

namespace WhatsApp\Stream\Encryption\Algorithms\WhatsAppV1;

use WhatsApp\Stream\Encryption\Exceptions\WhatsAppException;

class WhatsAppEncrypt extends Algorithm
{
    /**
     * Returns encrypted data
     *
     * @param string $data
     * @param string $mediakey
     *
     * @return string
     */
    public function index(string $data): string
    {
        //Encrypt AES-CBC
        $enc = $this->aesCbcEncrypt($data);

        //Generate MAC
        $mac = $this->generateHmac($enc);

        return $enc . $mac;
    }

    /**
     * Returns string aesCbc encrypt, pad PKCS7
     *
     * @param string $data
     *
     * @return string
     */
    private function aesCbcEncrypt(string $data): string
    {
        $blockSize = 16;
        $pad = $blockSize - (strlen($data) % $blockSize);
        $data = $data . str_repeat(chr($pad), $pad);

        $encrypted = openssl_encrypt(
            $data,
            self::SSL_ALGO,
            $this->mediaKeyExpanded['cipherKey'],
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->mediaKeyExpanded['iv']
        );

        if ($encrypted === false) {
            throw new WhatsAppException('Encryption failed: ' . openssl_error_string());
        }

        return $encrypted;
    }
}