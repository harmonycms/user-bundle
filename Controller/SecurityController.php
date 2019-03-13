<?php

namespace Harmony\UserBundle\Controller;

use Harmony\UserBundle\Exception\PasswordResetRequiredException;
use Harmony\UserBundle\Form\Type\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SecurityController
 *
 * @package Harmony\UserBundle\Controller
 */
class SecurityController extends AbstractController
{

    /** @var AuthenticationUtils $authenticationUtils */
    protected $authenticationUtils;

    /** @var SessionInterface $session */
    protected $session;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /**
     * SecurityController constructor.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param SessionInterface    $session
     * @param TranslatorInterface $translator
     */
    public function __construct(AuthenticationUtils $authenticationUtils, SessionInterface $session,
                                TranslatorInterface $translator)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->session             = $session;
        $this->translator          = $translator;
    }

    /**
     * @Route("/login", name="harmony_user_login", methods={"GET", "POST"})
     */
    public function login()
    {
        // get the login error if there is one
        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        if (null !== $lastError) {
            if ($lastError instanceof PasswordResetRequiredException) {
                // password reset required: we forward the user to the reset password form
                return $this->forward('Harmony\UserBundle\Controller\PasswordController::resetAction',
                    ['token' => $lastError->getResetToken()]);
            }

            $this->session->getFlashBag()
                ->add('error',
                    $this->translator->trans($lastError->getMessageKey(), $lastError->getMessageData(), 'UserBundle'));
        }

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);
        $form->get('_username')->setData($lastUsername);

        return $this->render('@HarmonyUser/Security/login.html.twig', [
            'form'          => $form->createView(),
            'last_username' => $lastUsername,
            'last_error'    => $lastError,
        ]);
    }

    /**
     * @Route("/logout", name="harmony_user_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
