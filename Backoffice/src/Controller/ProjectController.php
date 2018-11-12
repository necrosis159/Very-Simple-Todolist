<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\TodoTaskList;
use App\Entity\Project;
use App\Entity\OutsideAccess;
use App\Form\ProjectLogoType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
     /**
     * 
     *  Matches /project/outsideAccess/*
     * 
     * @Route("/project/outsideAccess/{projectId}/{canEdit}/{name}", name="outsideAccess")
     */
    public function outsideAccess($projectId, $canEdit,$name){
        $entityManager = $this->getDoctrine()->getManager();
        $OutsideAccess = new OutsideAccess();
        $OutsideAccess->setIdentifier(uniqid());
        $OutsideAccess->setName($name);
        $OutsideAccess->setCanEdit($canEdit);
        $OutsideAccess->setIdProject($projectId);
        $entityManager->persist($OutsideAccess);
        $entityManager->flush();
    }

    /**
     * @Route("/project", name="project")
     */
    public function index()
    {
        $projects = $this->getDoctrine()
            ->getRepository(\App\Entity\Project::class)
            ->findAll();

        if (!$projects) {
            $projects = "empty";
        }
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController', 'projects' => $projects,
        ]);
    }

    /**
     *
     * Matches /project/archiveTaskList/*
     *
     * @Route("/project/archiveTaskList/{id}", name="archiveTaskList")
     */
    public function archiveTaskList($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoList::class)->find($id);
        $tasklist->setIsArchived(1);
        $entityManager->persist($tasklist);
        $entityManager->flush();
    }

    /**
     *
     * Matches /project/restoreTaskList/*
     *
     * @Route("/project/restoreTaskList/{id}", name="restoreTaskList")
     */
    public function restoreTaskList($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoList::class)->find($id);
        $tasklist->setIsArchived(0);
        $entityManager->persist($tasklist);
        $entityManager->flush();
    }

    /**
     * @Route("/project/add/{name}", name="projectadd")
     */
    public function projectadd($name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = new Project();
        $project->setName($name);
        $entityManager->persist($project);
        $entityManager->flush();
    }

     /**
     * @Route("/project/update/{id}/{name}", name="projectupdate")
     */
    public function projectupdate($id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $project->setName($name);
        $entityManager->flush();
    }

     /**
     * @Route("/project/updateTask/{id}/{name}", name="updateTask")
     */
    public function updateTask($id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository(TodoTaskList::class)->find($id);
        
        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $project->setName($name);
        $entityManager->flush();
    }

     /**
     * @Route("/project/updateTaskList/{id}/{name}", name="updateTaskList")
     */
    public function updateTaskList($id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository(TodoTaskList::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $project->setName($name);
        $entityManager->flush();
    }

    /**
     *
     * Matches /project/delTaskList/*
     *
     * @Route("/project/delTaskList/{id}", name="delTaskList")
     */
    public function delTaskList($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoList::class)->find($id);
        $entityManager->remove($tasklist);
        $entityManager->flush();
    }

    /**
     *
     * Matches /project/addTaskList/*
     *
     * @Route("/project/addTaskList/{projectId}/{name}", name="addTaskList")
     */
    public function addTaskList($projectId, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = new TodoList();
        $tasklist->setName($name);
        $tasklist->setIdProject($projectId);
        $tasklist->setIsArchived(0);
        $entityManager->persist($tasklist);
        $entityManager->flush();
    }

    /**
     *
     * Matches /project/delTask/*
     *
     * @Route("/project/delTask/{id}", name="delTask")
     */
    public function delTask($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoTaskList::class)->find($id);
        $entityManager->remove($tasklist);
        $entityManager->flush();
    }
    /**
     *
     * Matches /project/addTask/*
     *
     * @Route("/project/addTask/{projectId}/{taskListId}/{val}", name="addTask")
     */
    public function addTask($projectId, $taskListId, $val)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = new TodoTaskList();
        $task->setName($val);
        $task->setIdProject($projectId);
        $task->setIdList($taskListId);
        $task->setIsDone(0);
        $entityManager->persist($task);
        $entityManager->flush();
    }

     /**
     *
     * Matches /project/isDone/*
     *
     * @Route("/project/isDone/{projectId}", name="isDone")
     */
    public function isDone($projectId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoTaskList::class)->find($id);
        $task->setIsDone(1);
        $entityManager->persist($task);
        $entityManager->flush();
    }

     /**
     *
     * Matches /project/isNotDone/*
     *
     * @Route("/project/isNotDone/{projectId}", name="isNotDone")
     */
    public function isNotDone($projectId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoTaskList::class)->find($id);
        $task->setIsDone(0);
        $entityManager->persist($task);
        $entityManager->flush();
    }

    /**
     *
     * Matches /project/*
     *
     * @Route("/project/{id}", name="projectShow")
     */
    public function projectShow($id, Request $request)
    {
        $project = $this->getDoctrine()
            ->getRepository(\App\Entity\Project::class)
            ->find($id);

        //Doctrine Manager
        $dm = $this->getDoctrine()->getManager();

        //Get all task list from the project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isArchived')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $id, '2' => 0));
        $query = $qb->getQuery();
        $tasklists = $query->getScalarResult();

        //Get all task list archived from the project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isArchived')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $id, '2' => 1));
        $query = $qb->getQuery();
        $ArchivedTasklists = $query->getScalarResult();

        //Get all undone tasks from this project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isDone, u.idList')
            ->from('\App\Entity\TodoTaskList', 'u')
            ->where('u.idProject = ?1 AND u.isDone = ?2')
            ->setParameters(array('1' => $id, '2' => 0));
        $query = $qb->getQuery();
        $tasks = $query->getScalarResult();

        //Get all done tasks from this project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isDone, u.idList')
            ->from('\App\Entity\TodoTaskList', 'u')
            ->where('u.idProject = ?1 AND u.isDone = ?2')
            ->setParameters(array('1' => $id, '2' => 1));
        $query = $qb->getQuery();
        $tasksDone = $query->getScalarResult();

        //Count the number of Archived TaskList
        $qb = $dm->createQueryBuilder();
        $qb->select('COUNT(u.id)')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $id, '2' => 1));
        $query = $qb->getQuery();
        $countArchivedTask = $query->getSingleScalarResult();

        //Count the number of Task Done
        $qb = $dm->createQueryBuilder();
        $qb->select('COUNT(u.id)')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $id, '2' => 1));
        $query = $qb->getQuery();
        $countDoneTask = $query->getSingleScalarResult();

        $dm = $this->getDoctrine()->getManager();
        $ProjectLogo = new Project();
        $form = $this->createForm(ProjectLogoType::class, $ProjectLogo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $ProjectLogo->getLogo();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('logo_directory'),
                $fileName
            );
            $project->setLogo($fileName);
            //$product->setLogo($fileName);
            $dm->flush();

            return $this->redirect('/project/' . $id);
        }

        return $this->render('project/indexShow.html.twig', [
            'controller_name' => 'ProjectController', 'countDoneTask' => $countDoneTask, 'tasksDone' => $tasksDone, 'ArchivedTasklists' => $ArchivedTasklists, 'countArchivedTask' => $countArchivedTask , 'project' => $project, 'tasklists' => $tasklists, 'tasks' => $tasks, 'form' => $form->createView(),
        ]);
    }
        /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
