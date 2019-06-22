<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

use Fig\Http\Message\StatusCodeInterface;
use Middlewares\ContentType;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Stream;
use function assert;
use function strpos;
use function substr;

final class ContentTypeMiddleware implements MiddlewareInterface
{
    /**
     * @var MiddlewareInterface
     */
    private $negotiator;

    /**
     * @var callable
     */
    private $streamFactory;

    /**
     * @var Formatter[]
     */
    private $formatters;

    /**
     * @param Formatter[] $formatters
     */
    public function __construct(
        MiddlewareInterface $negotiator,
        array $formatters,
        callable $streamFactory
    ) {
        $this->negotiator    = $negotiator;
        $this->formatters    = $formatters;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param mixed[]     $formats
     * @param Formatter[] $formatters
     */
    public static function fromRecommendedSettings(
        array $formats,
        array $formatters,
        ?callable $streamFactory = null
    ): self {
        return new self(
            new ContentType($formats),
            $formatters,
            $streamFactory ?? static function (): StreamInterface {
                return new Stream('php://temp', 'wb+');
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContentCouldNotBeFormatted
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->negotiator->process($request, $handler);

        if (! $response instanceof UnformattedResponse) {
            return $response;
        }

        $contentType = $this->extractContentType($response->getHeaderLine('Content-Type'));
        $formatter   = $this->formatters[$contentType] ?? null;

        return $this->formatResponse($response, $formatter);
    }

    private function extractContentType(string $contentType): string
    {
        $charsetSeparatorPosition = strpos($contentType, ';');

        if ($charsetSeparatorPosition === false) {
            return $contentType;
        }

        return substr($contentType, 0, $charsetSeparatorPosition);
    }

    /**
     * @throws ContentCouldNotBeFormatted
     */
    private function formatResponse(UnformattedResponse $response, ?Formatter $formatter): ResponseInterface
    {
        $body = ($this->streamFactory)();
        assert($body instanceof StreamInterface);

        $response = $response->withBody($body);

        if ($formatter === null) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
        }

        $body->write($formatter->format($response->getUnformattedContent(), $response->getAttributes()));
        $body->rewind();

        return $response;
    }
}
