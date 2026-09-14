<?php

namespace App\Entity;

use App\Repository\LivroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivroRepository::class)]
#[ORM\Table(name: 'livro')]
class Livro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'cod_l', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'titulo', length: 40)]
    #[Assert\NotBlank(message: 'Informe o título do livro.')]
    #[Assert\Length(max: 40, maxMessage: 'O título deve ter no máximo {{ limit }} caracteres.')]
    private ?string $titulo = null;

    #[ORM\Column(name: 'editora', length: 40)]
    #[Assert\NotBlank(message: 'Informe a editora.')]
    #[Assert\Length(max: 40, maxMessage: 'A editora deve ter no máximo {{ limit }} caracteres.')]
    private ?string $editora = null;

    #[ORM\Column(name: 'edicao', type: 'integer')]
    #[Assert\NotBlank(message: 'Informe a edição.')]
    #[Assert\Positive(message: 'A edição deve ser um número positivo.')]
    private ?int $edicao = null;

    #[ORM\Column(name: 'ano_publicacao', length: 4)]
    #[Assert\NotBlank(message: 'Informe o ano de publicação.')]
    #[Assert\Regex(pattern: '/^\d{4}$/', message: 'Informe um ano válido com 4 dígitos.')]
    private ?string $anoPublicacao = null;

    #[ORM\Column(name: 'valor', type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Informe o valor do livro.')]
    #[Assert\PositiveOrZero(message: 'O valor não pode ser negativo.')]
    private ?string $valor = null;

    /** @var Collection<int, Autor> */
    #[ORM\ManyToMany(targetEntity: Autor::class, inversedBy: 'livros')]
    #[ORM\JoinTable(name: 'livro_autor')]
    #[ORM\JoinColumn(name: 'livro_cod_l', referencedColumnName: 'cod_l')]
    #[ORM\InverseJoinColumn(name: 'autor_cod_au', referencedColumnName: 'cod_au')]
    #[Assert\Count(min: 1, minMessage: 'Informe ao menos um autor.')]
    private Collection $autores;

    /** @var Collection<int, Assunto> */
    #[ORM\ManyToMany(targetEntity: Assunto::class, inversedBy: 'livros')]
    #[ORM\JoinTable(name: 'livro_assunto')]
    #[ORM\JoinColumn(name: 'livro_cod_l', referencedColumnName: 'cod_l')]
    #[ORM\InverseJoinColumn(name: 'assunto_cod_as', referencedColumnName: 'cod_as')]
    #[Assert\Count(min: 1, minMessage: 'Informe ao menos um assunto.')]
    private Collection $assuntos;

    public function __construct()
    {
        $this->autores = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getEditora(): ?string
    {
        return $this->editora;
    }

    public function setEditora(string $editora): static
    {
        $this->editora = $editora;

        return $this;
    }

    public function getEdicao(): ?int
    {
        return $this->edicao;
    }

    public function setEdicao(int $edicao): static
    {
        $this->edicao = $edicao;

        return $this;
    }

    public function getAnoPublicacao(): ?string
    {
        return $this->anoPublicacao;
    }

    public function setAnoPublicacao(string $anoPublicacao): static
    {
        $this->anoPublicacao = $anoPublicacao;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): static
    {
        $this->valor = $valor;

        return $this;
    }

    /** @return Collection<int, Autor> */
    public function getAutores(): Collection
    {
        return $this->autores;
    }

    public function addAutor(Autor $autor): static
    {
        if (!$this->autores->contains($autor)) {
            $this->autores->add($autor);
        }

        return $this;
    }

    public function removeAutor(Autor $autor): static
    {
        $this->autores->removeElement($autor);

        return $this;
    }

    /** @return Collection<int, Assunto> */
    public function getAssuntos(): Collection
    {
        return $this->assuntos;
    }

    public function addAssunto(Assunto $assunto): static
    {
        if (!$this->assuntos->contains($assunto)) {
            $this->assuntos->add($assunto);
        }

        return $this;
    }

    public function removeAssunto(Assunto $assunto): static
    {
        $this->assuntos->removeElement($assunto);

        return $this;
    }

    public function __toString(): string
    {
        return $this->titulo ?? '';
    }
}
