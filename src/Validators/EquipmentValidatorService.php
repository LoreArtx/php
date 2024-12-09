<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EquipmentValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateEquipment(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(['message' => 'Name cannot be blank']),
                new Assert\Length([
                    'min' => 3,
                    'minMessage' => 'Name cannot be less than 3 characters',
                    'max' => 255,
                    'maxMessage' => 'Name cannot exceed 255 characters',
                ]),
            ],
            'type' => [
                new Assert\NotBlank(['message' => 'Type cannot be blank']),
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => 'Type cannot exceed 255 characters',
                ]),
            ],
            'quantity' => [
                new Assert\NotBlank(['message' => 'Quantity cannot be blank']),
                new Assert\Positive(['message' => 'Quantity must be positive']),
            ],
            'status' => [
                new Assert\NotBlank(['message' => 'Status cannot be blank']),
                new Assert\Choice([
                    'choices' => ['available', 'unavailable'],
                    'message' => 'Status must be "available" or "unavailable"',
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
