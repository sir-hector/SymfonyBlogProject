<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticatore extends JWTTokenAuthenticator
{

    /*
     *
     */
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        $user = parent::getUser(
            $preAuthToken,
            $userProvider
        );

        var_dump($preAuthToken->getPayload());die;
    }

}