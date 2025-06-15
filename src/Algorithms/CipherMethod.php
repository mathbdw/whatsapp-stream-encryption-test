<?php

namespace WhatsApp\StreamEncryption\Algorithms;

interface CipherMethod
{
    public function getAlgo(): string;

    /**
     * Execute encryption
     *
     * @param string $data Data/EncryptedData
     * @param bool   $eof End of stream flag
     *
     * @return string
     */
    public function execute(string $data, bool $eof): string;
}