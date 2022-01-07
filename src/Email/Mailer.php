<?php

namespace App\Email;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime;
use Twig\Environment;

class Mailer
{

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;

    public function __contruct(
        MailerInterface $mailer,
        Environment $twig
    ){
        $this->mailer = $mailer;
        $this->twig = $twig;

    }


    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function  sendConfirmationEmail()
    {

        $email = (new Mime\Email())
            ->from('glynn@example.com')
            ->to('krauskarol7@gmail.com')
            ->subject("What's the point of the Symfony Mailer, anyway?")
            ->html('<h1>HTML email</h1><p>Email with HTML tags if the client supports it.</p>')
            ->text('Plain text email');

//       $this->mailer->send($email);


    }
}