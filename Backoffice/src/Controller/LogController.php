<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Log;

class LogController extends AbstractController
{
    /**
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
