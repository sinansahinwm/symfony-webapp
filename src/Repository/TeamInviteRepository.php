<?php

namespace App\Repository;

use App\Entity\TeamInvite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeamInvite>
 *
 * @method TeamInvite|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamInvite|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamInvite[]    findAll()
 * @method TeamInvite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamInviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamInvite::class);
    }

//    /**
//     * @return TeamInvite[] Returns an array of TeamInvite objects
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

//    public function findOneBySomeField($value): ?TeamInvite
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
