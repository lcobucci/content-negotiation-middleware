<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter\Plates;
use Lcobucci\ContentNegotiation\Tests\PersonDto;
use League\Plates\Engine;
use PHPUnit\Framework\TestCase;
use function dirname;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\Plates
 */
final class PlatesTest extends TestCase
{
    private Engine $engine;

    /**
     * @before
     */
    public function configureEngine(): void
    {
        $this->engine = new Engine(dirname(__DIR__, 2) . '/templates/plates');
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
        $formatter = new Plates($this->engine);
        $content   = $formatter->format(new PersonDto(1, 'Testing'), ['template' => 'person']);

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
        $formatter = new Plates($this->engine, 'fancy!');
        $content   = $formatter->format(new PersonDto(1, 'Testing'), ['fancy!' => 'person']);

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
    public function formatShouldConvertAnyPlatesException(): void
    {
        $formatter = new Plates($this->engine);

        $this->expectException(ContentCouldNotBeFormatted::class);
        $this->expectExceptionMessage('An error occurred while formatting using plates');

        $formatter->format(new PersonDto(1, 'Testing'), ['template' => 'no-template-at-all']);
    }
}
