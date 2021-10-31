<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests;

final class PersonDto
{
    public function __construct(public int $id, public string $name)
    {
    }
}
