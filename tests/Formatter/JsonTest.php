<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use JsonSerializable;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Lcobucci\ContentNegotiation\Formatter\Json;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function acos;

use const JSON_UNESCAPED_SLASHES;

#[PHPUnit\CoversClass(Json::class)]
#[PHPUnit\CoversClass(ContentOnly::class)]
#[PHPUnit\UsesClass(UnformattedResponse::class)]
final class JsonTest extends TestCase
{
    #[PHPUnit\Test]
    public function constructorShouldAllowTheConfigurationOfEncodingFlags(): void
    {
        self::assertSame(
            '["<foo>","\'bar\'","\"baz\"","&blong&","\u00e9","http://"]',
            $this->formatContent(
                ['<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9", 'http://'],
                new Json(JSON_UNESCAPED_SLASHES),
            ),
        );
    }

    #[PHPUnit\Test]
    public function constructorShouldUseDefaultFlagsWhenNothingWasSet(): void
    {
        self::assertSame(
            '["\u003Cfoo\u003E","\u0027bar\u0027","\u0022baz\u0022","\u0026blong\u0026","\u00e9","http://"]',
            $this->formatContent(['<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9", 'http://']),
        );
    }

    #[PHPUnit\Test]
    public function formatShouldReturnAJsonEncodedValue(): void
    {
        self::assertJsonStringEqualsJsonString(
            '{"id":1,"name":"Test"}',
            $this->formatContent(new PersonDto(1, 'Test')),
        );
    }

    #[PHPUnit\Test]
    public function formatShouldRaiseExceptionWhenContentCouldNotBeEncoded(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('Inf and NaN cannot be JSON encoded');

        $this->formatContent(acos(8));
    }

    #[PHPUnit\Test]
    public function formatShouldConvertAnyExceptionDuringJsonSerialization(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('An exception was thrown during JSON formatting');

        $this->formatContent(
            new class implements JsonSerializable
            {
                public function jsonSerialize(): mixed
                {
                    throw new RuntimeException('This should be converted');
                }
            },
        );
    }

    private function formatContent(mixed $content, Json $formatter = new Json()): string
    {
        $formatted = $formatter->format(
            new UnformattedResponse(new Response(), $content),
            new StreamFactory(),
        );

        return (string) $formatted->getBody();
    }
}
