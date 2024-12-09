<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TrainerValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateTrainerData(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['min' => 3, 'max' => 255])],
            'specialization' => [new Assert\NotBlank(), new Assert\Length(['min'=>3, 'max' => 255])],
            'experience' => [new Assert\NotBlank(), new Assert\Positive()],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'phone' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
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
