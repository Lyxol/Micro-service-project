<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\User;
use App\Repository\CommandRepository;
use App\Repository\MessagesRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\EntityToArray;
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

    #[Route('/message', name: 'api_user_send_message', methods: ['POST'])]
    public function sendMessage(MessagesRepository $mR,UserRepository $uR,Request $request)
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent());
        $seller = $uR->findOneById($data->id_seller);
        if($seller === null)
            return $this->json([
                'error'=>'seller not found'
            ],404);
        if(!in_array('ROLE_SELLER', $seller->getRoles(), true))
            return $this->json([
                'error'=>'user is not a seller'
            ],409);
        try {
            $message = new Messages;
            $message->setMail($user->getEmail());
            $message->setSeller($seller);
            $message->setMessage($data->message);
            $mR->save($message,true);
            return $this->json([
                'id'=>$message->getId(),
                'mail_customer'=>$message->getMail(),
                'seller'=>[
                    "id"=> $message->getSeller()->getId(),
                    "email"=> $message->getSeller()->getEmail(),
                ],
                'message'=> $message->getMessage()
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                'error'=>'bad request'
            ],400);
        }
    }

    #[Route('/message', name: 'api_user_show_message', methods: ['GET'])]
    public function showMessage()
    {
        $user = $this->getUser();
        $display = [];
        foreach($user->getMessages() as $message){
            $display[] = [                
                'id'=>$message->getId(),
                'mail_customer'=>$message->getMail(),
                'message'=> $message->getMessage()
            ];
        }
        return $this->json([
            'messages'=>$display
        ]);
    }

}
