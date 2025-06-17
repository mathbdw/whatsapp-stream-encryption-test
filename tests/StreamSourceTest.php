<?php

namespace WhatsApp\StreamEncryption\Tests;

use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use WhatsApp\StreamEncryption\Algorithms\WhatsAppV1\CbcEncrypt;
use WhatsApp\StreamEncryption\StreamSource;


#[CoversClass(StreamSource::class)] class StreamSourceTest extends TestCase
{
    private string|false $tempFileRead;
    private StreamInterface $source;

    /**
     * @throws Exception
     */
    public function testRead()
    {
        $algo = $this->createMock(CbcEncrypt::class);
        $algo->expects($this->once())->method('execute')->willReturn('Data file.');

        $sSource = new StreamSource($this->source, $algo);
        $res = $sSource->read(1024);

        $this->assertInstanceOf(StreamSource::class, $sSource);
        $this->assertEquals('Data file.', $res);
    }

    protected function setUp(): void
    {
        $this->tempFileRead = tempnam(sys_get_temp_dir(), 'testRead');
        file_put_contents($this->tempFileRead, 'Data file.');
        $this->source = new Stream(fopen($this->tempFileRead, 'rb'));
    }

    protected function tearDown(): void
    {
        $this->source->close();

        if (file_exists($this->tempFileRead)) {
            unlink($this->tempFileRead);
        }
    }
}