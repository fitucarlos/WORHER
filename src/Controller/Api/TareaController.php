<?php

namespace App\Controller\Api;

use App\Entity\Proyecto;
use App\Entity\Lista;
use App\Entity\Mensaje;
use App\Entity\Tarea;
use App\Entity\Usuario;
use App\Form\Model\ProyectoDto;
use App\Form\Model\ListaDto;
use App\Form\Model\MensajeDto;
use App\Form\Model\TareaDto;
use App\Form\Model\UsuarioDto;
use App\Form\Type\ListaFormType;
use App\Form\Type\MensajeFormType;
use App\Form\Type\ProyectoFormType;
use App\Form\Type\TareaFormType;
use App\Repository\ProyectoRepository;
use App\Repository\ListaRepository;
use App\Repository\MensajeRepository;
use App\Repository\TareaRepository;
use App\Repository\UsuarioRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TareaController extends AbstractFOSRestController{

    
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

               if($newTareaDto->dificultad == 0){
                  $newTareaDto->dificultad = 1;
               }
               if($newTareaDto->prioridad == 0){
                  $newTareaDto->prioridad = 1;
               }
               if(!$tarea && $newTareaDto->nombre && $newTareaDto->dificultad && $newTareaDto->prioridad){
                  $tarea = new Tarea();
                  $tarea->setNombre($newTareaDto->nombre);
                  if($newTareaDto->descripcion){

                     $tarea->setDescripcion($newTareaDto->descripcion);
                  }
                  else{
                     $tarea->setDescripcion(null);
                  }
                  //comprobar dificultad
                  if($newTareaDto->dificultad <= 3 && $newTareaDto->dificultad > 0){
                     $tarea->setDificultad($newTareaDto->dificultad);
                  }
                  else{
                     if($newTareaDto->dificultad > 3){
                        $tarea->setDificultad(3);
                     }
                     else if($newTareaDto->dificultad == 0){
                        $tarea->setDificultad(1);
                     }
                     else{
                        $tarea->setDificultad(1);
                     }
                  }
                  //comprobar prioridad
                  if($newTareaDto->prioridad <= 5 && $newTareaDto->prioridad > 0){
                     $tarea->setPrioridad($newTareaDto->prioridad);
                  }
                  else{
                     if($newTareaDto->prioridad > 5){
                        $tarea->setPrioridad(5);
                     }
                     else if($newTareaDto->prioridad == 0){
                        $tarea->setPrioridad(1);
                     }
                     else{
                        $tarea->setPrioridad(1);
                     }
                  }
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
     * @Rest\Post(path="/edit_tarea/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

    public function editTareaActions(
        int $id,
        EntityManagerInterface $em,
        Request $request,
        TareaRepository $tareaRepository
        ){
         $tarea = $tareaRepository->find($id);
         if(!$tarea){
            throw $this->createNotFoundException('Esta lista no existe');
         }
         $tareaDto = TareaDto::createFromTarea($tarea);

         $form = $this->createForm(TareaFormType::class, $tareaDto);
         $form->handleRequest($request);
  
           if (!$form->isSubmitted()) {
              throw $this->createNotFoundException('Form not submitted');
          }
          if ($form->isValid()) {
              if($tareaDto->dificultad == 0){
                 $tareaDto->dificultad = 1;
              }
              if($tareaDto->prioridad == 0){
                 $tareaDto->prioridad = 1;
              }
              if($tareaDto->dificultad > 3){
                 $tarea->setDificultad(3);
              }
              else if($tareaDto->dificultad == 0){
                 $tarea->setDificultad(1);
              }
              else{
                 $tarea->setDificultad(1);
              }
  
              if($tareaDto->prioridad > 5){
                 $tarea->setPrioridad(5);
              }
              else if($tareaDto->prioridad == 0){
                 $tarea->setPrioridad(1);
              }
              else{
                 $tarea->setPrioridad(1);
              }
  
  
              if($tareaDto->nombre){
                 $tarea->setNombre($tareaDto->nombre);
              }

              $tarea->setDescripcion($tareaDto->descripcion);
              
              $em->persist($tarea);
              $em->flush();
              return $tarea;
           }
           
           return $form;
     }
  
  
   /**
           * @Rest\Get(path="/proyecto/change_tarea/{id_tarea}/{id_new_list}")
           * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
           */
      
          public function cambiarTareaDeLista(
            int $id_tarea,
            int $id_new_list,
            TareaRepository $tareaRepository,
            ListaRepository $listaRepository,
            EntityManagerInterface $em
         ){
            $tarea = $tareaRepository->find($id_tarea);
            if(!$tarea){
               throw $this->createNotFoundException('Esta tarea no existe');
            }
            $oldLista = $tarea->getLista();
            $newLista = $listaRepository->find($id_new_list);
            if(!$newLista){
               throw $this->createNotFoundException('Esta lista no existe');
            }
            $oldLista->removeTarea($tarea);
            $newLista->addTarea($tarea);

            $em->persist($tarea);
            $em->flush();

            return $newLista;
         }      


}