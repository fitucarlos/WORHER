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
     * @Rest\Get(path="/proyecto/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function getProyectoActions(
        int $id,
        ProyectoRepository $ProyectoRepository
     ){
        $proyecto = $ProyectoRepository->find($id);
        return $proyecto;
     }

    /**
     * @Rest\Post(path="/proyecto")
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

               if($newTareaDto->dificultad == 0){
                  $newTareaDto->dificultad = 1;
               }
               if($newTareaDto->prioridad == 0){
                  $newTareaDto->prioridad = 1;
               }
               if(!$tarea && $newTareaDto->nombre && $newTareaDto->descripcion && $newTareaDto->dificultad && $newTareaDto->prioridad){
                  $tarea = new Tarea();
                  $tarea->setNombre($newTareaDto->nombre);
                  $tarea->setDescripcion($newTareaDto->descripcion);
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
      /**
           * @Rest\Get(path="/proyecto/search_user/{email}")
           * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
           */
      
          public function buscarUsuarioPorEmail(
            string $email,
            EntityManagerInterface $em,
            Request $request,
            UsuarioRepository $usuarioRepository
         ){
            $usuario = $usuarioRepository->findByEmail($email);
         if(!$usuario){
            throw $this->createNotFoundException('Este usuario no existe');
         }
         else{
            return $usuario;
         }
         }


         
    /**
     * @Rest\Post(path="/edit_proyecto/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function editProyectoActions(
      int $id,
      EntityManagerInterface $em,
      Request $request,
      ProyectoRepository $proyectoRepository
      ){
         $Proyecto = $proyectoRepository->find($id);
         $ProyectoDto = ProyectoDto::createFromProyecto($Proyecto);

         $form = $this->createForm(ProyectoFormType::class, $ProyectoDto);
         $form->handleRequest($request);

         if (!$form->isSubmitted()) {
            throw $this->createNotFoundException('Form not submitted');
        }
        if ($form->isValid()) {
            $Proyecto->setNombre($ProyectoDto->nombre);
            $em->persist($Proyecto);
            $em->flush();
            return $Proyecto;
         }
         
         return $form;
   }

    /**
     * @Rest\Post(path="/edit_lista/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function editListaActions(
      int $id,
      EntityManagerInterface $em,
      Request $request,
      ListaRepository $listaRepository
      ){
         $Lista = $listaRepository->find($id);
         $ListaDto = ListaDto::createFromLista($Lista);

         $form = $this->createForm(ListaFormType::class, $ListaDto);
         $form->handleRequest($request);

         if (!$form->isSubmitted()) {
            throw $this->createNotFoundException('Form not submitted');
        }
        if ($form->isValid()) {
            $Lista->setNombre($ListaDto->nombre);
            $em->persist($Lista);
            $em->flush();
            return $Lista;
         }
         
         return $form;
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
            if($tareaDto->descripcion){
               $tarea->setDescripcion($tareaDto->descripcion);
            }
            $em->persist($tarea);
            $em->flush();
            return $tarea;
         }
         
         return $form;
   }

   
    /**
     * @Rest\Post(path="/proyecto/add_user/{id}/{user}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

    public function addUserActions(
      int $id,
      string $user,
      EntityManagerInterface $em,
      Request $request,
      ProyectoRepository $proyectoRepository,
      UsuarioRepository $usuarioRepository
   ){
    $Proyecto = $proyectoRepository->find($id);
    if(!$Proyecto){
       throw $this->createNotFoundException('Este proyecto no existe');
    }
    $ProyectoDto = ProyectoDto::createFromProyecto($Proyecto);



       //Add usuario
       $usuario = $usuarioRepository->find($user);
       if(!$usuario){
         throw $this->createNotFoundException('Este usuario no existe');
      }
             $Proyecto->addUsuario($usuario);
       
       $em->persist($Proyecto);
       $em->flush();
       $em->refresh($Proyecto);
       return $Proyecto;
    
 }

/**
     * @Rest\Post(path="/proyecto/add_mensaje/{id}/{user}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

    public function addMensajeActions(
      int $id,
      int $user,
      EntityManagerInterface $em,
      Request $request,
      ProyectoRepository $proyectoRepository,
      UsuarioRepository $usuarioRepository
   ){
      $Proyecto = $proyectoRepository->find($id);
      $usuario = $usuarioRepository->find($user);
      $now = new DateTime();
      $MensajeDto = new MensajeDto();
      $form = $this->createForm(MensajeFormType::class, $MensajeDto);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()){
          $Mensaje = new Mensaje();
          $Mensaje->setTexto($MensajeDto->texto);
          $Mensaje->setFecha($now);
          $Mensaje->setHora($now);
          $Mensaje->setUsuario($usuario);
          $Proyecto->addMensaje($Mensaje);
          $em->persist($Mensaje);
          $em->flush();
          return $Proyecto;
      }
      return $form;
 }
/**
     * @Rest\Get(path="/proyecto/get_mensajes/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

    public function getMensajesActions(
      int $id,
      MensajeRepository $mensajeRepository
   ){
      $mensajes = $mensajeRepository->findByProyecto($id);

      return $mensajes;
      
 }


/**
     * @Rest\Get(path="/proyecto/get_users/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

    public function getUsersByProyectoActions(
      int $id,
      ProyectoRepository $proyectoRepository
   ){
      $proyecto = $proyectoRepository->find($id);

      return $proyecto->getUsuarios();
      
 }


   }
   