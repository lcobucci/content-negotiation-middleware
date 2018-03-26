<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter;
use function is_object;
use function method_exists;

final class StringCast implements Formatter
{
    /**
     * {@inheritdoc}
     */
    public function format($content): string
    {
        if (is_object($content) && ! method_exists($content, '__toString')) {
            throw new ContentCouldNotBeFormatted('Given data could not be cast to string');
        }

        return (string) $content;
    }
}
