<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Lcobucci\ContentNegotiation\Formatter\Twig;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function dirname;

#[PHPUnit\CoversClass(Twig::class)]
#[PHPUnit\CoversClass(ContentOnly::class)]
#[PHPUnit\UsesClass(UnformattedResponse::class)]
final class TwigTest extends TestCase
{
    #[PHPUnit\Test]
    public function formatShouldReturnContentFormattedByPlates(): void
    {
        $content = $this->format(new PersonDto(1, 'Testing'), ['template' => 'person.twig']);

        self::assertStringContainsString('<dd>1</dd>', $content);
        self::assertStringContainsString('<dd>Testing</dd>', $content);
    }

    #[PHPUnit\Test]
    public function formatShouldReadTemplateNameFromCustomAttribute(): void
    {
        $content = $this->format(
            new PersonDto(1, 'Testing'),
            ['fancy!' => 'person.twig'],
            'fancy!',
        );

        self::assertStringContainsString('<dd>1</dd>', $content);
        self::assertStringContainsString('<dd>Testing</dd>', $content);
    }

    #[PHPUnit\Test]
    public function formatShouldConvertAnyTwigException(): void
    {
        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('An error occurred while formatting using twig');

        $this->format(new PersonDto(1, 'Testing'), ['template' => 'no-template-at-all']);
    }

    /** @param array<string, mixed> $attributes */
    private function format(
        mixed $content,
        array $attributes = [],
        string $templateAttribute = 'template',
    ): string {
        $formatter = new Twig(
            new Environment(
                new FilesystemLoader('templates/twig', dirname(__DIR__, 2) . '/'),
            ),
            $templateAttribute,
        );

        return (string) $formatter->format(
            new UnformattedResponse(new Response(), $content, $attributes),
            new StreamFactory(),
        )->getBody();
    }
}
