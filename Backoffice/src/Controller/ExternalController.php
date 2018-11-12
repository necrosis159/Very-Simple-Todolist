<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OutsideAccess;
use App\Entity\TodoList;
use App\Entity\TodoTaskList;
use App\Entity\Project;
use App\Form\ProjectLogoType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ExternalController extends AbstractController
{
    /**
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
            'controller_name' => 'ProjectController', 'countDoneTask' => $countDoneTask, 'tasksDone' => $tasksDone, 'ArchivedTasklists' => $ArchivedTasklists, 'countArchivedTask' => $countArchivedTask , 'project' => $project, 'tasklists' => $tasklists, 'tasks' => $tasks, 'form' => $form->createView(),
        ]);
    }
}
