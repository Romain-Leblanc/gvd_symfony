<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function add(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function updateFacture(Facture $facture) {
        return $this->createQueryBuilder('u')
            ->update(Facture::class, 'f')
            ->set('f.date_facture', ":date_facture")
            ->set('f.date_paiement', ":date_paiement")
            ->set('f.fk_moyen_paiement', ":moyen_paiement")
            ->where('f.id = :id_facture')
            ->setParameter("id_facture", $facture->getId())
            ->setParameter("date_facture", $facture->getDateFacture()->format('Y-m-d'))
            ->setParameter("date_paiement", $facture->getDatePaiement()->format('Y-m-d'))
            ->setParameter("moyen_paiement", $facture->getFKMoyenPaiement()->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByMoisAnnee(string $annee)
    {
        $annee = $annee."%";

        return $this->createQueryBuilder('f')
            ->select('YEAR(f.date_facture) as annee')
            ->addSelect('MONTHNAME(f.date_facture) as mois')
            ->addSelect('COUNT(f.id) as nombre')
            ->addSelect('SUM(f.montant_ht) as montant')
            ->where('f.date_facture LIKE :date_facture')
            ->setParameter('date_facture', $annee)
            ->groupBy('annee')
            ->addGroupBy('mois')
            ->orderBy('MONTH(f.date_facture)')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByMois(string $annee, string $mois)
    {
        $annee = $annee."%";

        return $this->createQueryBuilder('f')
            ->select('YEAR(f.date_facture) as annee')
            ->addSelect('MONTHNAME(f.date_facture) as mois')
            ->addSelect('COUNT(f.id) as nombre')
            ->addSelect('SUM(f.montant_ht) as montant')
            ->where('f.date_facture LIKE :annee')
            ->andWhere('MONTHNAME(f.date_facture) LIKE :mois')
            ->setParameter('annee', $annee)
            ->setParameter('mois', $mois)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
