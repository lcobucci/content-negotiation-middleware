<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests;

use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPStan\Testing\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\UnformattedResponse
 */
final class UnformattedResponseTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getUnformattedContent()
     */
    public function getUnformattedContentShouldReturnTheConfiguredValue(): void
    {
        $dto      = new PersonDto(1, 'Testing');
        $response = new UnformattedResponse(new Response(), $dto);

        self::assertSame($dto, $response->getUnformattedContent());
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getProtocolVersion()
     */
    public function getProtocolVersionShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getProtocolVersion');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getHeaders()
     */
    public function getHeadersShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getHeaders');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::hasHeader()
     */
    public function hasHeaderShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('hasHeader', 'Content-Type');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getHeader()
     */
    public function getHeaderShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getHeader', 'Content-Type');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getHeaderLine()
     */
    public function getHeaderLineShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getHeaderLine', 'Content-Type');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getBody()
     */
    public function getBodyShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getBody');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getStatusCode()
     */
    public function getStatusCodeShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getStatusCode');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getReasonPhrase()
     */
    public function getReasonPhraseShouldReturnTheSameValueAsTheDecoratedObject(): void
    {
        $this->assertGetterReturn('getReasonPhrase');
    }

    /**
     * @param mixed ...$arguments
     */
    private function assertGetterReturn(string $method, ...$arguments): void
    {
        $decoratedResponse = new Response();
        $response          = new UnformattedResponse($decoratedResponse, new PersonDto(1, 'Testing'));

        self::assertSame($decoratedResponse->$method(...$arguments), $response->$method(...$arguments));
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::withProtocolVersion()
     */
    public function withProtocolVersionShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withProtocolVersion', '2');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::withHeader()
     */
    public function withHeaderShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withHeader', 'Content-Type', 'application/json');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::withAddedHeader()
     */
    public function withAddedHeaderShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withAddedHeader', 'Content-Type', 'application/json');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::withoutHeader()
     */
    public function withoutHeaderShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withoutHeader', 'Content-Type');
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::withBody()
     */
    public function withBodyShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withBody', new Stream('php://temp', 'wb+'));
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::withStatus()
     */
    public function withStatusShouldReturnANewInstanceWithTheModifiedDecoratedObject(): void
    {
        $this->assertSetterReturn('withStatus', 202);
    }

    /**
     * @param mixed ...$arguments
     */
    private function assertSetterReturn(string $method, ...$arguments): void
    {
        $decoratedResponse = new Response();
        $dto               = new PersonDto(1, 'Testing');

        $response = new UnformattedResponse($decoratedResponse, $dto);
        $expected = new UnformattedResponse(
            $decoratedResponse->$method(...$arguments),
            $dto
        );

        self::assertEquals($expected, $response->$method(...$arguments));
    }
}
