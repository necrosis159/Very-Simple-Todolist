<?php

namespace App\Controller;

use App\Entity\OutsideAccess;
use App\Entity\Project;
use App\Entity\TodoList;
use App\Entity\TodoTaskList;
use App\Entity\Log;

use App\Form\ProjectLogoType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExternalController extends AbstractController
{
    /**
     *
     * Get the list of all external access created
     *
     * Matches /listExternal/*
     *
     * @Route("/listExternal", name="listExternal")
     */
    public function listExternal()
    {
        $OutsideAccess = $this->getDoctrine()
            ->getRepository(\App\Entity\OutsideAccess::class)
            ->findAll();

        if (!$OutsideAccess) {
            $OutsideAccess = "empty";
        }

        return $this->render('external/index.html.twig', [
            'controller_name' => 'ProjectController', 'OutsideAccess' => $OutsideAccess,
        ]);
    }

    /**
     *
     * Route for deleting an external access via Symfony. Used by Jquery only.
     *
     * Matches /listExternal/delete/*
     *
     * @Route("/listExternal/delete/{id}", name="listExternalDelete")
     */
    public function listExternalDelete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $OutsideAccess = $entityManager->getRepository(OutsideAccess::class)->find($id);
        $entityManager->remove($OutsideAccess);
        $entityManager->flush();
    }

    /**
     *
     * Route for updating who can edit on external access via Symfony. Used by Jquery only.
     *
     * Matches /listExternal/updateCanEdit/*
     *
     * @Route("/listExternal/updateCanEdit/{id}", name="updateCanEdit")
     */
    public function updateCanEdit($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $OutsideAccess = $entityManager->getRepository(OutsideAccess::class)->find($id);
        $canEdit = $OutsideAccess->getCanEdit();
        $OutsideAccess->setCanEdit(!$canEdit);
        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they see the project.
     *
     * Matches /external/*
     *
     * @Route("/external/{identifier}", name="indexExternal")
     */
    public function indexExternal($identifier, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        $canEdit = $autorisation[0]->getCanEdit();
        $idProject = $autorisation[0]->getIdProject();
        $name = $autorisation[0]->getName();

        $project = $this->getDoctrine()
            ->getRepository(\App\Entity\Project::class)
            ->find($idProject);

        //Doctrine Manager
        $dm = $this->getDoctrine()->getManager();

        //Get all task list from the project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isArchived')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $idProject, '2' => 0));
        $query = $qb->getQuery();
        $tasklists = $query->getScalarResult();

        //Get all task list archived from the project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isArchived')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $idProject, '2' => 1));
        $query = $qb->getQuery();
        $ArchivedTasklists = $query->getScalarResult();

        //Get all undone tasks from this project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isDone, u.idList')
            ->from('\App\Entity\TodoTaskList', 'u')
            ->where('u.idProject = ?1 AND u.isDone = ?2')
            ->setParameters(array('1' => $idProject, '2' => 0));
        $query = $qb->getQuery();
        $tasks = $query->getScalarResult();

        //Get all done tasks from this project
        $qb = $dm->createQueryBuilder();
        $qb->select('u.id, u.name, u.isDone, u.idList')
            ->from('\App\Entity\TodoTaskList', 'u')
            ->where('u.idProject = ?1 AND u.isDone = ?2')
            ->setParameters(array('1' => $idProject, '2' => 1));
        $query = $qb->getQuery();
        $tasksDone = $query->getScalarResult();

        //Count the number of Archived TaskList
        $qb = $dm->createQueryBuilder();
        $qb->select('COUNT(u.id)')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $idProject, '2' => 1));
        $query = $qb->getQuery();
        $countArchivedTask = $query->getSingleScalarResult();

        //Count the number of Task Done
        $qb = $dm->createQueryBuilder();
        $qb->select('COUNT(u.id)')
            ->from('\App\Entity\TodoList', 'u')
            ->where('u.idProject = ?1 AND u.isArchived = ?2')
            ->setParameters(array('1' => $idProject, '2' => 1));
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

            return $this->redirect('/external/' . $identifier);
        }

        return $this->render('external/indexShow.html.twig', [
            'identifier' => $identifier, 'canEdit' => $canEdit, 'controller_name' => 'ProjectController', 'countDoneTask' => $countDoneTask, 'tasksDone' => $tasksDone, 'ArchivedTasklists' => $ArchivedTasklists, 'countArchivedTask' => $countArchivedTask, 'project' => $project, 'tasklists' => $tasklists, 'tasks' => $tasks, 'form' => $form->createView(),
        ]);
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they archive a tasklist. Only used by Jquery.
     *
     * Matches /external/archiveTaskList/*
     *
     * @Route("/external/archiveTaskList/{identifier}/{id}", name="archiveTaskListExternal")
     */
    public function archiveTaskListExternal($identifier, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }
        
        $TodoList = $entityManager->getRepository(TodoList::class)->find($id);

        if($autorisation[0]->getIdProject() != $TodoList->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $TodoList->setIsArchived(1);
        $entityManager->persist($TodoList);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Archived the tasklist: ' . $id);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they say that a task isn't done . Only used by Jquery.
     *
     * Matches /external/isNotDone/*
     *
     * @Route("/external/isNotDone/{identifier}/{projectId}", name="isNotDoneExternal")
     */
    public function isNotDoneExternal($identifier, $projectId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }
        $TodoTaskList = $entityManager->getRepository(TodoTaskList::class)->find($id);
        
        if($autorisation[0]->getIdProject() != $TodoTaskList->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $TodoTaskList->setIsDone(0);
        $entityManager->persist($TodoTaskList);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Task not done: ' . $id);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they say that a task is done. Only used by Jquery.
     *
     * Matches /external/isDone/*
     *
     * @Route("/external/isDone/{identifier}/{id}", name="isDoneExternal")
     */
    public function isDoneExternal($identifier, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }
        $TodoTaskList = $entityManager->getRepository(TodoTaskList::class)->find($id);

        if($autorisation[0]->getIdProject() != $TodoTaskList->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $TodoTaskList->setIsDone(1);
        $entityManager->persist($TodoTaskList);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('task done: ' . $id);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they restore a tasklist. Only used by Jquery.
     *
     * Matches /external/restoreTaskList/*
     *
     * @Route("/external/restoreTaskList/{identifier}/{id}", name="restoreTaskListExternal")
     */
    public function restoreTaskListExternal($identifier, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }
        
        $TodoList = $entityManager->getRepository(TodoList::class)->find($id);

        if($autorisation[0]->getIdProject() != $TodoList->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $TodoList->setIsArchived(0);
        $entityManager->persist($TodoList);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Restore tasklist: ' . $id);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they update a task name. Only used by Jquery.

     * @Route("/external/updateTask/{identifier}/{id}/{name}", name="updateTaskExternal")
     */
    public function updateTaskExternal($identifier, $id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }        
        
        $TodoTaskList = $entityManager->getRepository(TodoTaskList::class)->find($id);

        if (!$TodoTaskList) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        
        if($autorisation[0]->getIdProject() != $TodoTaskList->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $TodoTaskList->setName($name);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Updated task name: ' . $id . ' - '.$name);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they delete a task list. Only used by Jquery.
     *
     * Matches /external/delTaskList/*
     *
     * @Route("/external/delTaskList/{identifier}/{id}", name="delTaskListExternal")
     */
    public function delTaskListExternal($identifier, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }        
        $task = $entityManager->getRepository(TodoList::class)->find($id);

        if($autorisation[0]->getIdProject() != $task->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $entityManager->remove($task);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Removed task list: ' . $id);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they delete a task. Only used by Jquery.
     *
     * Matches /external/delTask/*
     *
     * @Route("/external/delTask/{identifier}/{id}", name="delTaskExternal")
     */
    public function delTaskExternal($identifier, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }        
        $tasklist = $entityManager->getRepository(TodoTaskList::class)->find($id);

        if($autorisation[0]->getIdProject() != $tasklist->getIdProject())
        {
            throw $this->createNotFoundException(
            'Not authorized'
            );
        }

        $entityManager->remove($tasklist);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Remove task: ' . $id);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they add a tasklist. Only used by Jquery.
     *
     * Matches /external/addTaskList/*
     *
     * @Route("/external/addTaskList/{identifier}/{projectId}/{name}", name="addTaskListExternal")
     */
    public function addTaskListExternal($identifier, $projectId, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }        
        $tasklist = new TodoList();
        $tasklist->setName($name);
        $tasklist->setIdProject($autorisation[0]->getIdProject());
        $tasklist->setIsArchived(0);
        $entityManager->persist($tasklist);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Add task list at the project: ' . $projectId . '. With the name: '. $name);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they add a task. Only used by Jquery.
     *
     * Matches /external/addTask/*
     *
     * @Route("/external/addTask/{identifier}/{projectId}/{taskListId}/{val}", name="addTaskExternal")
     */
    public function addTaskExternal($identifier, $projectId, $taskListId, $val)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }        
        $task = new TodoTaskList();
        $task->setName($val);
        $task->setIdProject($autorisation[0]->getIdProject());
        $task->setIdList($taskListId);
        $task->setIsDone(0);
        $entityManager->persist($task);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Add a task. On the project ' . $projectId . ' on the tasklist '. $taskListId . ' with the name '.$val);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }

    /**
     *
     * Route for external people accessing a project if they have an identifier. Here they update a task. Only used by Jquery.
     *
     * @Route("/external/update/{identifier}/{id}/{name}", name="projectupdateExternal")
     */
    public function projectupdateExternal($identifier, $id, $name)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $autorisation = $entityManager->getRepository(\App\Entity\OutsideAccess::class)->findBy(array('identifier' => $identifier));

        if (!$autorisation) {
            throw $this->createNotFoundException(
                'Invalid code'
            );
        }
        
        $project = $entityManager->getRepository(Project::class)->find($autorisation[0]->getIdProject());

        if (!$project) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }
        
        $project->setName($name);

        $log = new Log();
        $log->setUser($identifier);
        $log->setAction('Update the project name on the project ' . $id . ' with the name '. $name);
        $log->setIdProject($autorisation[0]->getIdProject());
        $entityManager->persist($log);

        $entityManager->flush();
    }
}
