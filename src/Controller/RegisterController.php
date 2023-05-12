<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;


class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted()) {
            // Check for existing username and email
            $existingUser = $this->checkExistingUser($user, $entityManager);
            $existingEmail = $this->checkExistingEmail($user, $entityManager);
    
            if ($existingUser) {
                $form->get('username')->addError(new FormError('Username already exists.'));
            }
    
            if ($existingEmail) {
                $form->get('email')->addError(new FormError('Email already exists.'));
            }
    
            if ($form->isValid() && !$existingUser && !$existingEmail) {
                // Encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
    
                $entityManager->persist($user);
                $entityManager->flush();
                // Do anything else you need here, like send an email
    
                try {
                    $userAuthenticator->authenticateUser(
                        $user,
                        $formLoginAuthenticator,
                        $request
                    );
    
                    return $this->redirectToRoute('app_profile');
                } catch (AuthenticationException $exception) {
                    // Handle authentication exception, if needed
                    $this->addFlash('error', $exception->getMessage());
                }
            }
        }
    
        return $this->render('register/index.html.twig', [
            'registrationForm' => $form->createView(),
            'form' => $form,
        ]);

    }

    private function checkExistingUser(User $user, EntityManagerInterface $entityManager): bool
    {
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);

        return ($existingUser !== null);
    }

    private function checkExistingEmail(User $user, EntityManagerInterface $entityManager): bool
    {
        $existingEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        return ($existingEmail !== null);
    }

}
