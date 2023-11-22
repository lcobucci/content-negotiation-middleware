<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\StreamFactory;
use Lcobucci\ContentNegotiation\Formatter\NotAcceptable;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;

#[PHPUnit\CoversClass(NotAcceptable::class)]
#[PHPUnit\UsesClass(UnformattedResponse::class)]
final class NotAcceptableTest extends TestCase
{
    #[PHPUnit\Test]
    public function formatShouldReturnAResponseWithEmptyBodyAndTheCorrectStatusCode(): void
    {
        $formatter = new NotAcceptable();
        $response  = $formatter->format(new UnformattedResponse(new Response(), 'testing'), new StreamFactory());

        self::assertSame('', (string) $response->getBody());
        self::assertSame(StatusCodeInterface::STATUS_NOT_ACCEPTABLE, $response->getStatusCode());
    }
}
