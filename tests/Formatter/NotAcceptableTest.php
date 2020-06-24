<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\Formatter\NotAcceptable;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\Formatter\NotAcceptable
 */
final class NotAcceptableTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::format
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     */
    public function formatShouldReturnAResponseWithEmptyBodyAndTheCorrectStatusCode(): void
    {
        $formatter = new NotAcceptable();
        $response  = $formatter->format(new UnformattedResponse(new Response(), 'testing'), new StreamFactory());

        self::assertSame('', $response->getBody()->getContents());
        self::assertSame(StatusCodeInterface::STATUS_NOT_ACCEPTABLE, $response->getStatusCode());
    }
}
