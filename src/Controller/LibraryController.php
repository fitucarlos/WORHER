<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    /**
     * @Route("/books", name="books_get")
     */
    public function list(Request $request, BookRepository $bookRepository)
    {
        $title = $request->get('title','Alergias');
        $books = $bookRepository->findAll();
        $booksAsArray = [];
        foreach($books as $book){
            $booksAsArray[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImagen()
            ];
        }
        $response = new JsonResponse();
        $response->setData(
            [
                'success' => true,
                'data' => $booksAsArray
            ]);
        return $response;
    }

    /**
     * @Route("/book/create", name="create")
     */

    public function createBook(Request $request,EntityManagerInterface $em){
        $book = new Book();
        $response = new JsonResponse();
        $title = $request->get('title',null);
        if(empty($title)){
            $response->setData(
                [
                    'success' => false,
                    'error' => 'title not found',
                    'data' => null
                ]);
            return $response;
        
        }
        $book->setTitle($title);
        $em->persist($book);
        $em->flush();
        $response->setData(
            [
                'success' => true,
                'data' => [
                    [
                        'id' => $book->getId(),
                        'title' => $book->getTitle()
                    ]
                ]
            ]);
        return $response;
}
}