<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Entity\TodoTaskList;
use App\Entity\Project;
use App\Form\ProjectLogoType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
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
            ->where('u.idProject = ?1')
            ->setParameter(1, $id);
        $query = $qb->getQuery();
        $tasklists = $query->getScalarResult();

        //Get all tasks from this project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isDone, u.idList')
            ->from('\App\Entity\TodoTaskList', 'u')
            ->where('u.idProject = ?1')
            ->setParameter(1, $id);
        $query = $qb->getQuery();
        $tasks = $query->getScalarResult();

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
            'controller_name' => 'ProjectController', 'project' => $project, 'tasklists' => $tasklists, 'tasks' => $tasks, 'form' => $form->createView(),
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
