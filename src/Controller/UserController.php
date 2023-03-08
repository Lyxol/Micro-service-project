<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\User;
use App\Exception\UserNotFoundApiException;
use App\Exception\UserNotValidApiException;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\ApiResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    #[Route('/login', name: 'api_user_login', methods: ['POST'], format: 'application/json')]
    public function login(
        Request                     $request,
        SerializerInterface         $serializer,
        UserService                 $userService
    ): Response {
        /** @var User $body */
        $body = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => []]
        );

        $response = $userService->auth($body);

        return $this->json(
            $response,
            200,
            [],
            ['groups' => ['user']]
        );
    }
}
