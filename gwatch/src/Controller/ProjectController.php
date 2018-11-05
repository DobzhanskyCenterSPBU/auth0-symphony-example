<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /** @var integer */
    const PROJECTS_LIMIT = 10;
    
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
        $this->projectRepository = $entityManager->getRepository('App:Project');
        $this->userRepository = $entityManager->getRepository('App:User');
    }
    
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }
    
    /**
     * @Route("/projects", name="project-entries")
     */
    public function entriesAction(Request $request)
    {
        $page = 1;
        
        if ($request->get('page')) {
            $page = $request->get('page');
        }

        return $this->render('project/entries.html.twig', [
            'projects' => $this->projectRepository->getAllProjects($page, self::PROJECTS_LIMIT),
            'totalProjects' => $this->projectRepository->getProjectsCount(),
            'page' => $page,
            'entryLimit' => self::PROJECTS_LIMIT
        ]);
    }
    
    /**
     * @Route("/project-entry/{id}", name="project-entry")
     */
    public function projectEntryAction($id)
    {
        $project = $this->projectRepository->findOneById($id);
    
        if (!$project) {
            $this->addFlash('error', 'Unable to find entry!');
    
            return $this->redirectToRoute('project-entries');
        }
    
        return $this->render('project/project_entry.html.twig', array(
            'project' => $project
        ));
    }
    
    /**
     * @Route("/user/{username}", name="user")
     */
    public function userAction($username)
    {
        $user = $this->userRepository->findOneByUserName($username);
    
        if (!$user) {
            $this->addFlash('error', 'Unable to find user!');
            return $this->redirectToRoute('project-entries');
        }
    
        return $this->render('project/user.html.twig', [
            'user' => $user
        ]);
    }
}
