<?php

namespace App\Service;

use App\Entity\Livro;
use App\Exception\RelacionamentoInexistenteException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class LivroService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws RelacionamentoInexistenteException se um autor ou assunto vinculado não existir mais
     */
    public function criar(Livro $livro): void
    {
        try {
            $this->em->persist($livro);
            $this->em->flush();
        } catch (ForeignKeyConstraintViolationException) {
            throw new RelacionamentoInexistenteException('livro.relacionamento_invalido');
        }
    }

    /**
     * @throws RelacionamentoInexistenteException se um autor ou assunto vinculado não existir mais
     */
    public function atualizar(Livro $livro): void
    {
        try {
            $this->em->flush();
        } catch (ForeignKeyConstraintViolationException) {
            throw new RelacionamentoInexistenteException('livro.relacionamento_invalido');
        }
    }

    public function excluir(Livro $livro): void
    {
        $this->em->remove($livro);
        $this->em->flush();
    }
}
