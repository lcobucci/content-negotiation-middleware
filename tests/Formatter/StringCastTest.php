<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\StringCast;
use PHPUnit\Framework\TestCase;
use Stringable;

/** @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\StringCast */
final class StringCastTest extends TestCase
{
    /**
     * @test
     * @dataProvider validData
     *
     * @covers ::formatContent()
     */
    public function formatShouldSimplyReturnTheStringRepresentationOfTheContent(
        string $expected,
        mixed $content,
    ): void {
        $formatter = new StringCast();

        self::assertSame($expected, $formatter->formatContent($content));
    }

    /** @return mixed[][] */
    public function validData(): array
    {
        $test = new class
        {
            public function __toString(): string
            {
                return 'test';
            }
        };

        $test2 = new class implements Stringable
        {
            public function __toString(): string
            {
                return 'test2';
            }
        };

        return [
            ['test',  'test'],
            ['test',  $test],
            ['test2',  $test2],
            ['1',  1],
            ['1.1',  1.1],
            ['1',  true],
            ['',  false],
            ['',  null],
        ];
    }

    /**
     * @test
     *
     * @covers ::formatContent()
     */
    public function formatShouldRaiseExceptionWhenContentCouldNotBeCastToString(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);

        $content = new class
        {
        };

        $formatter = new StringCast();
        $formatter->formatContent($content);
    }

    /**
     * @test
     *
     * @covers ::formatContent()
     */
    public function formatShouldRaiseExceptionWhenContentIsAnArray(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);

        $formatter = new StringCast();
        $formatter->formatContent([]);
    }
}
