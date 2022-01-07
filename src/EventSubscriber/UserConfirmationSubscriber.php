<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\UserConfirmation;
use App\Security\UserConfirmationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserConfirmationSubscriber implements EventSubscriberInterface
{


    private UserConfirmationService $confirmationService;

    public function __construct(
        UserConfirmationService $confirmationService
    ){
        $this->confirmationService = $confirmationService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE]
        ];
    }

    public function confirmUser(ViewEvent $event){
        $request = $event->getRequest();

        if('api_user_confirmations_post_collection' !== $request->get('_route')){
            return;
        }
        /** @var UserConfirmation $confirmationToken */
        $confirmationToken = $event->getControllerResult();

        $this->confirmationService->confirmUser(
            $confirmationToken->confirmationToken
        );



        $event->setResponse(new JsonResponse(null, 200));
    }
}