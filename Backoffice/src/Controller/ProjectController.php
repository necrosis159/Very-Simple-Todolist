<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TodoList;
use App\Entity\TodoTaskList;

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
            'controller_name' => 'ProjectController', 'projects' => $projects
        ]);
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
    public function projectShow($id)
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

        return $this->render('project/indexShow.html.twig', [
            'controller_name' => 'ProjectController', 'project' => $project, 'tasklists' => $tasklists, 'tasks' => $tasks
        ]);
    }
}
