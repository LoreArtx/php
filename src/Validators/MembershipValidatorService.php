<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MembershipValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateMembershipData(array $data): array
    {
        $constraints = new Assert\Collection([
            'user' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'subscription_plan' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'start_date' => [new Assert\NotBlank(), new Assert\Type('\DateTime')],
            'end_date' => [new Assert\NotBlank(), new Assert\Type('\DateTime')],
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
