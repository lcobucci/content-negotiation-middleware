<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Throwable;

use function json_encode;
use function sprintf;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final class Json extends ContentOnly
{
    private const DEFAULT_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;

    public function __construct(private readonly int $flags = self::DEFAULT_FLAGS)
    {
    }

    /** {@inheritdoc} */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function formatContent(mixed $content, array $attributes = []): string
    {
        try {
            return json_encode($content, $this->flags | JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                sprintf('An exception was thrown during JSON formatting: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception,
            );
        }
    }
}
