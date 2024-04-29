<?php

namespace App\Repository;

use App\Entity\Panier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Panier>
 *
 * @method Panier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panier[]    findAll()
 * @method Panier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }
    // src/Repository/PanierRepository.php

    public function findGreaterThanQuantity(int $quantiteFiltre): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.qte > :quantiteFiltre')
            ->setParameter('quantiteFiltre', $quantiteFiltre)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findGreaterThanSubtotal(float $sousTotalFiltre): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.sousTotal > :sousTotalFiltre')
            ->setParameter('sousTotalFiltre', $sousTotalFiltre)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findWithFilters(?int $quantiteFiltre, ?float $sousTotalFiltre): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($quantiteFiltre !== null) {
            $qb->andWhere('p.qte > :quantiteFiltre')
                ->setParameter('quantiteFiltre', $quantiteFiltre);
        }

        if ($sousTotalFiltre !== null) {
            $qb->andWhere('p.sousTotal > :sousTotalFiltre')
                ->setParameter('sousTotalFiltre', $sousTotalFiltre);
        }

        return $qb->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }




//    /**
//     * @return Panier[] Returns an array of Panier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Panier
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}