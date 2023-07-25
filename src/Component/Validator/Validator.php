<?php

declare(strict_types=1);

namespace App\Component\Validator;

use App\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate(object $object): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors)) {
            throw new ValidatorException($errors->__toString());
        }
    }
}
