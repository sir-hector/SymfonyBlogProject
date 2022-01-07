<?php

namespace App\Security;

use App\Exception\InvalidConfiramtionTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;


    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ){

        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    public function confirmUser(string $confirmationToken)
        {
            $this->logger->debug('Loguje');

            $user = $this->userRepository->findOneBy(
                    ['confiramtionToken' => $confirmationToken]
                );
                // User was not found by confiramtion token
                if(!$user) {
                    throw new InvalidConfiramtionTokenException();
                }

                $user->setEnabled(true);
                $user->setConfiramtionToken(null);
                $this->entityManager->flush();
        }
}