<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogController
 * @package App\Controller
 *
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Template()
     * @Route("/", defaults={"page": "1"}, name="blog_index")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="blog_index_paginated")
     * @Method("GET")
     * @param int            $page
     * @param PostRepository $posts
     * @return array
     */
    public function index(int $page, PostRepository $posts): array
    {
        return ['posts' => $posts->findLatest($page)];
    }

    /**
     * @Template()
     * @Route("/posts/{slug}", name="blog_post")
     * @Method({"GET", "POST"})
     * @param Post $post
     * @return array
     */
    public function postShow(Post $post, Request $request): array
    {
        $comment = new Comment();
        $form    = $this->createForm(CommentType::class, $comment);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setPost($post);
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $comment = new Comment();
                $form    = $this->createForm(CommentType::class, $comment);
            }
        }

        return [
            'post' => $post,
            'form' => $form->createView()
        ];
    }
}
