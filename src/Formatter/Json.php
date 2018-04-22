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
use const JSON_UNESCAPED_SLASHES;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
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
            $encoded = json_encode($content, $this->flags);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                'An exception was thrown during JSON formatting',
                $exception->getCode(),
                $exception
            );
        }

        if ($encoded === false) {
            throw new ContentCouldNotBeFormatted(
                sprintf('Given data cannot be formatted as JSON: %s', json_last_error_msg()),
                json_last_error()
            );
        }

        return $encoded;
    }
}
