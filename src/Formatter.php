<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

interface Formatter
{
    /**
     * @param mixed $content
     *
     * @throw ContentCouldNotBeFormatted
     */
    public function format($content): string;
}
