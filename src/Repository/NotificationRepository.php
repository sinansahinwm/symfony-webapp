<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function getLatest(UserInterface $user, int $latestNotificationsCount = 5, bool $justUnread = TRUE)
    {
        $queryBuilder = $this->createQueryBuilder("qb");
        $queryBuilder->where('qb.to_user = :param1')->setParameter('param1', $user);
        if ($justUnread === TRUE) {
            $queryBuilder->andWhere('qb.is_read = :param2')->setParameter('param2', FALSE);
        }
        $queryBuilder->orderBy('qb.created_at', 'DESC');
        return $queryBuilder->setMaxResults($latestNotificationsCount)->getQuery()->getResult();
    }

}
