<?php

namespace App\Form\Model;

use App\Entity\Lista;

class ListaDto{
    public $id;
    public $nombre;
    public $tareas;

    public function __construct() {
        $this->tareas = [];
    }

    public static function createFromLista(Lista $lista):self
    {
        $dto = new self();
        $dto->id = $lista->getId();
        $dto->nombre = $lista->getNombre();
        return $dto;
    }
}