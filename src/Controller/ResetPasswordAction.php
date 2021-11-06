<?php

namespace App\Controller;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ResetPasswordAction
{

    private $validator;
    private  $userPasswordEncoder;
    private $entityManager;
    private  $tokenManager;
    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $tokenManager
    ){
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }
    public function __invoke(User $data){
        $this->validator->validate($data);
        $data->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $data, $data->getNewPassword()
            )
        );
        // After cgange password, the old password is still valid.
        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();
        $token = $this->tokenManager->create($data);
        return new JsonResponse((['token' => $token]));
    }

}