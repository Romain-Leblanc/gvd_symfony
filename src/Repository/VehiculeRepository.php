<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 *
 * @method Vehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicule[]    findAll()
 * @method Vehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function add(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /* Met à jour les informations d'un véhicule */
    public function updateVehicule(Vehicule $vehicule) {
        return $this->createQueryBuilder('u')
            ->update(Vehicule::class, 'v')
            ->set('v.FK_Carburant', ":carburant")
            ->set('v.Immatriculation', ":immatriculation")
            ->set('v.Annee', ":annee")
            ->set('v.Kilometrage', ":kilometrage")
            ->where('v.id = :idvehicule')
            ->setParameter("idvehicule", $vehicule->getId())
            ->setParameter("carburant", $vehicule->getFKCarburant()->getId())
            ->setParameter("immatriculation", $vehicule->getImmatriculation())
            ->setParameter("annee", $vehicule->getAnnee())
            ->setParameter("kilometrage", $vehicule->getKilometrage())
            ->getQuery()
            ->getResult()
            ;
    }
}
