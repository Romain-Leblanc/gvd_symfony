<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Intervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intervention>
 *
 * @method Intervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervention[]    findAll()
 * @method Intervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    public function add(Intervention $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Intervention $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function updateIntervention(Intervention $intervention) {
        return $this->createQueryBuilder('u')
            ->update(Intervention::class, 'i')
            ->set('i.date_intervention', ":date_intervention")
            ->set('i.duree_intervention', ":duree_intervention")
            ->set('i.detail_intervention', ":detail_intervention")
            ->set('i.montant_ht', ":montant")
            ->set('i.fk_etat', ":etat")
            ->where('i.id = :id_intervention')
            ->setParameter("id_intervention", $intervention->getId())
            ->setParameter("date_intervention",$intervention->getDateIntervention())
            ->setParameter("duree_intervention",$intervention->getDureeIntervention())
            ->setParameter("detail_intervention", $intervention->getDetailIntervention())
            ->setParameter("montant", $intervention->getMontantHT())
            ->setParameter("etat", $intervention->getFKEtat()->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    public function updateInterventionByEtatAndNumFacture(array $idIntervention, int $idEtat, int $idFacture)
    {
        $query = $this->createQueryBuilder('f');
        return $query
            ->update(Intervention::class, 'i')
            ->set('i.fk_etat', ":id_etat")
            ->set('i.fk_facture', ":id_facture")
            ->where($query->expr()->in("i.id", ":id_intervention"))
            ->setParameter("id_etat", $idEtat)
            ->setParameter("id_facture", $idFacture)
            ->setParameter("id_intervention", $idIntervention)
            ->getQuery()
            ->getResult();
    }
}
