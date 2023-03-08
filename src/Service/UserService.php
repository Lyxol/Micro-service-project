<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    /** @var User $user */
    $user = $this->userRepository->findOneBy(['email' => $body->getEmail()]);
//        if (is_null($user)) throw new UserNotFoundApiException();     // renvoier un texte et un code erreur

    $isValid = $this->passwordHasher->isPasswordValid($user, $body->getPlainPassword());
//        if (!$isValid) throw new UserNotValidApiException();

    $token = $this->jwtManager->create($user);
    return [
        'user' => $user,
        'token' => $token
    ];
}

}