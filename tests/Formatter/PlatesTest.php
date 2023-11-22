<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Lcobucci\ContentNegotiation\Formatter\Plates;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use League\Plates\Engine;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;

use function dirname;

#[PHPUnit\CoversClass(Plates::class)]
#[PHPUnit\CoversClass(ContentOnly::class)]
#[PHPUnit\UsesClass(UnformattedResponse::class)]
final class PlatesTest extends TestCase
{
    #[PHPUnit\Test]
    public function formatShouldReturnContentFormattedByPlates(): void
    {
        $content = $this->format(
            new PersonDto(1, 'Testing'),
            ['template' => 'person'],
        );

        self::assertStringContainsString('<dd>1</dd>', $content);
        self::assertStringContainsString('<dd>Testing</dd>', $content);
    }

    #[PHPUnit\Test]
    public function formatShouldReadTemplateNameFromCustomAttribute(): void
    {
        $content = $this->format(
            new PersonDto(1, 'Testing'),
            ['fancy!' => 'person'],
            'fancy!',
        );

        self::assertStringContainsString('<dd>1</dd>', $content);
        self::assertStringContainsString('<dd>Testing</dd>', $content);
    }

    #[PHPUnit\Test]
    public function formatShouldConvertAnyPlatesException(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('An error occurred while formatting using plates');

        $this->format(new PersonDto(1, 'Testing'), ['template' => 'no-template-at-all']);
    }

    /** @param array<string, mixed> $attributes */
    private function format(
        mixed $content,
        array $attributes = [],
        string $templateAttribute = 'template',
    ): string {
        $formatter = new Plates(
            new Engine(dirname(__DIR__, 2) . '/templates/plates'),
            $templateAttribute,
        );

        return (string) $formatter->format(
            new UnformattedResponse(new Response(), $content, $attributes),
            new StreamFactory(),
        )->getBody();
    }
}
