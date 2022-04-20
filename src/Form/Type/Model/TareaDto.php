<?php

namespace App\Form\Model;

use App\Entity\Tarea;

class TareaDto{
    public $id;
    public $nombre;
    public $descripcion;
    public $dificultad;
    public $prioridad;

    public static function createFromTarea(Tarea $Tarea):self
    {
        $dto = new self();
        $dto->id = $Tarea->getId();
        $dto->nombre = $Tarea->getNombre();
        $dto->nombre = $Tarea->getDescripcion();
        $dto->nombre = $Tarea->getDificultad();
        $dto->nombre = $Tarea->getPrioridad();
        return $dto;
    }
}