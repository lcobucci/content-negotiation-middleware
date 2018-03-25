<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests;

final class PersonDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    public function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
