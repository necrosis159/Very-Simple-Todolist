<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $users = $this->getDoctrine()
            ->getRepository(\App\Entity\User::class)
            ->findAll();

        if (!$users) {
            throw $this->createNotFoundException(
                'No product found for all'
            );
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $salut = '';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $users = $this->getDoctrine()
                ->getRepository(\App\Entity\User::class)
                ->findAll();
            return $this->render('user/index.html.twig', ['users' => $users, 'form' => $form->createView()]);
        }

        return $this->render('user/index.html.twig', ['users' => $users, 'form' => $form->createView()]);
    }
    /**
     *
     * Matches /user/update/*
     *
     * @Route("/user/update/{id}", name="usersUpdate")
     */
    public function update($id, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $product = $this->getDoctrine()
            ->getRepository(\App\Entity\User::class)
            ->find($id);

        $users = $this->getDoctrine()
            ->getRepository(\App\Entity\User::class)
            ->findById($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for all'
            );
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $product->setUsername($dataForm->getUsername());
            $product->setEmail($dataForm->getEmail());
            $product->setPassword($dataForm->getPassword());
            $product->setFirstname($dataForm->getFirstname());
            $product->setLastname($dataForm->getLastname());
            $product->setBusinessname($dataForm->getBusinessname());
            $product->setJob($dataForm->getJob());
            $product->setIsactive($dataForm->getIsactive());
            $product->setRules($dataForm->getRules());
            $entityManager->flush();

            return $this->redirectToRoute('users');
        }
        return $this->render('user/indexUpdate.html.twig', ['users' => $users, 'form' => $form->createView()]);
    }

    /**
     *
     * Matches /user/delete/*
     *
     * @Route("/user/delete/{id}", name="usersDelete")
     */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(User::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $entityManager->remove($product);
        $entityManager->flush();
    }
}
