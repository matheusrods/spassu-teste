<?php

namespace App\Service;

use App\Entity\Autor;
use App\Exception\AutorDuplicadoException;
use App\Exception\RegistroVinculadoException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class AutorService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws AutorDuplicadoException se o nome já existir
     */
    public function criar(Autor $autor): void
    {
        try {
            $this->em->persist($autor);
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new AutorDuplicadoException('autor.duplicado');
        }
    }

    /**
     * @throws AutorDuplicadoException se o nome já existir
     */
    public function atualizar(Autor $autor): void
    {
        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new AutorDuplicadoException('autor.duplicado');
        }
    }

    /**
     * @throws RegistroVinculadoException se o autor ainda tiver livros vinculados
     */
    public function excluir(Autor $autor): void
    {
        if (!$autor->getLivros()->isEmpty()) {
            throw new RegistroVinculadoException('autor.vinculado');
        }

        try {
            $this->em->remove($autor);
            $this->em->flush();
        } catch (ForeignKeyConstraintViolationException) {
            // Rede de segurança contra corrida entre a checagem acima e o flush.
            throw new RegistroVinculadoException('autor.vinculado');
        }
    }
}
