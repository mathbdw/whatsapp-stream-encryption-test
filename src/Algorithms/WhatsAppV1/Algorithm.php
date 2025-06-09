<?php

namespace WhatsApp\Stream\Encryption\Algorithms\WhatsAppV1;

use WhatsApp\Stream\Encryption\Exceptions\InvalidArgumentEncrypteException;

class Algorithm
{
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_AUDIO = 'audio';
    const MEDIA_TYPE_DOCUMENT = 'document';

    const HASH_HKDF_LENGTH = 112;
    const HASH_ALGO = 'sha256';
    const SSL_ALGO = 'aes-256-cbc';

    public string $mediaKey;
    public array $mediaKeyExpanded;

    public string $appInfo;

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
     * Validate MediaKey
     *
     * @param string $mediaKey
     *
     * @return bool
     */
    public function validateMediaKey(string $mediaKey): bool
    {
        if (!is_file($mediaKey)) {
            throw new InvalidArgumentEncrypteException("The filename key does not exist.");
        }
        $data = file_get_contents($mediaKey);

        if (strlen($data) !== 32) {
            throw new InvalidArgumentEncrypteException('Media key must be 32 bytes.');
        }

        $this->mediaKey = $data;

        return true;
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
     * Return first 10 bytes generate HASH_HMAC
     *
     * @param string $encryptedData
     *
     * @return string
     */
    protected function generateHmac(string $encryptedData): string
    {
        $hash = hash_hmac(
            self::HASH_ALGO,
            $this->mediaKeyExpanded['iv'] . $encryptedData,
            $this->mediaKeyExpanded['macKey'],
            true
        );
        return substr($hash, 0, 10);
    }
}