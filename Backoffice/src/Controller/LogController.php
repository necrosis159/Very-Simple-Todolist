<?php

namespace App\Controller;

use App\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     *
     * Return all the logs in the database
     *
     * @Route("/log", name="log")
     */
    public function index()
    {
        $logs = $this->getDoctrine()
            ->getRepository(\App\Entity\Log::class)
            ->findAll();

        if (!$logs) {
            $logs = "empty";
        }

        return $this->render('log/index.html.twig', [
            'controller_name' => 'LogController', 'logs' => $logs,
        ]);
    }
}
