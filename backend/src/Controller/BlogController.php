<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Method("GET")
     * @param Post $post
     * @return array
     */
    public function postShow(Post $post): array
    {
        return['post' => $post];
    }

    /**
     * @Route("/comment/{postSlug}/new", name="comment_new")
     * @Method("POST")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     */
    public function commentNew(Request $request, Post $post): Response
    {
        $comment = new Comment();
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
