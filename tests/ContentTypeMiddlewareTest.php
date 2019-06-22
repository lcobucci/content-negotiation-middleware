<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests;

use AssertionError;
use Fig\Http\Message\StatusCodeInterface;
use Lcobucci\ContentNegotiation\ContentTypeMiddleware;
use Lcobucci\ContentNegotiation\Formatter;
use Lcobucci\ContentNegotiation\Tests\Formatter\NaiveTemplateEngine;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\ServerRequest;
use function array_map;
use function ini_set;

/**
 * @coversDefaultClass \Lcobucci\ContentNegotiation\ContentTypeMiddleware
 */
final class ContentTypeMiddlewareTest extends TestCase
{
    private const SUPPORTED_FORMATS = [
        'json' => [
            'extension' => ['json'],
            'mime-type' => ['application/json', 'text/json', 'application/x-json'],
        ],
        'txt'  => [
            'extension' => ['txt'],
            'mime-type' => ['text/plain'],
        ],
        'html' => [
            'extension' => ['html', 'htm'],
            'mime-type' => ['text/html', 'application/xhtml+xml'],
        ],
    ];

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     *
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldReturnFormattedResponseDirectly(): void
    {
        $middleware = $this->createMiddleware();
        $response   = $middleware->process(new ServerRequest(), $this->createRequestHandler(new EmptyResponse()));

        self::assertInstanceOf(EmptyResponse::class, $response);
        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertSame('application/json; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     * @covers ::extractContentType()
     * @covers ::formatResponse()
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldReturnAResponseWithErrorWhenFormatterWasNotFound(): void
    {
        $middleware = $this->createMiddleware();

        $response = $middleware->process(
            (new ServerRequest())->withAddedHeader('Accept', 'text/plain'),
            $this->createRequestHandler($this->createResponse())
        );

        self::assertInstanceOf(UnformattedResponse::class, $response);
        self::assertSame(StatusCodeInterface::STATUS_NOT_ACCEPTABLE, $response->getStatusCode());
        self::assertSame('text/plain; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     * @covers ::extractContentType()
     * @covers ::formatResponse()
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldReturnAResponseWithFormattedContent(): void
    {
        $middleware = $this->createMiddleware();

        $response = $middleware->process(
            new ServerRequest(),
            $this->createRequestHandler($this->createResponse())
        );

        self::assertInstanceOf(UnformattedResponse::class, $response);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        self::assertJsonStringEqualsJsonString('{"id":1,"name":"Testing"}', (string) $response->getBody());
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     * @covers ::extractContentType()
     * @covers ::formatResponse()
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldPassAttributesToTheFormatterProperly(): void
    {
        $middleware = $this->createMiddleware();

        $response = $middleware->process(
            (new ServerRequest())->withAddedHeader('Accept', 'text/html'),
            $this->createRequestHandler($this->createResponse(['template' => 'person']))
        );

        self::assertInstanceOf(UnformattedResponse::class, $response);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));

        $body = (string) $response->getBody();

        self::assertStringContainsString('<dd>1</dd>', $body);
        self::assertStringContainsString('<dd>Testing</dd>', $body);
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     * @covers ::extractContentType()
     * @covers ::formatResponse()
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldReturnAResponseWithFormattedContentEvenWithoutForcingTheCharset(): void
    {
        $middleware = $this->createMiddleware(false);

        $response = $middleware->process(
            new ServerRequest(),
            $this->createRequestHandler($this->createResponse())
        );

        self::assertInstanceOf(UnformattedResponse::class, $response);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
        self::assertJsonStringEqualsJsonString('{"id":1,"name":"Testing"}', (string) $response->getBody());
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     * @covers ::extractContentType()
     * @covers ::formatResponse()
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldRaiseExceptionWhenInvalidStreamWasCreatedInDevelopmentMode(): void
    {
        ini_set('assert.exception', '1');

        $this->expectException(AssertionError::class);
        $this->processWithInvalidStreamFactory();
    }

    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::fromRecommendedSettings()
     * @covers ::process()
     * @covers ::extractContentType()
     * @covers ::formatResponse()
     *
     * @uses \Lcobucci\ContentNegotiation\UnformattedResponse
     * @uses \Lcobucci\ContentNegotiation\Formatter\Json
     */
    public function processShouldRaiseAWarningWhenInvalidStreamWasCreatedInDevelopmentMode(): void
    {
        ini_set('assert.exception', '0');

        $this->expectException(Warning::class);
        $this->processWithInvalidStreamFactory();
    }

    private function processWithInvalidStreamFactory(): void
    {
        $middleware = $this->createMiddleware(
            true,
            static function () {
                return false;
            }
        );

        $middleware->process(
            new ServerRequest(),
            $this->createRequestHandler($this->createResponse())
        );
    }

    /**
     * @param mixed[] $attributes
     */
    private function createResponse(array $attributes = []): UnformattedResponse
    {
        return new UnformattedResponse(
            new Response(),
            new PersonDto(1, 'Testing'),
            $attributes
        );
    }

    private function createRequestHandler(ResponseInterface $response): RequestHandlerInterface
    {
        return new class($response) implements RequestHandlerInterface
        {
            /**
             * @var ResponseInterface
             */
            private $response;

            public function __construct(ResponseInterface $response)
            {
                $this->response = $response;
            }

            /**
             * {@inheritdoc}
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };
    }

    private function createMiddleware(bool $forceCharset = true, ?callable $streamFactory = null): ContentTypeMiddleware
    {
        return ContentTypeMiddleware::fromRecommendedSettings(
            $this->configureCharset($forceCharset),
            [
                'application/json' => new Formatter\Json(),
                'text/html'        => new NaiveTemplateEngine(),
            ],
            $streamFactory
        );
    }

    /**
     * @return mixed[]
     */
    private function configureCharset(bool $forceCharset = true): array
    {
        return array_map(
            static function (array $config) use ($forceCharset): array {
                return ['charset' => $forceCharset] + $config;
            },
            self::SUPPORTED_FORMATS
        );
    }
}
