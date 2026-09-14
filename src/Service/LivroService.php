<?php

namespace App\Service;

use App\Entity\Livro;
use Doctrine\ORM\EntityManagerInterface;

class LivroService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function criar(Livro $livro): void
    {
        $this->em->persist($livro);
        $this->em->flush();
    }

    public function atualizar(Livro $livro): void
    {
        $this->em->flush();
    }

    public function excluir(Livro $livro): void
    {
        $this->em->remove($livro);
        $this->em->flush();
    }
}
