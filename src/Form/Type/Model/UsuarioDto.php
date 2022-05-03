<?php

namespace App\Form\Model;

use App\Entity\Usuario;

class UsuarioDto{
    public $id;
    public $nombre;
    public $apellido;
    public $password;
    public $email;


    public static function createFromUsuario(Usuario $Usuario):self
    {
        $dto = new self();
        $dto->id = $Usuario->getId();
        $dto->nombre = $Usuario->getNombre();
        $dto->email = $Usuario->getEmail();
        $dto->apellido = $Usuario->getApellido();
        return $dto;
    }
}