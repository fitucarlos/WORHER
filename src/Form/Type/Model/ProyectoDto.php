<?php

namespace App\Form\Model;

use App\Entity\Proyecto;

class ProyectoDto{
    public $nombre;
    public $listas;

    public function __construct() {
        $this->listas = [];
        $this->usuarios = [];
    }

    public static function createFromProyecto(Proyecto $Proyecto):self
    {
        $dto = new self();
        $dto->nombre = $Proyecto->getNombre();
        return $dto;
    }
}