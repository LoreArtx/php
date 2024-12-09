<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateBooking(array $data): array
    {
        $constraints = new Assert\Collection([
            'userId' => [
                new Assert\NotBlank(['message' => 'User ID cannot be blank']),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'User ID must be an integer',
                ]),
            ],
            'workoutSessionId' => [
                new Assert\NotBlank(['message' => 'Workout session ID cannot be blank']),
                new Assert\Type([
                    'type' => 'integer',
                    'message' => 'Workout session ID must be an integer',
                ]),
            ],
            'status' => [
                new Assert\NotBlank(['message' => 'Status cannot be blank']),
                new Assert\Choice([
                    'choices' => ['confirmed', 'cancelled'],
                    'message' => 'Status must be one of "confirmed" or "cancelled"',
                ]),
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
