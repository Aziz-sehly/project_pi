<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/client", name="client")
     */
    public function client(): Response
    {

        return $this->render('client/index.html.twig');
    }
    #[Route('/client/{id}/edit', name: 'client_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Ensure you don't modify roles by accident
        $originalRoles = $user->getRoles();

        // Create a form for editing user data (excluding the roles)
        $form = $this->createForm(ProfileForm::class, $user);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Prevent role modification (roles remain the same)
            $user->setRoles($originalRoles);

            $entityManager->flush();

            // Redirect to the user's profile or another page
            return $this->redirectToRoute('client', ['id' => $user->getId()]);
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
