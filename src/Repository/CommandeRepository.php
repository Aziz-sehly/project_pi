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

    // Example of a custom query to find orders by product
    public function findByProduct($productId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.product = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Example of a custom query to find all orders within a price range
    public function findOrdersByPriceRange($minPrice, $maxPrice)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.price BETWEEN :minPrice AND :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->getQuery()
            ->getResult();
    }

    // Additional useful methods can be added here, such as:
    // - find orders by customer, status, date range, etc.
}
