<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Handle CV upload
            $cvFile = $form->get('cv')->getData();

            if ($cvFile) {
                // Directory to store the CVs
                $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/cv';

                // Ensure the directory exists
                $filesystem = new Filesystem();
                if (!$filesystem->exists($uploadsDirectory)) {
                    $filesystem->mkdir($uploadsDirectory);
                }

                // Generate a unique filename for the uploaded file
                $newFilename = uniqid() . '.' . $cvFile->guessExtension();

                try {
                    // Move the uploaded CV to the directory
                    $cvFile->move($uploadsDirectory, $newFilename);

                    // Update the User entity with the CV filename
                    $user->setCv($newFilename);
                } catch (IOExceptionInterface $exception) {
                    $this->addFlash('error', 'An error occurred while uploading the CV.');
                    return $this->redirectToRoute('app_register');
                }
            }

            // Persist the user entity
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
