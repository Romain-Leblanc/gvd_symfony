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
            ->set('v.fk_client', ":id_client")
            ->set('v.fk_marque', ":id_marque")
            ->set('v.fk_modele', ":id_modele")
            ->set('v.fk_carburant', ":id_carburant")
            ->set('v.immatriculation', ":immatriculation")
            ->set('v.annee', ":annee")
            ->set('v.kilometrage', ":kilometrage")
            ->set('v.fk_etat', ":id_etat")
            ->where('v.id = :id_vehicule')
            ->setParameter("id_vehicule", $vehicule->getId())
            ->setParameter("id_client", $vehicule->getFkClient()->getId())
            ->setParameter("id_marque", $vehicule->getFkMarque()->getId())
            ->setParameter("id_modele", $vehicule->getFkModele()->getId())
            ->setParameter("id_carburant", $vehicule->getFkCarburant()->getId())
            ->setParameter("immatriculation", $vehicule->getImmatriculation())
            ->setParameter("annee", $vehicule->getAnnee())
            ->setParameter("kilometrage", $vehicule->getKilometrage())
            ->setParameter("id_etat", $vehicule->getFkEtat()->getId())
            ->getQuery()
            ->getResult()
            ;
    }
}
