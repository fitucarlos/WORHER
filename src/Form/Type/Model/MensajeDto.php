<?php

namespace App\Form\Model;

use App\Entity\Mensaje;

class MensajeDto{
    public $id;
    public $texto;
    public $fecha;
    public $hora;


    public static function createFromMensaje(Mensaje $Mensaje):self
    {
        $dto = new self();
        $dto->id = $Mensaje->getId();
        $dto->texto = $Mensaje->getTexto();
        $dto->hora = $Mensaje->getHora();
        $dto->fecha = $Mensaje->getFecha();
        return $dto;
    }
}