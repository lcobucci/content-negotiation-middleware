<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter;
use Throwable;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use function assert;
use function is_string;
use function json_encode;
use function sprintf;

final class Json implements Formatter
{
    private const DEFAULT_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;

    /**
     * @var int
     */
    private $flags;

    public function __construct(int $flags = self::DEFAULT_FLAGS)
    {
        $this->flags = $flags;
    }

    /**
     * {@inheritdoc}
     */
    public function format($content, array $attributes = []): string
    {
        try {
            $content = json_encode($content, $this->flags | JSON_THROW_ON_ERROR);
            assert(is_string($content));

            return $content;
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                sprintf('An exception was thrown during JSON formatting: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }
}
