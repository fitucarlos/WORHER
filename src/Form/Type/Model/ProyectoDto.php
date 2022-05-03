<?php

namespace App\Form\Model;

use App\Entity\Proyecto;

class ProyectoDto{
    public $nombre;
    public $listas;
    public $mensajes;

    public function __construct() {
        $this->listas = [];
        $this->usuarios = [];
        $this->mensajes = [];
    }

    public static function createFromProyecto(Proyecto $Proyecto):self
    {
        $dto = new self();
        $dto->nombre = $Proyecto->getNombre();
        return $dto;
    }
}