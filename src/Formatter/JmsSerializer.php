<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use JMS\Serializer\SerializerInterface;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter;
use Throwable;
use function sprintf;

final class JmsSerializer implements Formatter
{
    private SerializerInterface $serializer;
    private string $format;

    public function __construct(SerializerInterface $serializer, string $format)
    {
        $this->serializer = $serializer;
        $this->format     = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function format($content, array $attributes = []): string
    {
        try {
            return $this->serializer->serialize($content, $this->format);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                sprintf('Given content could not be formatted in %s using JMS Serializer', $this->format),
                $exception->getCode(),
                $exception
            );
        }
    }
}
