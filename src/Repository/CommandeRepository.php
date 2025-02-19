<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    // Add custom query methods here if needed. For example:

    // /**
    //  * @return Commande[] Returns an array of Commande objects
    //  */
    // public function findByUserName($userName): array
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.utilisateur = :userName') // Assuming "utilisateur" is the user's name field
    //         ->setParameter('userName', $userName)
    //         ->orderBy('c.id', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // /**
    //  * @return Commande|null
    //  */
    // public function findOneByUserNameAndProductName($userName, $productName): ?Commande
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.utilisateur = :userName')
    //         ->andWhere('c.nomProduit = :productName')
    //         ->setParameters([
    //             'userName' => $userName,
    //             'productName' => $productName,
    //         ])
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}