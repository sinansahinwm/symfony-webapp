<?php namespace App\Controller\Admin\Select;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamMatesSelectController extends SelectController implements SelectControllerInterface
{

    const CALLBACK_PATH = self::CALLBACK_PATH_PREFIX . 'team_mates';

    public function __construct(private UserRepository $userRepository, private Security $security)
    {
    }

    #[Route(self::CALLBACK_PATH)]
    public function callback(Request $request): Response
    {
        return $this->getCallbackResponse($this->queryBuilder($this->userRepository), function (User $userObject) {
            return $userObject->getEmail();
        });
    }

    public function queryBuilder(EntityRepository $entityRepository): QueryBuilder
    {
        return $entityRepository;
    }
}