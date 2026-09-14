<?php

namespace App\Entity;

use App\Repository\AssuntoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssuntoRepository::class)]
#[ORM\Table(name: 'assunto')]
#[ORM\UniqueConstraint(name: 'uniq_assunto_descricao', columns: ['descricao'])]
#[UniqueEntity(fields: ['descricao'], message: 'Já existe um assunto com esta descrição.')]
class Assunto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cod_as', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'descricao', length: 20, unique: true)]
    #[Assert\NotBlank(message: 'Informe a descrição do assunto.')]
    #[Assert\Length(max: 20, maxMessage: 'A descrição deve ter no máximo {{ limit }} caracteres.')]
    private ?string $descricao = null;

    /** @var Collection<int, Livro> */
    #[ORM\ManyToMany(targetEntity: Livro::class, mappedBy: 'assuntos')]
    private Collection $livros;

    public function __construct()
    {
        $this->livros = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): static
    {
        $this->descricao = $descricao;

        return $this;
    }

    /** @return Collection<int, Livro> */
    public function getLivros(): Collection
    {
        return $this->livros;
    }

    public function __toString(): string
    {
        return $this->descricao ?? '';
    }
}
