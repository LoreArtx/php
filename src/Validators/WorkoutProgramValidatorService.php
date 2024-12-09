<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WorkoutProgramValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateWorkoutProgramData(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'description' => [new Assert\NotBlank(), new Assert\Length(['max' => 2000])],
            'duration' => [new Assert\NotBlank(), new Assert\Type('integer'), new Assert\GreaterThan(0)],
            'trainer' => [new Assert\NotBlank(), new Assert\Type('integer')],
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
