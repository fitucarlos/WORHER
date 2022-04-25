<?php

namespace App\Controller\Api;

use App\Entity\Proyecto;
use App\Entity\Lista;
use App\Entity\Tarea;
use App\Form\Model\ProyectoDto;
use App\Form\Model\ListaDto;
use App\Form\Model\TareaDto;
use App\Form\Type\ListaFormType;
use App\Form\Type\ProyectoFormType;
use App\Repository\BookRepository;
use App\Repository\ProyectoRepository;
use App\Repository\ListaRepository;
use App\Repository\TareaRepository;
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
     * @Rest\Post(path="/proyecto/add_lista/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function addListaActions(
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


    /**
     * @Rest\Post(path="/proyecto/add_tarea/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function addTareaActions(
        int $id,
        EntityManagerInterface $em,
        Request $request,
        TareaRepository $tareaRepository,
        ListaRepository $listaRepository
     ){
      $lista = $listaRepository->find($id);
      if(!$lista){
         throw $this->createNotFoundException('Esta lista no existe');
      }
      $ListaDto = ListaDto::createFromLista($lista);

      $originalTareas = new ArrayCollection();
      foreach ($lista->getTareas() as $tarea) {
         $tareaDto = TareaDto::createFromTarea($tarea);
         $ListaDto->tareas[] = $tareaDto;
         $originalTareas->add($tareaDto);
      }

      $form = $this->createForm(ListaFormType::class, $ListaDto);
      $form->handleRequest($request);
      if(!$form->isSubmitted()){
         return new Response('',Response::HTTP_BAD_REQUEST);
      }
      if($form->isValid()){

         //Add tareas
         foreach($ListaDto->tareas as $newTareaDto){
               $tarea = $tareaRepository->find($newTareaDto->id ?? 0);
               if(!$tarea && $newTareaDto->nombre && $newTareaDto->descripcion && $newTareaDto->dificultad && $newTareaDto->prioridad){
                  $tarea = new Tarea();
                  $tarea->setNombre($newTareaDto->nombre);
                  $tarea->setDescripcion($newTareaDto->descripcion);
                  $tarea->setDificultad($newTareaDto->dificultad);
                  $tarea->setPrioridad($newTareaDto->prioridad);
                  $em->persist($tarea);
               }
               else{
                  throw $this->createNotFoundException('Faltan campos por rellenar');
               }
               $lista->addTarea($tarea);
         }
         $em->persist($lista);
         $em->flush();
         $em->refresh($lista);
         return $lista;
      }
      return $form;
   }


   /**
        * @Rest\Post(path="/proyecto/remove_tarea/{id}")
        * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
        */
   
       public function removeTareaActions(
         int $id,
         EntityManagerInterface $em,
         Request $request,
         TareaRepository $tareaRepository,
         ListaRepository $listaRepository
      ){
         $tarea = $tareaRepository->find($id);
      if(!$tarea){
         throw $this->createNotFoundException('Esta tarea no existe');
      }
      else{
         $tareaRepository->remove($tarea);
      }
      }


   /**
        * @Rest\Post(path="/proyecto/remove_lista/{id}")
        * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
        */
   
       public function removeListaActions(
         int $id,
         EntityManagerInterface $em,
         Request $request,
         TareaRepository $tareaRepository,
         ListaRepository $listaRepository
      ){
         $lista = $listaRepository->find($id);
      if(!$lista){
         throw $this->createNotFoundException('Esta lista no existe');
      }
      else{
         foreach($lista->getTareas() as $tarea);{
            $tareaRepository->remove($tarea);
         }
         $listaRepository->remove($lista);
      }
      }


      /**
           * @Rest\Post(path="/proyecto/remove_proyecto/{id}")
           * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
           */
      
          public function removeProyectoActions(
            int $id,
            EntityManagerInterface $em,
            Request $request,
            TareaRepository $tareaRepository,
            ListaRepository $listaRepository,
            ProyectoRepository $proyectoRepository
         ){
            $proyecto = $proyectoRepository->find($id);
         if(!$proyecto){
            throw $this->createNotFoundException('Este proyecto no existe');
         }
         else{
            foreach($proyecto->getListas() as $lista);{
               foreach($lista->getTareas() as $tarea);{
                  $tareaRepository->remove($tarea);
               }
               $listaRepository->remove($lista);
            }
            $proyectoRepository->remove($proyecto);
         }
         }
   }
   