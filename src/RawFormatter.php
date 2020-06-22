<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

use Psr\Http\Message\ResponseInterface;

interface RawFormatter
{
    /**
     * @throws ContentCouldNotBeFormatted
     */
    public function format(UnformattedResponse $response): ResponseInterface;
}
