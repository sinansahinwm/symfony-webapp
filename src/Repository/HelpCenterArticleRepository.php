<?php

namespace App\Repository;

use App\Entity\HelpCenterArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpCenterArticle>
 *
 * @method HelpCenterArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpCenterArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpCenterArticle[]    findAll()
 * @method HelpCenterArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpCenterArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpCenterArticle::class);
    }

    //    /**
    //     * @return HelpCenterArticle[] Returns an array of HelpCenterArticle objects
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

    //    public function findOneBySomeField($value): ?HelpCenterArticle
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
