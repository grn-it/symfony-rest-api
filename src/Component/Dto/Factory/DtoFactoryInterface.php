<?php

declare(strict_types=1);

namespace App\Component\Dto\Factory;

interface DtoFactoryInterface
{
    /** @param array<mixed> $context */
    public function create(object $data, string $class, array $context = []): mixed;
}
