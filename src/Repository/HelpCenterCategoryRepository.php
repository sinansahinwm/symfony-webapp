<?php

namespace App\Repository;

use App\Entity\HelpCenterCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpCenterCategory>
 *
 * @method HelpCenterCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpCenterCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpCenterCategory[]    findAll()
 * @method HelpCenterCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpCenterCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpCenterCategory::class);
    }

    //    /**
    //     * @return HelpCenterCategory[] Returns an array of HelpCenterCategory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HelpCenterCategory
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
