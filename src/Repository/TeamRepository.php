<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function collaboratorExist(Team $team, ?User $collaborator, ?string $userMail = NULL): null|Team
    {
        $queryBuilder = $this->createQueryBuilder("qb");

        $queryBuilder->where('qb.id = :param1')->setParameter('param1', $team->getId());
        $queryBuilder->innerJoin('qb.users', 'u');

        if ($collaborator instanceof UserInterface) {
            $queryBuilder->andWhere('u = :param2')->setParameter('param2', $collaborator);
        }
        if ($userMail !== NULL) {
            $queryBuilder->andWhere('u.email = :param3')->setParameter('param3', $userMail);
        }
        return $queryBuilder->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    public function getTeamMembers(Team $team)
    {
        $queryBuilder = $this->createQueryBuilder("qb");
        $queryBuilder->where('qb.id = :param1')->setParameter('param1', $team->getId());
        $queryBuilder->innerJoin('qb.users', 'users');
        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Team[] Returns an array of Team objects
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

//    public function findOneBySomeField($value): ?Team
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
