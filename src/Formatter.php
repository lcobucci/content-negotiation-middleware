<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

interface Formatter
{
    /** @throws ContentCouldNotBeFormatted */
    public function format(UnformattedResponse $response, StreamFactoryInterface $streamFactory): ResponseInterface;
}
