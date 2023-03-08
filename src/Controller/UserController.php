<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommandRepository;
use App\Repository\ProductRepository;
use App\Service\EntityToArray;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/commands', name: 'api_user_commands', methods: ['GET'])]
    public function showCommand(CommandRepository $cR, ProductRepository $pR, EntityToArray $entityToArray)
    {
        $user = $this->getUser();
        $list_command = [];
        foreach ($cR->findAllByUser($user) as $command) {
            $list_command[] = $entityToArray->commandArray($command, $pR);
        }
        return $this->json([
            'commands' => $list_command
        ]);
    }
}
