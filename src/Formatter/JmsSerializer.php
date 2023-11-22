<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use JMS\Serializer\SerializerInterface;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Throwable;

use function sprintf;

final class JmsSerializer extends ContentOnly
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly string $format,
    ) {
    }

    /** {@inheritDoc} */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function formatContent(mixed $content, array $attributes = []): string
    {
        try {
            return $this->serializer->serialize($content, $this->format);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                sprintf('Given content could not be formatted in %s using JMS Serializer', $this->format),
                $exception->getCode(),
                $exception,
            );
        }
    }
}
