<?php

namespace App\Repository;

use App\Entity\Assunto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assunto>
 */
class AssuntoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assunto::class);
    }

    public function findAllOrdenadosPorDescricao(): Query
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.descricao', 'ASC')
            ->getQuery();
    }

    public function findFiltrados(?string $descricao): Query
    {
        $qb = $this->createQueryBuilder('a')->orderBy('a.descricao', 'ASC');

        if ($descricao !== null && $descricao !== '') {
            $qb->andWhere('a.descricao LIKE :descricao')->setParameter('descricao', '%'.$descricao.'%');
        }

        return $qb->getQuery();
    }
}
