<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Throwable;
use Twig\Environment;

use function assert;
use function is_string;

final class Twig extends ContentOnly
{
    private const DEFAULT_ATTRIBUTE = 'template';

    public function __construct(
        private readonly Environment $environment,
        private readonly string $attributeName = self::DEFAULT_ATTRIBUTE,
    ) {
    }

    /** {@inheritdoc} */
    public function formatContent(mixed $content, array $attributes = []): string
    {
        try {
            return $this->render($content, $attributes);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                'An error occurred while formatting using twig',
                $exception->getCode(),
                $exception,
            );
        }
    }

    /**
     * @param mixed[] $attributes
     *
     * @throws Throwable
     */
    private function render(mixed $content, array $attributes = []): string
    {
        $template = $attributes[$this->attributeName] ?? '';
        assert(is_string($template));

        return $this->environment->render($template, ['content' => $content]);
    }
}
