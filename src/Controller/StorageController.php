<?php

namespace App\Controller;

use App\Entity\AbstractFile;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/admin', name: 'app_admin_')]
class StorageController extends AbstractController
{
    #[Route('/storage/{fileName}/{download}/{filePassword}', name: 'app_storage')]
    public function index(AbstractFile $abstractFilePublic, Security $security, DownloadHandler $downloadHandler, TeamRepository $teamRepository, bool $download = TRUE, ?string $filePassword = NULL): StreamedResponse|BinaryFileResponse
    {

        // Check user logged in
        if (!$security->getUser()) {
            throw new AccessDeniedException();
        }

        // Check file password match
        if ($abstractFilePublic->getPassword() !== NULL) {
            if ($filePassword !== $abstractFilePublic->getPassword()) {
                throw new AccessDeniedException();
            }
        }

        // Check team collaborator or file owner
        $userCanAccess = $this->userCanAccess($security->getUser(), $abstractFilePublic, $teamRepository);
        if ($userCanAccess === FALSE) {
            throw new AccessDeniedException();
        }

        // Return response for download method
        if ($download === TRUE) {
            return $downloadHandler->downloadObject($abstractFilePublic, AbstractFile::FILE_PROPERTY);
        } else {
            return $downloadHandler->downloadObject($abstractFilePublic, AbstractFile::FILE_PROPERTY, NULL, NULL, FALSE);
        }

    }

    private function userCanAccess(UserInterface $user, AbstractFile $abstractFile, TeamRepository $teamRepository): bool
    {
        if ($user->getUserIdentifier() === $abstractFile->getUploadedBy()->getUserIdentifier()) {
            return TRUE;
        }
        $ownerTeams = $abstractFile->getUploadedBy()->getTeams();
        foreach ($ownerTeams as $ownerTeam) {
            $collaboratorExist = $teamRepository->collaboratorExist($ownerTeam, $user);
            if ($collaboratorExist instanceof Team) {
                return TRUE;
            }
        }
        return FALSE;
    }
}
