<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\BookFormProcessor;
use App\Service\BookManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController{
    /**
     * @Rest\Get(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */

     public function getActions(
        BookRepository $bookRepository
     ){
        return $bookRepository->findAll();
     }

    /**
     * @Rest\Post(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */

     public function postActions(
        EntityManagerInterface $em,
        Request $request
     ){
        $bookDto = new BookDto();
        $form = $this->createForm(BookFormType::class, $bookDto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $book = new Book();
            $book->setTitle($bookDto->title);
            $em->persist($book);
            $em->flush();
            return $book;
        }
        return $form;
     }
     /**
     * @Rest\Post(path="/books/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
      int $id,
      BookFormProcessor $bookFormProcessor,
      BookManager $bookManager,
      Request $request
  ) {
      $book = $bookManager->find($id);
      if (!$book) {
          throw View::create('Book not found',Response::HTTP_BAD_REQUEST);
      }
      [$book, $error] = ($bookFormProcessor)($book, $request);
      return $book ?? $error;
   }
}
