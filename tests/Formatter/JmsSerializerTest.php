<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use JMS\Serializer\SerializerInterface;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\JmsSerializer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/** @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\JmsSerializer */
final class JmsSerializerTest extends TestCase
{
    /** @var SerializerInterface&MockObject */
    private SerializerInterface $serializer;

    /** @before */
    public function createSerializer(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::formatContent()
     */
    public function formatShouldSimplyForwardCallToSerializer(): void
    {
        $content = ['a' => 'test'];

        $this->serializer->expects(self::once())
                         ->method('serialize')
                         ->with($content, 'json')
                         ->willReturn('{"a":"test"}');

        $formatter = new JmsSerializer($this->serializer, 'json');

        self::assertSame('{"a":"test"}', $formatter->formatContent($content));
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::formatContent()
     */
    public function formatShouldConvertAnyRaisedException(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);

        $this->serializer->method('serialize')
                         ->willThrowException(new RuntimeException());

        $formatter = new JmsSerializer($this->serializer, 'json');
        $formatter->formatContent(['a' => 'test']);
    }
}
