<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;

/**
 * @ApiResource (
 *     normalizationContext={"groups"={"get"}},
 *     itemOperations={
 *     "get" = { "access_control" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *     "normalization_context"={
 *         "groups"={"get"}
 *     }
 *     },
 *
 *     "put" = { "access_control" = "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *     "denormalization_context"={
 *         "groups"={"put"}
 *     }
 *     },
 *     "put-reset-password" = {
 *          "access_control" = "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *          "method"="PUT",
 *          "path"="/users/{id}/reset-password",
 *          "controller"=ResetPasswordAction::class,
 *          "denormalization_context"={
 *         "groups"={"put-reset-password"}
 *         }
 *     }
 * },
 *     collectionOperations={
 *     "post"= {
 *     "denormalization_context"={
 *         "groups"={"post"}
 *          }
 *      }
 *     },
 *
 *   )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @method string getUserIdentifier()
 */



class User implements UserInterface
{
    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Groups({"get", "post", "get-comment-with-author","get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="hasło jest niepoprawne - musi zawierac conajmniej jedna wielka i mała litere oraz liczbe",
     *     groups={"post"}
     * )
     * @Groups({"post"})
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
            "this.getPassword() === this.getretypePassword()",
     *     message = "passwords does not match",
     *     groups={"post"}
*     )
     * @Groups({"post"})
     */

    private $retypePassword;

    /**
     *
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="hasło jest niepoprawne - musi zawierac conajmniej jedna wielka i mała litere oraz liczbe",
     *     groups={"post"}
     * )
     * @Groups({"put-reset-password"})
     */

    private $newPassword;

    /**
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
    "this.getNewPassword() === this.getNewRetypePassword()",
     *     message = "passwords does not match",
     *     groups={"post"}
     *     )
     * @Groups({"put-reset-password"})
     */

    private $newRetypedPassword;

    /**
     * @Assert\NotBlank()
     * @UserPassword()
     * @Groups({"put-reset-password"})
     */

    private $oldPassword;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"get", "post", "put"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups({"post", "put","get-admin", "get-owner"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * * @Groups({"get"})
     */
    private$posts;
    /**
     * @ORM\OneToMany (targetEntity="App\Entity\Comment", mappedBy="author")
     * * @Groups({"get"})
     */

    private$comments;

    /**
     * @ORM\Column (type="simple_array", length=200)
     * * @Groups({"get-admin", "get-owner"})
     */
    private $roles;

    /**
     * @ORM\Column (type="integer", nullable=true)
     */

    private $passwordChangeDate;

    public function  __construct(){
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return Collection
     */
    public function getComments(): \Doctrine\Common\Collections\Collection
    {
        return $this->comments;
    }


    public function getRoles() :array
    {
        return  $this->roles;
    }
    public function setRoles(array $roles){
        $this->roles = $roles;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }


    public function getRetypePassword()
    {
        return $this->retypePassword;
    }

    public function setRetypePassword($retypePassword): void
    {
        $this->retypePassword = $retypePassword;
    }


    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }


    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }


    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }


    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }


    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }


    public function getPasswordChangeDate(): integer
    {
        return $this->passwordChangeDate;
    }


    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }






}
