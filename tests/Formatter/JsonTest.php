<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use JsonSerializable;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\Json;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function acos;

use const JSON_UNESCAPED_SLASHES;

/** @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\Json */
final class JsonTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::formatContent()
     */
    public function constructorShouldAllowTheConfigurationOfEncodingFlags(): void
    {
        self::assertSame(
            '["<foo>","\'bar\'","\"baz\"","&blong&","\u00e9","http://"]',
            (new Json(JSON_UNESCAPED_SLASHES))
                ->formatContent(['<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9", 'http://']),
        );
    }

    /**
     * @test
     *
     * @covers ::__construct()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::formatContent()
     */
    public function constructorShouldUseDefaultFlagsWhenNothingWasSet(): void
    {
        self::assertSame(
            '["\u003Cfoo\u003E","\u0027bar\u0027","\u0022baz\u0022","\u0026blong\u0026","\u00e9","http://"]',
            $this->formatContent(['<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9", 'http://']),
        );
    }

    /**
     * @test
     *
     * @covers ::formatContent()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     */
    public function formatShouldReturnAJsonEncodedValue(): void
    {
        self::assertJsonStringEqualsJsonString(
            '{"id":1,"name":"Test"}',
            $this->formatContent(new PersonDto(1, 'Test')),
        );
    }

    /**
     * @test
     *
     * @covers ::formatContent()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     */
    public function formatShouldRaiseExceptionWhenContentCouldNotBeEncoded(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('Inf and NaN cannot be JSON encoded');

        $this->formatContent(acos(8));
    }

    /**
     * @test
     *
     * @covers ::formatContent()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     */
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

    private function formatContent(mixed $content): string
    {
        $formatter = new Json();

        return $formatter->formatContent($content);
    }
}
