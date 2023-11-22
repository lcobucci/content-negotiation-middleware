<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Stringable;

use function is_scalar;

final class StringCast extends ContentOnly
{
    /** {@inheritDoc} */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    public function formatContent(mixed $content, array $attributes = []): string
    {
        if (! $content instanceof Stringable && ! is_scalar($content) && $content !== null) {
            throw new ContentCouldNotBeFormatted('Given data could not be cast to string');
        }

        return (string) $content;
    }
}
