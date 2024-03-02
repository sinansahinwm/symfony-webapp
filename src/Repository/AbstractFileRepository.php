<?php

namespace App\Repository;

use App\Entity\AbstractFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbstractFile>
 *
 * @method AbstractFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractFile[]    findAll()
 * @method AbstractFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractFile::class);
    }

//    /**
//     * @return AbstractFile[] Returns an array of AbstractFile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AbstractFile
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
