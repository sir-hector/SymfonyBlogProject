<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    private const USERS = [
      [
          'username' => 'admin',
          'email' => 'admin@blog.com',
          'name' => 'Piotr Jura',
          'password' => 'password',
          'roles' => [User::ROLE_SUPERADMIN]
      ],
      [

          'username' => 'karol',
          'email' => 'karol@blog.com',
          'name' => 'karol',
          'password' => 'password',
          'roles' => [User::ROLE_ADMIN]
      ],
        [

          'username' => 'kraus',
          'email' => 'kraus@blog.com',
          'name' => 'kraus',
          'password' => 'password',
            'roles' => [User::ROLE_WRITER]
      ],
        [

          'username' => 'weronika',
          'email' => 'weronika@blog.com',
          'name' => 'weronika',
          'password' => 'password',
            'roles' => [User::ROLE_EDITOR]
      ],
        [

            'username' => 'weronikaa',
            'email' => 'weronikaa@blog.com',
            'name' => 'weronikaa',
            'password' => 'password',
            'roles' => [User::ROLE_COMMENTATOR]
        ]
    ];

    public function  __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();

    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager){

        $user = $this->getReference('user_admin');

        for($i=0; $i<100; $i++){
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished(($this->faker->dateTimeThisYear));
            $blogPost->setContent($this->faker->realText());
            $authorReference = ($this->getAuthorReference($blogPost));
            $blogPost->setAuthor($authorReference);
//            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);
            $manager->persist($blogPost);
        }
        $manager->flush();


    }
    public function  loadComments(ObjectManager $manager){

        for($i =0; $i<100; $i++){
            for($j =0; $j<rand(1,10); $j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $authorReference = ($this->getAuthorReference($comment));
                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }

        $manager->flush();

    }

    public function  loadUsers(ObjectManager $manager){
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setName($userFixture['name']);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));
            $user->setRoles($userFixture['roles']);
            $this->addReference('user_'.$userFixture['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }


    protected function getAuthorReference($entity): User
    {
        $randomUser = self::USERS[rand(0,4)];
        if($entity instanceof BlogPost &&
            !count(array_intersect($randomUser['roles'], [User::ROLE_SUPERADMIN,User::ROLE_ADMIN,User::ROLE_WRITER]))){
                return $this->getAuthorReference($entity);
        }

        if($entity instanceof Comment &&
            !count(array_intersect($randomUser['roles'], [User::ROLE_SUPERADMIN,User::ROLE_ADMIN,User::ROLE_WRITER, User::ROLE_COMMENTATOR]))){
            return $this->getAuthorReference($entity);
        }

        return $this->getReference('user_' . $randomUser['username']);

    }
}
