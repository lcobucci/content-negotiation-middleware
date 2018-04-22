<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

interface Formatter
{
    /**
     * @param mixed   $content
     * @param mixed[] $attributes
     *
     * @throw ContentCouldNotBeFormatted
     */
    public function format($content, array $attributes = []): string;
}
