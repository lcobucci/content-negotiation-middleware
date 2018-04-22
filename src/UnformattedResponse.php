<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class UnformattedResponse implements ResponseInterface
{
    /**
     * @var ResponseInterface
     */
    private $decoratedResponse;

    /**
     * @var mixed
     */
    private $unformattedContent;

    /**
     * @var mixed[]
     */
    private $attributes;

    /**
     * @param mixed   $unformattedContent
     * @param mixed[] $attributes
     */
    public function __construct(
        ResponseInterface $decoratedResponse,
        $unformattedContent,
        array $attributes = []
    ) {
        $this->decoratedResponse  = $decoratedResponse;
        $this->unformattedContent = $unformattedContent;
        $this->attributes         = $attributes;
    }

    /**
     * @return mixed
     */
    public function getUnformattedContent()
    {
        return $this->unformattedContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->decoratedResponse->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        return new self(
            $this->decoratedResponse->withProtocolVersion($version),
            $this->unformattedContent,
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->decoratedResponse->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return $this->decoratedResponse->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->decoratedResponse->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return $this->decoratedResponse->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        return new self(
            $this->decoratedResponse->withHeader($name, $value),
            $this->unformattedContent,
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        return new self(
            $this->decoratedResponse->withAddedHeader($name, $value),
            $this->unformattedContent,
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        return new self(
            $this->decoratedResponse->withoutHeader($name),
            $this->unformattedContent,
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->decoratedResponse->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        return new self(
            $this->decoratedResponse->withBody($body),
            $this->unformattedContent,
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->decoratedResponse->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        return new self(
            $this->decoratedResponse->withStatus($code, $reasonPhrase),
            $this->unformattedContent,
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->decoratedResponse->getReasonPhrase();
    }

    /**
     * Returns an instance with the specified attribute
     *
     * @param mixed $value
     */
    public function withAttribute(string $name, $value): self
    {
        return new self(
            $this->decoratedResponse,
            $this->unformattedContent,
            [$name => $value] + $this->attributes
        );
    }

    /**
     * Retrieve the configured attributes
     *
     * @return mixed[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
