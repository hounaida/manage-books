<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/", name="books_homepage")
     * @param BookRepository $bookRepository
     * @return Response
     */
    public function index(Request $request, BookRepository $bookRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $bookRepository->findBy(['status' => 1]);
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5
        );

        return $this->render('book/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/book/{id}", name="book_details", methods="GET")
     * @param Request $request
     * @param BookRepository $bookRepository
     * @return Response
     */
    public function bookShow(Request $request, BookRepository $bookRepository): Response
    {
        $id = $request->attributes->get('id');
        $book = $bookRepository->findOneBy([
            'id' => $id, 'status' => true,
        ]);

        if (!$book) {
            throw $this->createNotFoundException('The book does not exist');
        }

        return $this->render('book/show.html.twig', ['book' => $book]);
    }
}
