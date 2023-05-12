<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Folder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UploadController extends AbstractController
{

    #[Route('/{username}/upload', name: 'app_upload')]
    public function upload(Request $request, EntityManagerInterface $entityManager, string $username): Response
    {
        // Get the user that uploaded the files
        $user = $this->getUser();

        // Check if the authenticated user is the same as the one specified in the URL
        if ($user->getUsername() !== $username) {
            throw $this->createAccessDeniedException();
        }

        // Get the selected folder
        $formData = $request->request->all();
        $folderId = $formData['upload_form']['directory'];

        $folder = $entityManager->getRepository(Folder::class)->find($folderId);
        if (!$folder) {
            throw $this->createNotFoundException('Folder not found');
        }

        // Process the uploaded files
        $uploadedFiles = $request->files->get('upload_form')['files'];

        if (!$uploadedFiles) {
            throw new \Exception('No files uploaded');
        }

        $filePaths = array();
        foreach ($uploadedFiles as $uploadedFile) {
            $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME). '.' . $uploadedFile->guessExtension();
            $uploadedFile->move($folder->getPath(), $filename);
            $filePath = $folder->getPath() . '/' . $filename;
            $filePaths[] = $filePath;
        }

        $fileEntities = array();
        foreach ($filePaths as $filePath) {
            $file = new File();
            $file->setName(basename($filePath));
            $file->setPath($filePath);
            $file->setCreatedAt(new \DateTime());
            $file->setUser($user);
            $file->setFolder($folder);
            $entityManager->persist($file);
            $fileEntities[] = $file;
        }

        $entityManager->flush();

        // Redirect back to the profile page
        return $this->redirectToRoute('app_profile');
    }

}
