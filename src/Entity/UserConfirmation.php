<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ApiResource(
 *    collectionOperations={
 *      "post"={
 *          "path"="/users/confirm"
 *    }
 * },
 *     itemOperations={}
 * )
 */
class UserConfirmation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=10, max=60)
     */
    public $confirmationToken;

    public function __toString()
    {
        return $this->confirmationToken;
    }
}