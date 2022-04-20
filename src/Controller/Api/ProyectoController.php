<?php

namespace App\Controller\Api;

use App\Entity\Proyecto;
use App\Entity\Lista;
use App\Form\Model\ProyectoDto;
use App\Form\Model\ListaDto;
use App\Form\Type\ProyectoFormType;
use App\Repository\BookRepository;
use App\Repository\ProyectoRepository;
use App\Repository\ListaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProyectoController extends AbstractFOSRestController{
    /**
     * @Rest\Get(path="/proyectos")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function getActions(
        ProyectoRepository $ProyectoRepository
     ){
        return $ProyectoRepository->findAll();
     }

    /**
     * @Rest\Post(path="/proyectos")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function postActions(
        EntityManagerInterface $em,
        Request $request
     ){
        $ProyectoDto = new ProyectoDto();
        $form = $this->createForm(ProyectoFormType::class, $ProyectoDto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $Proyecto = new Proyecto();
            $Proyecto->setNombre($ProyectoDto->nombre);
            $em->persist($Proyecto);
            $em->flush();
            return $Proyecto;
        }
        return $form;
     }


    /**
     * @Rest\Post(path="/proyectos/lista/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function editActions(
        int $id,
        EntityManagerInterface $em,
        Request $request,
        ProyectoRepository $proyectoRepository,
        ListaRepository $listaRepository
     ){
      $Proyecto = $proyectoRepository->find($id);
      if(!$Proyecto){
         throw $this->createNotFoundException('Este proyecto no existe');
      }
      $ProyectoDto = ProyectoDto::createFromProyecto($Proyecto);

      $originalListas = new ArrayCollection();
      foreach ($Proyecto->getListas() as $lista) {
         $ListaDto = ListaDto::createFromLista($lista);
         $ProyectoDto->listas[] = $ListaDto;
         $originalListas->add($ListaDto);
      }

      $form = $this->createForm(ProyectoFormType::class, $ProyectoDto);
      $form->handleRequest($request);
      if(!$form->isSubmitted()){
         return new Response('',Response::HTTP_BAD_REQUEST);
      }
      if($form->isValid()){

      

         //Add listas
         foreach($ProyectoDto->listas as $newListaDto){
               $lista = $listaRepository->find($newListaDto->id ?? 0);
               if(!$lista){
                  $lista = new Lista();
                  $lista->setNombre($newListaDto->nombre);
                  $em->persist($lista);
               }
               $Proyecto->addLista($lista);
         }
         $em->persist($Proyecto);
         $em->flush();
         $em->refresh($Proyecto);
         return $Proyecto;
      }
      return $form;
   }
}
