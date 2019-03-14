<?php

namespace Harmony\UserBundle\Controller;

use Harmony\UserBundle\Form\Type\RegistrationFormType;
use Harmony\UserBundle\Model\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 *
 * @package Harmony\UserBundle\Controller
 */
class RegistrationController extends AbstractController
{

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /** @var UserManager $manager */
    protected $manager;

    /**
     * RegistrationController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserManager                  $manager
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserManager $manager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->manager         = $manager;
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request): Response
    {
        $user = $this->manager->createUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('@HarmonyUser/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}