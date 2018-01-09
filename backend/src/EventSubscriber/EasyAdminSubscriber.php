<?php

namespace App\EventSubscriber;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use App\Entity\Post;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /** @var ObjectRepository $postRepository */
    protected $postRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->postRepository = $em->getRepository(Post::class);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.pre_persist' => array('setPostSlug'),
            'easy_admin.pre_update'  => array('setPostSlug'),
        );
    }

    public function setPostSlug(GenericEvent $event)
    {
        $entity = $event->getSubject();
        if (!($entity instanceof Post)) {
            return;
        }

        $slug    = preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($entity->getTitle())), 'UTF-8'));
        $counter = 1;
        while ($this->postRepository->findBy(['slug' => $slug])) {
            $slug .= $counter++;
        }

        $entity->setSlug($slug);
        $event['entity'] = $entity;
    }
}