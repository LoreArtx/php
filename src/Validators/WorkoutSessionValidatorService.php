<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WorkoutSessionValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateWorkoutSession(array $data): array
    {
        $constraints = new Assert\Collection([
            'program' => [
                new Assert\NotBlank(['message' => 'Program ID cannot be blank']),
                new Assert\Positive(['message' => 'Program ID must be positive']),
            ],
            'trainer' => [
                new Assert\NotBlank(['message' => 'Trainer ID cannot be blank']),
                new Assert\Positive(['message' => 'Trainer ID must be positive']),
            ],
            'start_time' => [
                new Assert\NotBlank(['message' => 'Start time cannot be blank']),
                new Assert\Type('\DateTime'),
            ],
            'end_time' => [
                new Assert\NotBlank(['message' => 'End time cannot be blank']),
                new Assert\Type('\DateTime'),
            ],
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
