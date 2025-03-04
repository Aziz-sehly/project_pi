<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class AdminController extends AbstractController

{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }


   /**
 * @Route("/admin", name="admin")
 */
public function index(Request $request, UserRepository $userRepository): Response
{
    $query = $request->query->get('q');
    $users = $query ? $userRepository->searchUsers($query) : $userRepository->findAll();

    return $this->render('admin/index.html.twig', [
        'users' => $users,
    ]);
}

    /**
     * @Route("/admin/users/{id}/details", name="admin_user_show")
     */
    public function show(User $user): Response
    {
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }
    
    #[Route('/admin/user/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/{id}/delete', name: 'admin_user_delete')]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User deleted successfully!');

        return $this->redirectToRoute('admin');
    }

     /**
     * @Route("/admin/user/{id}/download-cv", name="admin_user_download_cv")
     */
    public function downloadCv($id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        
        if (!$user || !$user->getCv()) {
            throw $this->createNotFoundException('CV not found for this user.');
        }

        // Path to where the CVs are stored in the public directory
        $cvFile = $this->getParameter('kernel.project_dir') . '/public/cv/' . $user->getCv();
        
        //dd($cvFile);
        if (!file_exists($cvFile)) {
            throw new FileNotFoundException('CV file not found.');
        }

        return $this->file($cvFile);

        
    }
}
