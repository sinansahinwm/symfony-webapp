<?php

namespace App\Repository;

use App\Entity\TeamOwnership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeamOwnership>
 *
 * @method TeamOwnership|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamOwnership|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamOwnership[]    findAll()
 * @method TeamOwnership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamOwnershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamOwnership::class);
    }

    //    /**
    //     * @return TeamOwnership[] Returns an array of TeamOwnership objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TeamOwnership
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
