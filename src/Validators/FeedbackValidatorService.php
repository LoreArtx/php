<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FeedbackValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateFeedbackData(array $data): array
    {
        $constraints = new Assert\Collection([
            'user' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'trainer' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'rating' => [new Assert\NotBlank(), new Assert\Range(['min' => 1, 'max' => 5])],
            'comment' => [new Assert\NotBlank(), new Assert\Length(['max' => 1000])],
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
