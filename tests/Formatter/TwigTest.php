<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\Twig;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function dirname;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\Twig
 */
final class TwigTest extends TestCase
{
    private Environment $environment;

    /**
     * @before
     */
    public function configureEngine(): void
    {
        $this->environment = new Environment(
            new FilesystemLoader('templates/twig', dirname(__DIR__, 2) . '/')
        );
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::format()
     * @covers ::render()
     */
    public function formatShouldReturnContentFormattedByPlates(): void
    {
        $formatter = new Twig($this->environment);
        $content   = $formatter->format(new PersonDto(1, 'Testing'), ['template' => 'person.twig']);

        self::assertStringContainsString('<dd>1</dd>', $content);
        self::assertStringContainsString('<dd>Testing</dd>', $content);
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::format()
     * @covers ::render()
     */
    public function formatShouldReadTemplateNameFromCustomAttribute(): void
    {
        $formatter = new Twig($this->environment, 'fancy!');
        $content   = $formatter->format(new PersonDto(1, 'Testing'), ['fancy!' => 'person.twig']);

        self::assertStringContainsString('<dd>1</dd>', $content);
        self::assertStringContainsString('<dd>Testing</dd>', $content);
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::format()
     * @covers ::render()
     */
    public function formatShouldConvertAnyTwigException(): void
    {
        $formatter = new Twig($this->environment);

        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('An error occurred while formatting using twig');

        $formatter->format(new PersonDto(1, 'Testing'), ['template' => 'no-template-at-all']);
    }
}
