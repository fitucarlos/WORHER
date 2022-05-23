<?php

namespace App\Form\Model;

use App\Entity\Tarea;

class TareaDto{
    public $id;
    public $nombre;
    public $descripcion;
    public $dificultad;
    public $prioridad;

    public function __construct() {
        $this->usuarios = [];
    }

    public static function createFromTarea(Tarea $Tarea):self
    {
        $dto = new self();
        $dto->id = $Tarea->getId();
        $dto->nombre = $Tarea->getNombre();
        $dto->descripcion = $Tarea->getDescripcion();
        $dto->dificultad = $Tarea->getDificultad();
        $dto->prioridad = $Tarea->getPrioridad();
        return $dto;
    }
}