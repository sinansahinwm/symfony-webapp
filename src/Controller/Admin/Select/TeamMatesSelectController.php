<?php namespace App\Controller\Admin\Select;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class TeamMatesSelectController extends SelectController implements SelectControllerInterface
{

    const CALLBACK_PATH = self::CALLBACK_PATH_PREFIX . 'team_mates';

    public function __construct(private UserRepository $userRepository, private Security $security)
    {
    }

    #[Route(self::CALLBACK_PATH)]
    public function callback(Request $request): Response
    {
        return $this->getCallbackResponse($this->queryBuilder($this->userRepository, $request->get("q")), function (User $userObject) {
            return $userObject->getEmail();
        });
    }

    public function queryBuilder(EntityRepository $entityRepository, ?string $searchKeyword = NULL): QueryBuilder
    {
        // EntityRepository -> UserRepository
        $loggedUser = $this->security->getUser();
        $usersTeam = $loggedUser->getTeam();
        $queryBuilder = $entityRepository->createQueryBuilder("qb");

        if ($usersTeam instanceof Team && $entityRepository instanceof UserRepository) {
            $queryBuilder->innerJoin('qb.team', 't');
            $queryBuilder->where('qb.team = :param1')->setParameter('param1', $loggedUser->getTeam());
            if ($searchKeyword !== NULL) {
                $queryBuilder->andWhere($queryBuilder->expr()->like('qb.email', ':param2'))->setParameter('param2', "%$searchKeyword%");
                $queryBuilder->orWhere($queryBuilder->expr()->like('qb.display_name', ':param3'))->setParameter('param3', "%$searchKeyword%");
            }
        } else {
            $queryBuilder->where("false");
        }

        return $queryBuilder;
    }
}