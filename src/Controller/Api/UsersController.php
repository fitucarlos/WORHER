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

class UsersController extends AbstractFOSRestController{
    

  /**
           * @Rest\Get(path="/proyecto/search_user/{id_proyecto}/{email}")
           * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
           */
      
          public function buscarUsuarioPorEmailEnProyecto(
            string $email,
            int $id_proyecto,
            ProyectoRepository $proyectoRepository
         ){

            $proyecto = $proyectoRepository->find($id_proyecto);
            if($proyecto) $usuarios = $proyecto->getUsuarios();
            else throw $this->createNotFoundException('Este proyecto no existe');
            $encontrado = false;
            foreach($usuarios as $usuario){
               if($usuario->getEmail() == $email){
                  return $usuario;
               }
            }
         if(!$encontrado){
            throw $this->createNotFoundException('Este usuario no existe');
         }
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
    * @Rest\Get(path="/proyecto/get_users/{id}")
    * @Rest\View(serializerGroups={"proyecto"}, serializerEnableMaxDepthChecks=true)
    */
    
    public function getUsersByProyectoActions(
        int $id,
        ProyectoRepository $proyectoRepository
        ){
           $proyecto = $proyectoRepository->find($id);
           if(!$proyecto){
              throw $this->createNotFoundException('Este proyecto no existe');
           }
 
       return $proyecto->getUsuarios();
       
  }
 

}
