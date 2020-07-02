<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

use Lcobucci\ContentNegotiation\Formatter\NotAcceptable;
use Middlewares\ContentType;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function strpos;
use function substr;

final class ContentTypeMiddleware implements MiddlewareInterface
{
    private MiddlewareInterface $negotiator;
    private StreamFactoryInterface $streamFactory;

    /**
     * @var Formatter[]
     */
    private array $formatters;

    /**
     * @param Formatter[] $formatters
     */
    public function __construct(
        MiddlewareInterface $negotiator,
        array $formatters,
        StreamFactoryInterface $streamFactory
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
        StreamFactoryInterface $streamFactory
    ): self {
        return new self(
            new ContentType($formats),
            $formatters,
            $streamFactory
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
        $formatter   = $this->formatters[$contentType] ?? new NotAcceptable();

        return $formatter->format($response, $this->streamFactory);
    }

    private function extractContentType(string $contentType): string
    {
        $charsetSeparatorPosition = strpos($contentType, ';');

        if ($charsetSeparatorPosition === false) {
            return $contentType;
        }

        return substr($contentType, 0, $charsetSeparatorPosition);
    }
}
