<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests\Formatter;

use Lcobucci\ContentNegotiation\Formatter\ContentOnly;
use SplFileObject;

use function array_keys;
use function array_map;
use function assert;
use function is_string;
use function str_replace;
use function trim;

final class NaiveTemplateEngine extends ContentOnly
{
    private const BASE_DIR  = __DIR__ . '/../../templates/naive/';
    private const EXTENSION = 'html';

    /**
     * {@inheritdoc}
     */
    public function formatContent($content, array $attributes = []): string
    {
        $template = $this->getTemplateContent($attributes);

        return $this->render($template, (array) $content);
    }

    /**
     * @param mixed[] $attributes
     */
    private function getTemplateContent(array $attributes): string
    {
        $template = $attributes['template'] ?? '';
        assert(is_string($template));

        $file    = new SplFileObject(self::BASE_DIR . $template . '.' . self::EXTENSION);
        $content = $file->fread($file->getSize());
        assert(is_string($content));

        return $content;
    }

    /**
     * @param mixed[] $data
     */
    private function render(string $template, array $data): string
    {
        $variables = array_map(
            static function (string $attribute): string {
                return '{' . $attribute . '}';
            },
            array_keys($data)
        );

        return trim(str_replace($variables, $data, $template));
    }
}
