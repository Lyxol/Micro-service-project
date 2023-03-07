<?php

// src/Controller/RegistrationController.php
namespace App\Controller;

// ...
use App\Entity\User;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request)
    {
        // ... e.g. get the user data from a registration form
        $user = $request->getRequestUrl();
        $plaintextPassword = $request->query->get();

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        // ...
    }
}