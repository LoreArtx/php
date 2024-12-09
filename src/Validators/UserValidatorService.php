<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserValidatorService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateUserData(array $data): array
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(message: 'Name cannot be blank.'),
                new Assert\Length(max: 255, maxMessage: 'Name cannot be longer than {{ limit }} characters.'),
            ],
            'email' => [
                new Assert\NotBlank(message: 'Email cannot be blank.'),
                new Assert\Email(message: 'Invalid email address.'),
            ],
            'password' => [
                new Assert\NotBlank(message: 'Password cannot be blank.'),
                new Assert\Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters long.'),
            ],
            'phone' => [
                new Assert\NotBlank(message: 'Phone cannot be blank.'),
                new Assert\Regex(pattern: '/^\+?[0-9]{10,15}$/', message: 'Invalid phone number format.'),
            ],
            'role' => [
                new Assert\NotBlank(message: 'Role cannot be blank.'),
                new Assert\Choice(
                    choices: ['client', 'admin', 'trainer'],
                    message: 'Invalid role. Allowed values are: client, admin, trainer.'
                ),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
