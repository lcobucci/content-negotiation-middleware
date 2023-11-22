<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Lcobucci\ContentNegotiation\Formatter\StringCast;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;
use Stringable;

#[PHPUnit\CoversClass(StringCast::class)]
#[PHPUnit\CoversClass(ContentOnly::class)]
#[PHPUnit\UsesClass(UnformattedResponse::class)]
final class StringCastTest extends TestCase
{
    #[PHPUnit\Test]
    #[PHPUnit\DataProvider('validData')]
    public function formatShouldSimplyReturnTheStringRepresentationOfTheContent(
        string $expected,
        mixed $content,
    ): void {
        self::assertSame($expected, $this->format($content));
    }

    /** @return iterable<array{string, mixed}> */
    public static function validData(): iterable
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

        yield ['test', 'test'];
        yield ['test', $test];
        yield ['test2', $test2];
        yield ['1', 1];
        yield ['1.1', 1.1];
        yield ['1', true];
        yield ['', false];
        yield ['', null];
    }

    #[PHPUnit\Test]
    public function formatShouldRaiseExceptionWhenContentCouldNotBeCastToString(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);

        $this->format(new class () {
        });
    }

    #[PHPUnit\Test]
    public function formatShouldRaiseExceptionWhenContentIsAnArray(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);

        $this->format([]);
    }

    private function format(mixed $content): string
    {
        $formatter = new StringCast();

        return (string) $formatter->format(
            new UnformattedResponse(new Response(), $content),
            new StreamFactory(),
        )->getBody();
    }
}
