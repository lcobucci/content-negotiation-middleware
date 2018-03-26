<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use JsonSerializable;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\Json;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_UNESCAPED_SLASHES;
use function acos;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\Json
 */
final class JsonTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct()
     */
    public function constructorShouldAllowTheConfigurationOfEncodingFlags(): void
    {
        self::assertAttributeSame(JSON_UNESCAPED_SLASHES, 'flags', new Json(JSON_UNESCAPED_SLASHES));
    }

    /**
     * @test
     *
     * @covers ::__construct()
     */
    public function constructorShouldUseDefaultFlagsWhenNothingWasSet(): void
    {
        self::assertAttributeSame(
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES,
            'flags',
            new Json()
        );
    }

    /**
     * @test
     *
     * @covers ::format()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json::__construct()
     *
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
