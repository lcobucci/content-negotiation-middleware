<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Formatter;

use JMS\Serializer\SerializerInterface;
use Lcobucci\ContentNegotiation\ContentCouldNotBeFormatted;
use Lcobucci\ContentNegotiation\Formatter;
use Throwable;
use function sprintf;

final class JmsSerializer implements Formatter
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $format;

    public function __construct(SerializerInterface $serializer, string $format)
    {
        $this->serializer = $serializer;
        $this->format     = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function format($content): string
    {
        try {
            return $this->serializer->serialize($content, $this->format);
        } catch (Throwable $exception) {
            throw new ContentCouldNotBeFormatted(
                sprintf('Given content could not be formatted in %s using JMS Serializer', $this->format),
                $exception->getCode(),
                $exception
            );
        }
    }
}
