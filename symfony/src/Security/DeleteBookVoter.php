<?php

namespace App\Security;

use App\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteBookVoter extends Voter
{
    const ATTRIBUTE = 'DELETE_BOOK';

    protected function supports(string $attribute, $subject)
    {
        if ($attribute !== self::ATTRIBUTE) {
            return false;
        }

        if (!$subject instanceof Book) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var Book $book */
        $book = $subject;
        return $this->canDelete($book);
    }

    private function canDelete(Book $book)
    {
        return $book->getStatus();
    }
}
