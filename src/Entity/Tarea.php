<?php

namespace App\Entity;

use App\Repository\TareaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TareaRepository::class)
 */
class Tarea
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="integer")
     */
    private $dificultad;

    /**
     * @ORM\Column(type="integer")
     */
    private $prioridad;

    /**
     * @ORM\ManyToOne(targetEntity=Lista::class, inversedBy="tareas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lista;

    /**
     * @ORM\ManyToMany(targetEntity=Usuario::class, inversedBy="tareas")
     */
    private $usuarios;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getDificultad(): ?int
    {
        return $this->dificultad;
    }

    public function setDificultad(int $dificultad): self
    {
        $this->dificultad = $dificultad;

        return $this;
    }

    public function getPrioridad(): ?int
    {
        return $this->prioridad;
    }

    public function setPrioridad(int $prioridad): self
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    public function getLista(): ?Lista
    {
        return $this->lista;
    }

    public function setLista(?Lista $lista): self
    {
        $this->lista = $lista;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        $this->usuarios->removeElement($usuario);

        return $this;
    }
}
