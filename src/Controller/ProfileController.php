<?php

namespace App\Controller;

use App\Form\UploadFormType;
use App\Form\FolderCreateFormType;
use App\Entity\Folder;
use App\Repository\FileRepository;
use App\Repository\FolderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, FileRepository $fileRepository, FolderRepository $folderRepository): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException('Access denied. Please log in.');
        }

        $user = $this->getUser();
        $username = $user->getUsername();

        $files = $fileRepository->findBy(['user' => $user]);
        $folders = $folderRepository->findBy(['user' => $user]);
        $selectedFolder = null;
        $folderId = $request->query->get('folderId');

        if ($folderId) {
            $selectedFolder = $folderRepository->findOneBy(['id' => $folderId, 'user' => $user]);
        }

        
        $formUpload = $this->createForm(UploadFormType::class, null, [
            'user' => $user,
            'action' => $this->generateUrl('app_upload', ['username' => $username]),
        ]);
        $formUpload->handleRequest($request);

        
        $folder = new Folder();
        $formFolder = $this->createForm(FolderCreateFormType::class, $folder, [
            'action' => $this->generateUrl('app_folder'),
        ]);
        $formFolder->handleRequest($request);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'files' => $files,
            'folders' => $folders,
            'selected_folder' => $selectedFolder,
            'form_upload' => $formUpload->createView(),
            'form_folder' => $formFolder->createView(),
            'controller_name' => 'ProfileController',
        ]);
    }
}
