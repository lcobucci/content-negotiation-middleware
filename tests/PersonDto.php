<?php
declare(strict_types=1);

namespace Lcobucci\ContentNegotiation\Tests;

final class PersonDto
{
    public function __construct(public readonly int $id, public readonly string $name)
    {
    }
}
