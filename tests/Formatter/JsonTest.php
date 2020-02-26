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

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\Json
 */
final class JsonTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::format()
     */
    public function constructorShouldAllowTheConfigurationOfEncodingFlags(): void
    {
        self::assertSame(
            '["<foo>","\'bar\'","\"baz\"","&blong&","\u00e9","http://"]',
            (new Json(JSON_UNESCAPED_SLASHES))->format(['<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9", 'http://'])
        );
    }

    /**
     * @test
     *
     * @covers ::__construct()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::format()
     */
    public function constructorShouldUseDefaultFlagsWhenNothingWasSet(): void
    {
        self::assertSame(
            '["\u003Cfoo\u003E","\u0027bar\u0027","\u0022baz\u0022","\u0026blong\u0026","\u00e9","http://"]',
            $this->format(['<foo>', "'bar'", '"baz"', '&blong&', "\xc3\xa9", 'http://'])
        );
    }

    /**
     * @test
     *
     * @covers ::format()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     */
    public function formatShouldReturnAJsonEncodedValue(): void
    {
        self::assertJsonStringEqualsJsonString(
            '{"id":1,"name":"Test"}',
            $this->format(new PersonDto(1, 'Test'))
        );
    }

    /**
     * @test
     *
     * @covers ::format()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     */
    public function formatShouldRaiseExceptionWhenContentCouldNotBeEncoded(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('Inf and NaN cannot be JSON encoded');

        $this->format(acos(8));
    }

    /**
     * @test
     *
     * @covers ::format()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     */
    public function formatShouldConvertAnyExceptionDuringJsonSerialization(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('An exception was thrown during JSON formatting');

        $this->format(
            new class implements JsonSerializable
            {
                public function jsonSerialize(): void
                {
                    throw new RuntimeException('This should be converted');
                }
            }
        );
    }

    /**
     * @param mixed $content
     */
    private function format($content): string
    {
        $formatter = new Json();

        return $formatter->format($content);
    }
}
