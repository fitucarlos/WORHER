<?php

namespace App\Entity;

use App\Repository\ProyectoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProyectoRepository::class)
 */
class Proyecto
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
     * @ORM\ManyToMany(targetEntity=Usuario::class, inversedBy="proyectos")
     */
    private $usuarios;

    /**
     * @ORM\OneToMany(targetEntity=Mensaje::class, mappedBy="proyecto")
     */
    private $mensajes;

    /**
     * @ORM\OneToMany(targetEntity=Lista::class, mappedBy="proyecto")
     */
    private $listas;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->mensajes = new ArrayCollection();
        $this->listas = new ArrayCollection();
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

    /**
     * @return Collection<int, Mensaje>
     */
    public function getMensajes(): Collection
    {
        return $this->mensajes;
    }

    public function addMensaje(Mensaje $mensaje): self
    {
        if (!$this->mensajes->contains($mensaje)) {
            $this->mensajes[] = $mensaje;
            $mensaje->setProyecto($this);
        }

        return $this;
    }

    public function removeMensaje(Mensaje $mensaje): self
    {
        if ($this->mensajes->removeElement($mensaje)) {
            // set the owning side to null (unless already changed)
            if ($mensaje->getProyecto() === $this) {
                $mensaje->setProyecto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lista>
     */
    public function getListas(): Collection
    {
        return $this->listas;
    }

    public function addLista(Lista $lista): self
    {
        if (!$this->listas->contains($lista)) {
            $this->listas[] = $lista;
            $lista->setProyecto($this);
        }

        return $this;
    }

    public function removeLista(Lista $lista): self
    {
        if ($this->listas->removeElement($lista)) {
            // set the owning side to null (unless already changed)
            if ($lista->getProyecto() === $this) {
                $lista->setProyecto(null);
            }
        }

        return $this;
    }
}
