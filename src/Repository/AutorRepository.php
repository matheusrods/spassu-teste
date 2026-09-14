<?php

namespace App\Repository;

use App\Entity\Autor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Autor>
 */
class AutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autor::class);
    }

    public function findAllOrdenadosPorNome(): Query
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.nome', 'ASC')
            ->getQuery();
    }

    public function findFiltrados(?string $nome): Query
    {
        $qb = $this->createQueryBuilder('a')->orderBy('a.nome', 'ASC');

        if ($nome !== null && $nome !== '') {
            $qb->andWhere('a.nome LIKE :nome')->setParameter('nome', '%'.$nome.'%');
        }

        return $qb->getQuery();
    }
}
