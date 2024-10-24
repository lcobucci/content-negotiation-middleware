<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class UnformattedResponse implements ResponseInterface
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        private readonly ResponseInterface $decoratedResponse,
        private readonly mixed $unformattedContent,
        private readonly array $attributes = [],
    ) {
    }

    public function getUnformattedContent(): mixed
    {
        return $this->unformattedContent;
    }

    public function getProtocolVersion(): string
    {
        return $this->decoratedResponse->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): self
    {
        return new self(
            $this->decoratedResponse->withProtocolVersion($version),
            $this->unformattedContent,
            $this->attributes,
        );
    }

    /** {@inheritDoc} */
    public function getHeaders(): array
    {
        return $this->decoratedResponse->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->decoratedResponse->hasHeader($name);
    }

    /** {@inheritDoc} */
    public function getHeader(string $name): array
    {
        return $this->decoratedResponse->getHeader($name);
    }

    /** {@inheritDoc} */
    public function getHeaderLine(string $name): string
    {
        return $this->decoratedResponse->getHeaderLine($name);
    }

    public function withHeader(string $name, mixed $value): self
    {
        return new self(
            $this->decoratedResponse->withHeader($name, $value),
            $this->unformattedContent,
            $this->attributes,
        );
    }

    public function withAddedHeader(string $name, mixed $value): self
    {
        return new self(
            $this->decoratedResponse->withAddedHeader($name, $value),
            $this->unformattedContent,
            $this->attributes,
        );
    }

    public function withoutHeader(string $name): self
    {
        return new self(
            $this->decoratedResponse->withoutHeader($name),
            $this->unformattedContent,
            $this->attributes,
        );
    }

    public function getBody(): StreamInterface
    {
        return $this->decoratedResponse->getBody();
    }

    public function withBody(StreamInterface $body): self
    {
        return new self(
            $this->decoratedResponse->withBody($body),
            $this->unformattedContent,
            $this->attributes,
        );
    }

    public function getStatusCode(): int
    {
        return $this->decoratedResponse->getStatusCode();
    }

    public function withStatus(int $code, string $reasonPhrase = ''): self
    {
        return new self(
            $this->decoratedResponse->withStatus($code, $reasonPhrase),
            $this->unformattedContent,
            $this->attributes,
        );
    }

    public function getReasonPhrase(): string
    {
        return $this->decoratedResponse->getReasonPhrase();
    }

    /**
     * Returns an instance with the specified attribute
     */
    public function withAttribute(string $name, mixed $value): self
    {
        return new self(
            $this->decoratedResponse,
            $this->unformattedContent,
            [$name => $value] + $this->attributes,
        );
    }

    /**
     * Retrieve the configured attributes
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
