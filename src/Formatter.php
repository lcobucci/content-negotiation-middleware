<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

interface Formatter
{
    /**
     * @param mixed   $content
     * @param mixed[] $attributes
     *
     * @throws ContentCouldNotBeFormatted
     */
    public function format($content, array $attributes = []): string;
}
