<?php

namespace App\EventListener\Book;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class DeleteBookListener implements EventSubscriberInterface
{
    private const ROUTE_DELETE_BOOK = 'book_delete';
    private $authorizationChecker;
    private $em;

    public function __construct(EntityManagerInterface $em, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => 'onKernelRequest',];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;

        if (self::ROUTE_DELETE_BOOK !== $attributes->get('_route')) {
            return;
        }

        if ($this->authorizationChecker->isGranted(
            'DELETE_BOOK',
            $this->getBook($attributes->get('id'))
        )) {
            throw new AccessDeniedHttpException('operation.not_allowed');
        }
    }

    private function getBook(int $id): ?Book
    {
        return $this->em->getRepository(Book::class)->find($id);
    }
}
