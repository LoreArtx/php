<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validatePaymentData(array $data): array
    {
        $constraints = new Assert\Collection([
            'user' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'membership' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'amount' => [new Assert\NotBlank(), new Assert\GreaterThan(0)],
            'payment_date' => [new Assert\NotBlank(), new Assert\Type('\DateTime')],
            'status' => [new Assert\NotBlank(), new Assert\Choice(['choices' => ['pending', 'completed', 'failed']])],
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
