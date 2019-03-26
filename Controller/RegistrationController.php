<?php

namespace Harmony\Bundle\UserBundle\Controller;

use Harmony\Bundle\UserBundle\Form\Type\RegistrationFormType;
use Harmony\Bundle\UserBundle\Manager\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 *
 * @package Harmony\Bundle\UserBundle\Controller
 */
class RegistrationController extends AbstractController
{

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /** @var UserManagerInterface $manager */
    protected $manager;

    /**
     * RegistrationController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserManagerInterface         $manager
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserManagerInterface $manager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager         = $manager;
    }

    /**
     * @Route("/register", name="harmony_user_register")
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request): Response
    {
        $user = $this->manager->getInstance();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirect('/');
        }

        return $this->render('@HarmonyUser/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}