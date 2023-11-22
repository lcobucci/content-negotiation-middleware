<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use JMS\Serializer\SerializerInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Lcobucci\ContentNegotiation\Formatter\JmsSerializer;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[PHPUnit\CoversClass(JmsSerializer::class)]
#[PHPUnit\CoversClass(ContentOnly::class)]
#[PHPUnit\UsesClass(UnformattedResponse::class)]
final class JmsSerializerTest extends TestCase
{
    #[PHPUnit\Test]
    public function formatShouldSimplyForwardCallToSerializer(): void
    {
        $content = ['a' => 'test'];

        $jms = $this->createMock(SerializerInterface::class);
        $jms->expects(self::once())
            ->method('serialize')
            ->with($content, 'json')
            ->willReturn('{"a":"test"}');

        self::assertSame('{"a":"test"}', $this->format($jms, $content));
    }

    #[PHPUnit\Test]
    public function formatShouldConvertAnyRaisedException(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);

        $jms = $this->createMock(SerializerInterface::class);
        $jms->method('serialize')
            ->willThrowException(new RuntimeException());

        $this->format($jms, ['a' => 'test']);
    }

    private function format(SerializerInterface $jms, mixed $content): string
    {
        $formatter = new JmsSerializer($jms, 'json');
        $formatted = $formatter->format(
            new UnformattedResponse(new Response(), $content),
            new StreamFactory(),
        );

        return (string) $formatted->getBody();
    }
}
