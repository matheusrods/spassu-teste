<?php

namespace App\Repository;

use App\Entity\Livro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livro>
 */
class LivroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livro::class);
    }

    /**
     * Faz fetch join de autores/assuntos para evitar N+1 ao listar (a
     * listagem exibe as duas colunas para cada livro). Os filtros de
     * autor/assunto usam um JOIN à parte (não o do fetch join) para não
     * restringir a coleção completa que é exibida.
     */
    public function findFiltrados(?string $titulo, ?int $autorId, ?int $assuntoId): Query
    {
        $qb = $this->createQueryBuilder('l')
            ->addSelect('autores', 'assuntos')
            ->leftJoin('l.autores', 'autores')
            ->leftJoin('l.assuntos', 'assuntos')
            ->orderBy('l.titulo', 'ASC');

        if ($titulo !== null && $titulo !== '') {
            $qb->andWhere('l.titulo LIKE :titulo')->setParameter('titulo', '%'.$titulo.'%');
        }

        if ($autorId !== null) {
            $qb->join('l.autores', 'filtroAutor')
                ->andWhere('filtroAutor.id = :autorId')
                ->setParameter('autorId', $autorId);
        }

        if ($assuntoId !== null) {
            $qb->join('l.assuntos', 'filtroAssunto')
                ->andWhere('filtroAssunto.id = :assuntoId')
                ->setParameter('assuntoId', $assuntoId);
        }

        return $qb->getQuery();
    }
}
