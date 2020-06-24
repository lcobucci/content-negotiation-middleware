<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\ContentOnly
 */
final class ContentOnlyTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::format
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     */
    public function formatShouldDelegateTheFormattingToTheConcreteClasses(): void
    {
        $formatter = $this->getMockForAbstractClass(ContentOnly::class);
        $formatter->method('formatContent')->willReturn('A fancy result');

        $response = $formatter->format(new UnformattedResponse(new Response(), 'testing'), new StreamFactory());

        self::assertSame('A fancy result', $response->getBody()->getContents());
    }
}
