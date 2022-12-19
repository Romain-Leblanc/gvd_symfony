<?php

namespace App\Repository;

use App\Entity\Marque;
use App\Entity\Modele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Marque>
 *
 * @method Marque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marque[]    findAll()
 * @method Marque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marque::class);
    }

    public function add(Marque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Marque $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithNombreModele()
    {
        return $this->createQueryBuilder('ma')
            ->select('ma.id')
            ->addSelect('ma.marque')
            ->addSelect('COUNT(mo.id) as nombre')
            ->innerJoin(Modele::class, 'mo', Join::WITH, 'ma.id = mo.fk_marque')
            ->groupBy('ma.id')
            ->getQuery()
            ->getResult()
            ;
    }
}
