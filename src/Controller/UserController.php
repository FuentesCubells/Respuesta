<?php

namespace App\Controller;

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/{username}/{folderId}', name: 'app_user_folders', requirements: ['folderId' => '\d+'], defaults: ['folderId' => null])]
    public function folders(Request $request, EntityManagerInterface $entityManager, string $username, ?int $folderId): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if ($folderId === null) {
            $folders = $user->getFolders();

            return $this->render('user/folders.html.twig', [
                'user' => $user,
                'folders' => $folders,
                'selectedFolder' => null,
            ]);
        }
      
        $folder = $entityManager->getRepository(Folder::class)->findOneBy(['id' => $folderId, 'user' => $user]);

        if (!$folder) {
            throw $this->createNotFoundException('Folder not found');
        }

        $files = $folder->getFiles();

        return $this->render('user/folders.html.twig', [
            'user' => $user,
            'folders' => $user->getFolders(),
            'selectedFolder' => $folder,
            'files' => $files,
        ]);
    }
}
