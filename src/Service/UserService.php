<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\managementError;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\throwException;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $jwtManager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

public function auth(User $body): array
{
    if(
        is_null($body->getEmail() ?? null)||
        is_null($body->getPlainPassword() ?? null)
    ){
        return [
            'content' => 'Error.',
            'exception'=> [
                'message' => 'empty fields or incorrect data ',
                'code' => 422,
            ],
        ];
    }
    /** @var User $user */
    $user = $this->userRepository->findOneBy(['email' => $body->getEmail()]);
        if (is_null($user ?? null)){
            return [
                'content' => 'Error.',
                'exception'=> [
                    'message' => 'User not found.',
                    'code' => 404,
                ],
            ];
        }

    $isValid = $this->passwordHasher->isPasswordValid($user, $body->getPlainPassword());
        if (!$isValid){
            return [
                'content' => 'Error.',
                'exception'=> [
                    'message' => 'password error.',
                    'code' => 404,
                ],
            ];
        }

    $token = $this->jwtManager->create($user);
    return [
        'user' => $user,
        'token' => $token
    ];
}

}