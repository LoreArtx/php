<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionPlanValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateSubscriptionPlanData(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Type('string')],
            'price' => [new Assert\NotBlank(), new Assert\GreaterThan(0), new Assert\Type('string')],
            'duration' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\GreaterThan(0)],
        ]);

        $violations = $this->validator->validate($data, $constraints);
        $errors = [];

        foreach ($violations as $violation) {
            $field = trim($violation->getPropertyPath(), '[]');
            $errors[$field] = $violation->getMessage();
        }

        return $errors;
    }
}
