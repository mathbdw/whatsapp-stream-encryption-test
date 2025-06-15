<?php

namespace WhatsApp\StreamEncryption\Algorithms\WhatsAppV1;

use WhatsApp\StreamEncryption\Algorithms\CipherMethod;
use WhatsApp\StreamEncryption\Exceptions\WhatsAppException;

class CbcDecrypt extends Cbc implements CipherMethod
{
    private string $providedMac;

    /**
     * Returns a string decrypted by aes-cbc
     *
     * @param string $data
     * @param bool   $eof
     *
     * @return string
     */
    public function execute(string $data, bool $eof = false): string
    {
        $providedMac = '';
        // Separate encrypted data and MAC
        if($eof) {
            $providedMac = substr($data, -10);
            $data = substr($data, 0, -10);
        }


        $decrypted = openssl_decrypt(
            $data,
            self::SSL_ALGO,
            $this->mediaKeyExpanded['cipherKey'],
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->mediaKeyExpanded['iv']
        );

        if ($decrypted === false) {
            throw new WhatsAppException('Decryption failed - ' . openssl_error_string() . PHP_EOL);
        }

        // Update IV for nuxt block (CBC)
        $this->mediaKeyExpanded['iv'] = substr($data, -self::BLOCK_SIZE);

        $this->hashUpdate($data);

        // Last block remove PKCS7 padding and hash equals
        if ($eof) {
            $pad = ord($decrypted[strlen($decrypted) - 1]);
            $decrypted = substr($decrypted, 0, -$pad);


            $calculatedMac = $this->getHash();
            if (!hash_equals($calculatedMac, $providedMac)) {
                throw new WhatsAppException('MAC verification failed' . PHP_EOL);
            }
        }

        return $decrypted;
    }
}