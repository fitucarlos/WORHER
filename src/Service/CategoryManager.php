<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryManager {
    private $em;
    private $CategoryRepository;
    public function __construct(EntityManagerInterface $em,CategoryRepository $CategoryRepository) {
        $this->em = $em;
        $this->CategoryRepository = $CategoryRepository;
    }

    public function find(int $id): ?Category{
        return $this->CategoryRepository->find($id);
    }

    public function create(): Category {
        $Category = new Category();
        return $Category;
    }

    public function persist(Category $Category): Category {
        $this->em->persist($Category);
        return $Category;
    }

    public function save(Category $Category): Category{
        $this->em->persist($Category);
        $this->em->flush();
        return $Category;
    }

    public function reload(Category $Category): Category{
        $this->em->refresh($Category);
        return $Category;
    }
}