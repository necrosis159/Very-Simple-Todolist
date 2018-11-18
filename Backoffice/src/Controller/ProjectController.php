<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\OutsideAccess;
use App\Entity\Project;
use App\Entity\TodoList;
use App\Entity\TodoTaskList;
use App\Form\ProjectLogoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @return string
     */
    private function getActiveUser()
    {
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        return $usr->getUsername();
    }
    /**
     *
     * Create a outsideaccess. Used only by Jquery.
     *
     *  Matches /project/outsideAccess/*
     *
     * @Route("/project/outsideAccess/{projectId}/{canEdit}/{name}", name="outsideAccess")
     */
    public function outsideAccess($projectId, $canEdit, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $OutsideAccess = new OutsideAccess();
        $OutsideAccess->setIdentifier(uniqid());
        $OutsideAccess->setName($name);
        $OutsideAccess->setCanEdit($canEdit);
        $OutsideAccess->setIdProject($projectId);
        $entityManager->persist($OutsideAccess);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Opened external access');
        $log->setIdProject($projectId);
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Return all the project
     *
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
     * Archive a task list. Used only by Jquery.
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

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Archived the tasklist: ' . $id);
        $log->setIdProject($tasklist->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Check if a list is done or not. If it is, archive it automatically. Used only by Jquery.
     *
     * Matches /project/isListDone/*
     *
     * @Route("/project/isListDone/{id}", name="isListDone")
     */
    public function isListDone($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.isDone, u.idList')
            ->from('\App\Entity\TodoTaskList', 'u')
            ->where('u.idList = ?1')
            ->setParameter(1, $id);
        $query = $qb->getQuery();
        $tasklists = $query->getScalarResult();
        $isEverythingDone = 0;
        $numberOfTaskDone = 0;
        $numberOfLoops = 0;
        foreach ($tasklists as $task) {
            $numberOfLoops++;
            if ($task['isDone'] == 1) {
                $numberOfTaskDone++;
            }
        }
        if ($numberOfLoops == $numberOfTaskDone) {
            $tasklist = $this->getDoctrine()
                ->getRepository(TodoList::class)
                ->find($id);
            $tasklist->setIsArchived(1);

            $log = new Log();
            $log->setUser($this->getActiveUser());
            $log->setAction('Automatically archived the tasklist: ' . $id);
            $log->setIdProject(null);
            $dm->persist($log);

            $dm->flush();
        }
    }

    /**
     *
     * Restore a tasklist. Used only by Jquery.
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

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Restored a tasklist: ' . $id);
        $log->setIdProject($tasklist->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Add a project.
     *
     * @Route("/project/add/{name}", name="projectadd")
     */
    public function projectadd($name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = new Project();
        $project->setName($name);
        $entityManager->persist($project);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Created a new projet: ' . $name);
        $log->setIdProject(null);
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Update a project
     * @Route("/project/update/{id}/{name}", name="projectupdate")
     */
    public function projectupdate($id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $project->setName($name);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Updated the project with a new name: ' . $name);
        $log->setIdProject($id);
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Update a task name. Used only by Jquery.
     *
     * @Route("/project/updateTask/{id}/{name}", name="updateTask")
     */
    public function updateTask($id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository(TodoTaskList::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $project->setName($name);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Updated the task: ' . $id . '. New Name: ' . $name);
        $log->setIdProject($project->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Update a task list name. Used only by Jquery.
     *
     * @Route("/project/updateTaskList/{id}/{name}", name="updateTaskList")
     */
    public function updateTaskList($id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository(TodoTaskList::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $project->setName($name);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Updated the task list: ' . $id . '. New Name: ' . $name);
        $log->setIdProject($project->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Delete a tasklist. Used only by Jquery.
     *
     * Matches /project/delTaskList/*
     *
     * @Route("/project/delTaskList/{id}", name="delTaskList")
     */
    public function delTaskList($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoList::class)->find($id);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Removed the task list ID: ' . $id);
        $log->setIdProject($tasklist->getIdProject());
        $entityManager->persist($log);

        $entityManager->remove($tasklist);
        $entityManager->flush();
    }

    /**
     *
     * Add a tasklist. Used only by Jquery.
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

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Added the task list: ' . $name);
        $log->setIdProject($projectId);
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Delete a tasklist. Used only by Jquery.
     *
     * Matches /project/delTask/*
     *
     * @Route("/project/delTask/{id}", name="delTask")
     */
    public function delTask($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasklist = $entityManager->getRepository(TodoTaskList::class)->find($id);
        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Deleted the task ID: ' . $id);
        $log->setIdProject($tasklist->getIdProject());
        $entityManager->persist($log);
        $entityManager->remove($tasklist);
        $entityManager->flush();
    }
    /**
     *
     * Add a task. Used only by Jquery.
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
        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Numéro de la liste: ' . $taskListId . ' . Added the task: ' . $val);
        $log->setIdProject($projectId);
        $entityManager->persist($log);
        $entityManager->flush();
    }

    /**
     *
     * Set a task in "done". Used only by Jquery.
     *
     * Matches /project/isDone/*
     *
     * @Route("/project/isDone/{id}", name="isDone")
     */
    public function isDone($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(TodoTaskList::class)->find($id);
        $task->setIsDone(1);
        $entityManager->persist($task);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('Done the task ID: ' . $id);
        $log->setIdProject($task->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Set a task in "notDone". Used only by Jquery.
     *
     * Matches /project/isNotDone/*
     *
     * @Route("/project/isNotDone/{id}", name="isNotDone")
     */
    public function isNotDone($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(TodoTaskList::class)->find($id);
        $task->setIsDone(0);
        $entityManager->persist($task);

        $log = new Log();
        $log->setUser($this->getActiveUser());
        $log->setAction('UnDone the task ID: ' . $id);
        $log->setIdProject($task->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Return details about a specific project.
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
            'controller_name' => 'ProjectController', 'countDoneTask' => $countDoneTask, 'tasksDone' => $tasksDone, 'ArchivedTasklists' => $ArchivedTasklists, 'countArchivedTask' => $countArchivedTask, 'project' => $project, 'tasklists' => $tasklists, 'tasks' => $tasks, 'form' => $form->createView(),
        ]);
    }

    /**
     *
     * Create an unique ID. This function is used when we save the project logo file.
     *
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
