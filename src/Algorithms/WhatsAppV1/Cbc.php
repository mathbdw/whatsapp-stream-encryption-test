<?php

namespace WhatsApp\StreamEncryption\Algorithms\WhatsAppV1;

use WhatsApp\StreamEncryption\Exceptions\InvalidArgumentException;

class Cbc
{
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_AUDIO = 'audio';
    const MEDIA_TYPE_DOCUMENT = 'document';

    const HASH_HKDF_LENGTH = 112;
    const HASH_ALGO = 'sha256';
    const SSL_ALGO = 'aes-256-cbc';
    const BLOCK_SIZE = 16;

    public string $mediaKey;
    public array $mediaKeyExpanded;

    public string $appInfo;
    private \HashContext $ctxHash;

    public function setAppInfo(string $appInfo): void
    {
        $this->appInfo = $appInfo;
    }

    /**
     * Generate new mediaKey
     *
     * @return void
     * @throws \Random\RandomException
     */
    public function generateMediaKey(): void
    {
        $this->mediaKey = random_bytes(32);
    }

    /**
     *  Validate MediaKey
     *
     * @param string $mediaKey
     *
     * @return void
     */
    public function validateMediaKey(string $mediaKey) : void
    {
        if (!is_file($mediaKey)) {
            throw new InvalidArgumentException("The filename key does not exist.");
        }
        $data = file_get_contents($mediaKey);

        if (strlen($data) !== 32) {
            throw new InvalidArgumentException('Media key must be 32 bytes.');
        }

        $this->mediaKey = $data;
    }

    /**
     * Create mediaKeyExpanded and split to array
     *
     * @return void
     */
    public function setMediaKeyExpanded(): void
    {
        $mediaKeyExpanded = hash_hkdf(
            self::HASH_ALGO,
            $this->mediaKey,
            self::HASH_HKDF_LENGTH,
            self::getTypes()[$this->appInfo]
        );

        $this->mediaKeyExpanded = [
            'iv' => substr($mediaKeyExpanded, 0, 16),
            'cipherKey' => substr($mediaKeyExpanded, 16, 32),
            'macKey' => substr($mediaKeyExpanded, 48, 32),
            'refKey' => substr($mediaKeyExpanded, 80),
        ];


        $this->ctxHash = hash_init(self::HASH_ALGO, HASH_HMAC, $this->mediaKeyExpanded['macKey']);
        $this->hashUpdate($this->mediaKeyExpanded['iv']);
    }

    /**
     * Return list types
     *
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            self::MEDIA_TYPE_IMAGE => 'WhatsApp Image Keys',
            self::MEDIA_TYPE_VIDEO => 'WhatsApp Video Keys',
            self::MEDIA_TYPE_AUDIO => 'WhatsApp Audio Keys',
            self::MEDIA_TYPE_DOCUMENT => 'WhatsApp Document Keys',
        ];
    }

    /**
     * Hash update
     *
     * @param string $data
     *
     * @return void
     */
    protected function hashUpdate(string $data): void
    {
        hash_update($this->ctxHash, $data);
    }

    /**
     * Returns first 10 bytes of hash
     *
     * @return string
     */
    protected function getHash(): string
    {
        $hash = hash_final($this->ctxHash, true);

        return substr($hash, 0, 10);
    }
}