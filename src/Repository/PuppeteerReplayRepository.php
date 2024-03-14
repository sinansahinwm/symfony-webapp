<?php

namespace App\Repository;

use App\Entity\PuppeteerReplay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PuppeteerReplay>
 *
 * @method PuppeteerReplay|null find($id, $lockMode = null, $lockVersion = null)
 * @method PuppeteerReplay|null findOneBy(array $criteria, array $orderBy = null)
 * @method PuppeteerReplay[]    findAll()
 * @method PuppeteerReplay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PuppeteerReplayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PuppeteerReplay::class);
    }

    //    /**
    //     * @return PuppeteerReplay[] Returns an array of PuppeteerReplay objects
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

    //    public function findOneBySomeField($value): ?PuppeteerReplay
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
