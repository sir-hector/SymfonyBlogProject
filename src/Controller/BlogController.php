<?php

namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route ("/blog")
 */

class BlogController extends AbstractController
{
    private const POSTS = [
      [
          'id' => 1,
          'slug' => 'hello-world',
          'tittle' => 'hello-world',
      ],
      [
          'id' => 2,
          'slug' => 'hello-world2',
          'tittle' => 'hello-world2',
      ]
    ];

    /**
     * @Route("/{page}", name="blog_list", defaults={"page" = 5}, requirements={"page" = "\d+"})
     */
    public function list($page  =1, Request $request){

        $limit = $request->get('limit', 10);
        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function ($item) {
                return $this->generateUrl('blog_by_id',["id" => $item['id']]);
            }, self::POSTS)
        ]
    );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id" = "\d+"})
     */
    public function post($id){
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, "id"))]
        );
    }
    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     */
    public function postbySlug($slug){
        return $this->json(
            self::POSTS[array_search($slug, array_column(self::POSTS, "slug"))]
        );
    }

    /**
     * @Route("/add", name="blog_add",methods={"POST"})
     */

    public function  add(Request $request){

        /** @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }



}