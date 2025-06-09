<?php

namespace WhatsApp\Stream\Encryption\Algorithms\WhatsAppV1;

use WhatsApp\Stream\Encryption\Exceptions\WhatsAppException;

class WhatsAppDecrypt extends Algorithm
{
    public function index(string $encryptedData): string
    {
        // Separate $encryptedData and MAC
        $enc = substr($encryptedData, 0, -10);
        $providedMac = substr($encryptedData, -10);

        // Verify MAC
        $calculatedMac = $this->generateHmac($enc);
        if (!hash_equals($calculatedMac, $providedMac)) {
            throw new WhatsAppException('MAC verification failed');
        }

        // Decrypt the data
        return $this->aesCbcDecrypt($enc);
    }

    /**
     * Returns string aesCbc decrypt, remove PKCS7 padding
     *
     * @param string $data
     *
     * @return string
     */
    private function aesCbcDecrypt(string $data): string
    {
        $decrypted = openssl_decrypt(
            $data,
            self::SSL_ALGO,
            $this->mediaKeyExpanded['cipherKey'],
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->mediaKeyExpanded['iv']
        );

        if ($decrypted === false) {
            throw new WhatsAppException('Decryption failed: ' . openssl_error_string());
        }

        $pad = ord($decrypted[strlen($decrypted) - 1]);
        $result = substr($decrypted, 0, -$pad);

        return $result;
    }
}