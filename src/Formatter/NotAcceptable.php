<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Fig\Http\Message\StatusCodeInterface;
use Lcobucci\ContentNegotiation\Formatter;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class NotAcceptable implements Formatter
{
    public function format(UnformattedResponse $response, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        return $response->withBody($streamFactory->createStream())
                        ->withStatus(StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
    }
}
