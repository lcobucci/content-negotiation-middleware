<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Attributes as PHPUnit;
use PHPUnit\Framework\TestCase;

#[PHPUnit\CoversClass(UnformattedResponse::class)]
final class UnformattedResponseTest extends TestCase
{
    #[PHPUnit\Test]
    public function getUnformattedContentShouldReturnTheConfiguredValue(): void
    {
        $dto      = new PersonDto(1, 'Testing');
        $response = new UnformattedResponse(new Response(), $dto);

        self::assertSame($dto, $response->getUnformattedContent());
    }

    #[PHPUnit\Test]
    public function withAttributeShouldReturnANewInstanceWithTheAddedAttribute(): void
    {
        $response1 = new UnformattedResponse(new Response(), new PersonDto(1, 'Testing'));
        $response2 = $response1->withAttribute('test', 1);

        self::assertSame([], $response1->getAttributes());
        self::assertSame(['test' => 1], $response2->getAttributes());
    }

    #[PHPUnit\Test]
    public function withAttributeShouldOverrideExistingAttributes(): void
    {
        $response = new UnformattedResponse(
            new Response(),
            new PersonDto(1, 'Testing'),
            ['test' => 1],
        );

        self::assertSame(['test' => 2], $response->withAttribute('test', 2)->getAttributes());
    }

    #[PHPUnit\Test]
    public function getAttributesShouldReturnTheConfiguredAttributes(): void
    {
        $response = new UnformattedResponse(
            new Response(),
            new PersonDto(1, 'Testing'),
            ['test' => 1],
        );

        self::assertSame(['test' => 1], $response->getAttributes());
    }

    #[PHPUnit\Test]
    public function getProtocolVersionShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getProtocolVersion');
    }

    #[PHPUnit\Test]
    public function getHeadersShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getHeaders');
    }

    #[PHPUnit\Test]
    public function hasHeaderShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('hasHeader', 'Content-Type');
    }

    #[PHPUnit\Test]
    public function getHeaderShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getHeader', 'Content-Type');
    }

    #[PHPUnit\Test]
    public function getHeaderLineShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getHeaderLine', 'Content-Type');
    }

    #[PHPUnit\Test]
    public function getBodyShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getBody');
    }

    #[PHPUnit\Test]
    public function getStatusCodeShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getStatusCode');
    }

    #[PHPUnit\Test]
    public function getReasonPhraseShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getReasonPhrase');
    }

    private function assertGetterReturn(string $method, mixed ...$arguments): void
    {
        $decoratedResponse = new Response();
        $response          = new UnformattedResponse($decoratedResponse, new PersonDto(1, 'Testing'));

        self::assertSame($decoratedResponse->$method(...$arguments), $response->$method(...$arguments));
    }

    #[PHPUnit\Test]
    public function withProtocolVersionShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withProtocolVersion', '2');
    }

    #[PHPUnit\Test]
    public function withHeaderShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withHeader', 'Content-Type', 'application/json');
    }

    #[PHPUnit\Test]
    public function withAddedHeaderShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withAddedHeader', 'Content-Type', 'application/json');
    }

    #[PHPUnit\Test]
    public function withoutHeaderShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withoutHeader', 'Content-Type');
    }

    #[PHPUnit\Test]
    public function withBodyShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withBody', new Stream('php://temp', 'wb+'));
    }

    #[PHPUnit\Test]
    public function withStatusShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withStatus', 202);
    }

    private function assertSetterReturn(string $method, mixed ...$arguments): void
    {
        $decoratedResponse = new Response();
        $dto               = new PersonDto(1, 'Testing');

        $response = new UnformattedResponse($decoratedResponse, $dto, ['test' => 1]);
        $expected = new UnformattedResponse(
            $decoratedResponse->$method(...$arguments),
            $dto,
            ['test' => 1],
        );

        self::assertEquals($expected, $response->$method(...$arguments));
    }
}
