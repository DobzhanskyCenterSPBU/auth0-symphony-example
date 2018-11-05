<?php

namespace App\Controller;

use DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Project;

use App\Form\UserFormType;
use App\Form\ProjectEntryFormType;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /** @var EntityManagerInterface */
    private $entityManager;
    
    /** @var \Doctrine\Common\Persistence\UserRepository */
    private $userRepository;
    
    /** @var \Doctrine\Common\Persistence\ProjectRepository */
    private $projectRepository;
    
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository('App:User');
        $this->projectRepository = $entityManager->getRepository('App:Project');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    
    /**
     * @Route("/user/create", name="user_create")
     */
    public function createUserAction(Request $request)
    {
        if ($this->userRepository->findOneByUserName($this->getUser()->getUsername())) {
            // Redirect to dashboard.
            $this->addFlash('error', 'Unable to create user, user already exists!');
    
            return $this->redirectToRoute('homepage');
        }
    
        $user = new User();
        $user->setUserName($this->getUser()->getUsername());
    
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush($user);
    
            $request->getSession()->set('user_is_registered', true);
            $this->addFlash('success', 'Congratulations! You are now an user.');
    
            return $this->redirectToRoute('homepage');
        }
    
        return $this->render('admin/create_user.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/create-project-entry", name="admin_create_project_entry")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProjectEntryAction(Request $request)
    {
        $project = new Project();
    
        $owner = $this->userRepository->findOneByUserName($this->getUser()->getUsername());
        $project->setOwner($owner);
        $project->setCreatedAt(new DateTime('NOW'));
    
        $form = $this->createForm(ProjectEntryFormType::class, $project);
        $form->handleRequest($request);
    
        // Check is valid
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush($project);
    
            $this->addFlash('success', 'Congratulations! Your project-entry is created');
    
            return $this->redirectToRoute('admin_project_entries');
        }
    
        return $this->render('admin/project_entry_form.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/", name="admin_index")
     * @Route("/project-entries", name="admin_project_entries")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectEntriesAction()
    {
        $user = $this->userRepository->findOneByUserName($this->getUser()->getUsername());
    
        $projects = [];
    
        if ($user) {
            $projects = $this->projectRepository->findByOwner($user);
        }
    
        return $this->render('admin/project_entries.html.twig', [
            'projects' => $projects
        ]);
    }
    
    /**
     * @Route("/delete-project/{entryId}", name="admin_delete_project_entry")
     *
     * @param $entryId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProjectEntryAction($entryId)
    {
        $project = $this->projectRepository->findOneById($entryId);
        $owner = $this->userRepository->findOneByUserName($this->getUser()->getUsername());
    
        if (!$project || $owner !== $project->getOwner()) {
            $this->addFlash('error', 'Unable to remove entry!');
    
            return $this->redirectToRoute('admin_project_entries');
        }
    
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    
        $this->addFlash('success', 'Entry was deleted!');
    
        return $this->redirectToRoute('admin_project_entries');
    }
}
