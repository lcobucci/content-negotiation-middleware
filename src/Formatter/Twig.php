<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Throwable;
use Twig\Environment;

final class Twig extends ContentOnly
{
    private const DEFAULT_ATTRIBUTE = 'template';

    private Environment $environment;
    private string $attributeName;

    public function __construct(
        Environment $environment,
        string $attributeName = self::DEFAULT_ATTRIBUTE
    ) {
        $this->environment   = $environment;
        $this->attributeName = $attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function formatContent($content, array $attributes = []): string
    {
        try {
            return $this->render($content, $attributes);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                'An error occurred while formatting using twig',
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param mixed   $content
     * @param mixed[] $attributes
     *
     * @throws Throwable
     */
    private function render($content, array $attributes = []): string
    {
        $template = $attributes[$this->attributeName] ?? '';

        return $this->environment->render($template, ['content' => $content]);
    }
}
