<?php

namespace App\Controller;

use App\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;

class FolderController extends AbstractController
{
    #[Route('/folder', name: 'app_folder')]
    public function folder(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $formData = $request->request->all();
        $folderName = $formData['folder_create_form']['folder_name'];

        if (!$folderName) {
            throw new \Exception('No folder name provided');
        }

        // Get the path to the public directory
        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/';

        // Get the path to the uploads directory
        $uploadsDirectory = $publicDirectory . 'uploads/';

        // Get the path to the user's directory
        $usernameDirectory = $uploadsDirectory . $user->getUsername() . '/';

        // Get the path to the folder
        $folderPath = $usernameDirectory . $folderName;

        // Create the folder
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create a new folder object and set its properties
        $folder = new Folder();
        $folder->setFolderName($folderName);
        $folder->setPath(str_replace($publicDirectory, '', $folderPath));
        $folder->setUser($user);

        // Get the entity manager and save the folder object to the database
        $entityManager->persist($folder);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/folder/delete/{id}', name: 'app_folder_delete', methods: ['POST'])]
    public function delete(Folder $folder, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        // Get the path to the public directory
        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/';

        // Get the full path to the folder
        $folderPath = $publicDirectory . $folder->getPath();

        // Delete the folder and its contents
        $filesystem->remove($folderPath);

        // Remove the folder from the database
        $entityManager->remove($folder);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/folder/rename/{id}', name: 'app_folder_rename', methods: ['POST'])]
    public function rename(Folder $folder, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newFolderName = $request->request->get('new_folder_name');

        if (!$newFolderName) {
            throw new \Exception('No new folder name provided');
        }

        // Get the path to the public directory
        $publicDirectory = $this->getParameter('kernel.project_dir') . '/public/';

        // Get the full path to the folder
        $folderPath = $publicDirectory . $folder->getPath();

        // Get the path to the parent directory
        $parentDirectory = dirname($folderPath);

        // Get the new path for the renamed folder
        $newFolderPath = $parentDirectory . '/' . $newFolderName;

        // Rename the folder
        rename($folderPath, $newFolderPath);

        // Update the folder's properties and save to the database
        $folder->setFolderName($newFolderName);
        $folder->setPath(str_replace($publicDirectory, '', $newFolderPath));
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
