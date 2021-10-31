<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter;
use Lcobucci\ContentNegotiation\UnformattedResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

abstract class ContentOnly implements Formatter
{
    /** @throws ContentCouldNotBeFormatted */
    public function format(UnformattedResponse $response, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        return $response->withBody(
            $streamFactory->createStream(
                $this->formatContent($response->getUnformattedContent(), $response->getAttributes()),
            ),
        );
    }

    /** @param mixed[] $attributes */
    abstract public function formatContent(mixed $content, array $attributes = []): string;
}
