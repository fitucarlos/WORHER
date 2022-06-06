<?php

namespace App\Controller\Api;

use App\Entity\Proyecto;
use App\Entity\Lista;
use App\Entity\Mensaje;
use App\Form\Model\ProyectoDto;
use App\Form\Model\ListaDto;
use App\Form\Model\MensajeDto;
use App\Form\Type\ListaFormType;
use App\Form\Type\MensajeFormType;
use App\Form\Type\ProyectoFormType;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProyectoController extends AbstractFOSRestController{
    /**
     * @Rest\Get(path="/proyectos/{id}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function getActions(
        int $id,
        UsuarioRepository $usuarioRepository
     ){
        $usuario = $usuarioRepository->find($id);
        $proyectos = $usuario->getProyectos();
        return $proyectos;
     }

    /**
     * @Rest\Get(path="/proyecto/{id}/{id_usuario}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function getProyectoActions(
        int $id,
        int $id_usuario,
        ProyectoRepository $ProyectoRepository,
        UsuarioRepository $usuarioRepository
     ){
        $proyecto = $ProyectoRepository->find($id);
        $bool = false;
        $usuario = $usuarioRepository->find($id_usuario);
        if(!$proyecto){
         throw $this->createNotFoundException('Este proyecto no existe');
      }
        foreach($proyecto->getUsuarios() as $usuario_proyecto){
         if($usuario_proyecto == $usuario){
            $bool = true;
            break;
         }
        }
        if($bool){
           
           return $proyecto;
        }
        else{
         throw $this->createNotFoundException('No es ningún proyecto tuyo');
        }
     }

    /**
     * @Rest\Post(path="/proyecto/{id_usuario}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

     public function postActions(
        int $id_usuario,
        EntityManagerInterface $em,
        UsuarioRepository $usuarioRepository,
        Request $request
     ){
        $usuario = $usuarioRepository->find($id_usuario);
        $ProyectoDto = new ProyectoDto();
        $form = $this->createForm(ProyectoFormType::class, $ProyectoDto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $Proyecto = new Proyecto();
            $Proyecto->setNombre($ProyectoDto->nombre);
            $Proyecto->addUsuario($usuario);
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
        * @Rest\Post(path="/proyecto/remove_lista/{id}")
        * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
        */
        
        public function removeListaActions(
         int $id,
         TareaRepository $tareaRepository,
         EntityManagerInterface $em,
         ListaRepository $listaRepository
         ){
         $lista = $listaRepository->find($id);
      if(!$lista){
         throw $this->createNotFoundException('Esta lista no existe');
      }
      else{
         if(count($lista->getTareas()) >= 0){
            $tareas = $lista->getTareas();
            $i = 0;
            if($tareas){
               while(count($tareas)>0){
                  $tareaRepository->remove($tareas[$i]);
                  $i++;
               }
               $listaRepository->remove($lista);
            }
         }
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
            ProyectoRepository $proyectoRepository,
            MensajeRepository $mensajeRepository
         ){
            $proyecto = $proyectoRepository->find($id);
         if(!$proyecto){
            throw $this->createNotFoundException('Este proyecto no existe');
         }
         else{
            $listas = $proyecto->getListas();
            $j = 0;
            if($listas){
            while(count($listas)>0){
               $tareas = $listas[$j]->getTareas();
               $i = 0;
               if($tareas){
               while(count($tareas)>0){
                  
                  $tareaRepository->remove($tareas[$i]);
                  $i++;
               }
            }
               $listaRepository->remove($listas[$j]);
               $j++;
            }
         }
            $mensajes = $proyecto->getMensajes();
            if($mensajes){
            foreach ($mensajes as $mensaje) {
               $mensajeRepository->remove($mensaje);
            }
         }
         $proyectoRepository->remove($proyecto);
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
         if(!$Proyecto){
            throw $this->createNotFoundException('Este proyecto no existe');
         }
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
         if(!$Lista){
            throw $this->createNotFoundException('Esta lista no existe');
         }
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
      if(!$Proyecto){
         throw $this->createNotFoundException('Este proyecto no existe');
      }
      $usuario = $usuarioRepository->find($user);
      if(!$usuario){
         throw $this->createNotFoundException('Este usuario no existe');
      }
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
      if(!$mensajes){
         throw $this->createNotFoundException('Este proyecto no existe');
      }
      
      return $mensajes;
      
   }
 
/**
     * @Rest\Get(path="/proyecto/exit_proyecto/{id}/{id_usuario}")
     * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
     */

    public function exitProyecto(
      int $id,
      int $id_usuario,
      EntityManagerInterface $em,
      TareaRepository $tareaRepository,
      ListaRepository $listaRepository,
      ProyectoRepository $proyectoRepository,
      MensajeRepository $mensajeRepository,
      UsuarioRepository $usuarioRepository
   ){
      $usuario = $usuarioRepository->find($id_usuario);
      if(!$usuario){
         throw $this->createNotFoundException('Este usuario no existe');
      }
      $proyecto = $proyectoRepository->find($id);
      if(!$proyecto){
         throw $this->createNotFoundException('Este proyecto no existe');
      }

      $proyecto->removeUsuario($usuario);
      if(count($proyecto->getUsuarios())<=0){
         $listas = $proyecto->getListas();
         $j = 0;
         if($listas){
         while(count($listas)>0){
            $tareas = $listas[$j]->getTareas();
            $i = 0;
            if($tareas){
            while(count($tareas)>0){
               
               $tareaRepository->remove($tareas[$i]);
               $i++;
            }
         }
            $listaRepository->remove($listas[$j]);
            $j++;
         }
      }
         $mensajes = $proyecto->getMensajes();
         if($mensajes){
         foreach ($mensajes as $mensaje) {
            $mensajeRepository->remove($mensaje);
         }
      }
      $proyectoRepository->remove($proyecto);
      }

      $em->persist($proyecto);
      $em->flush();
   }

}
   