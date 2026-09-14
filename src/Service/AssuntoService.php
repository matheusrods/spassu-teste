<?php

namespace App\Service;

use App\Entity\Assunto;
use App\Exception\AssuntoDuplicadoException;
use App\Exception\RegistroVinculadoException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class AssuntoService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws AssuntoDuplicadoException se a descrição já existir
     */
    public function criar(Assunto $assunto): void
    {
        try {
            $this->em->persist($assunto);
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new AssuntoDuplicadoException('assunto.duplicado');
        }
    }

    /**
     * @throws AssuntoDuplicadoException se a descrição já existir
     */
    public function atualizar(Assunto $assunto): void
    {
        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            throw new AssuntoDuplicadoException('assunto.duplicado');
        }
    }

    /**
     * @throws RegistroVinculadoException se o assunto ainda tiver livros vinculados
     */
    public function excluir(Assunto $assunto): void
    {
        if (!$assunto->getLivros()->isEmpty()) {
            throw new RegistroVinculadoException('assunto.vinculado');
        }

        try {
            $this->em->remove($assunto);
            $this->em->flush();
        } catch (ForeignKeyConstraintViolationException) {
            // Rede de segurança contra corrida entre a checagem acima e o flush.
            throw new RegistroVinculadoException('assunto.vinculado');
        }
    }
}
