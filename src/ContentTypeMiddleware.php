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
    /** @param Formatter[] $formatters */
    public function __construct(
        private readonly MiddlewareInterface $negotiator,
        private readonly array $formatters,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    /**
     * @param mixed[]     $formats
     * @param Formatter[] $formatters
     */
    public static function fromRecommendedSettings(
        array $formats,
        array $formatters,
        StreamFactoryInterface $streamFactory,
    ): self {
        return new self(
            new ContentType($formats),
            $formatters,
            $streamFactory,
        );
    }

    /**
     * {@inheritDoc}
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

        return ($this->formatters[$contentType] ?? new NotAcceptable())
            ->format($response, $this->streamFactory);
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
