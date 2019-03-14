<?php

namespace Harmony\UserBundle\Controller;

use Harmony\UserBundle\Event\PasswordRequestEvent;
use Harmony\UserBundle\Event\PasswordResetEvent;
use Harmony\UserBundle\Form\Type\PasswordRequestType;
use Harmony\UserBundle\Form\Type\PasswordResetType;
use Harmony\UserBundle\Mailer\TwigSwiftMailer;
use Harmony\UserBundle\Model\User;
use Harmony\UserBundle\Security\TokenGenerator;
use Harmony\UserBundle\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PasswordController
 *
 * @package Harmony\UserBundle\Controller
 */
class PasswordController extends AbstractController
{

    /** @var SessionInterface|Session $session */
    protected $session;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var RouterInterface $router */
    protected $router;

    /** @var TraceableEventDispatcher $eventDispatcher */
    protected $eventDispatcher;

    /** @var UserProviderInterface|UserProvider $userProvider */
    protected $userProvider;

    /** @var int $resetTokenTtl */
    protected $resetTokenTtl;

    /**
     * PasswordController constructor.
     *
     * @param SessionInterface         $session
     * @param TranslatorInterface      $translator
     * @param RouterInterface          $router
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserProviderInterface    $userProvider
     * @param null|int                 $resetTokenTtl
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator, RouterInterface $router,
                                EventDispatcherInterface $eventDispatcher, UserProviderInterface $userProvider,
                                ?int $resetTokenTtl)
    {
        $this->session         = $session;
        $this->translator      = $translator;
        $this->router          = $router;
        $this->eventDispatcher = $eventDispatcher;
        $this->userProvider    = $userProvider;
        $this->resetTokenTtl   = $resetTokenTtl;
    }

    /**
     * Displays password reset request form.
     * @Route("/lost-password", name="harmony_user_password_request", methods={"GET"})
     *
     * @return Response
     */
    public function request(): Response
    {
        $form = $this->createForm(PasswordRequestType::class);

        return $this->render('@HarmonyUser/Password/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Sends password reset request email.
     * @Route("/lost-password", name="harmony_user_password_request_send", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     * @throws \Exception
     */
    public function send(Request $request)
    {
        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);

        try {
            if ($form->isValid()) {
                $username = $form->getData()['username'];

                if (null === $username) {
                    return $this->render('@HarmonyUser/Password/request.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
                /** @var User $user */
                $user = $this->userProvider->findUserByUsername($username);
            } else {
                return $this->render('@HarmonyUser/Password/request.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }
        catch (UsernameNotFoundException $e) {
            $this->session->getFlashBag()
                ->add('error', $this->translator->trans('password.request.invalid_username', [], 'UserBundle'));

            return $this->render('@HarmonyUser/Password/request.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        if (!$user->isPasswordRequestExpired($this->resetTokenTtl)) {
            return $this->render('@HarmonyUser/Password/already_requested.html.twig', [
                'email' => $user->getEmail(),
                'ttl'   => $this->resetTokenTtl / 60 / 60,
            ]);
        }

        if (null === $user->getResetToken()) {
            $user->setResetToken(TokenGenerator::generateToken());
        }

        $mailer = $this->get(TwigSwiftMailer::class);
        $mailer->sendResetMessage($user);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        if (null !== $this->eventDispatcher) {
            $event = new PasswordRequestEvent($user, [
                'ip'         => $request->getClientIp(),
                'referer'    => $request->headers->get('referer'),
                'user-agent' => $request->headers->get('User-Agent'),
            ]);
            $this->eventDispatcher->dispatch('security.password.requested', $event);
        }

        return new RedirectResponse($this->router->generate('harmony_user_password_request_sent',
            ['email' => $user->getEmail()]));
    }

    /**
     * Tells the user to check his email provider.
     * @Route("/lost-password-confirmation", name="harmony_user_password_request_sent", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function sent(Request $request)
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->router->generate('harmony_user_security_request'));
        }

        return $this->render('@HarmonyUser/Password/requested.html.twig', [
            'email' => $email,
            'ttl'   => $this->resetTokenTtl / 60 / 60,
        ]);
    }

    /**
     * Resets user password.
     * @Route("/password-reset/{token}", name="harmony_user_password_reset", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param mixed   $token
     *
     * @return RedirectResponse|Response
     */
    public function reset(Request $request, ?string $token)
    {
        try {
            $user = $this->get(UserProvider::class)->findUserByResetToken($token);
        }
        catch (UsernameNotFoundException $e) {
            $this->session->getFlashBag()
                ->add('error', $this->translator->trans('password.reset.invalid_token', [], 'UserBundle'));

            $form = $this->createForm(PasswordRequestType::class);

            return $this->render('@HarmonyUser/Password/request.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $form = $this->createForm(PasswordResetType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if (null === $user->getPlainPassword()) {
                return $this->render('@HarmonyUser/Password/reset.html.twig', [
                    'form'  => $form->createView(),
                    'token' => $token,
                ]);
            }

            if (0 !== strlen($password = $user->getPlainPassword())) {
                $encoder = $this->get(UserProvider::class)->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $user->eraseCredentials();
            }

            $user->setResetToken(null);
            $user->setPasswordRequestedAt(null);

            if (method_exists($user, 'setIsPasswordResetRequired')) {
                $user->setIsPasswordResetRequired(0);
            }

            if (null !== $this->eventDispatcher) {
                $event = new PasswordResetEvent($user, [
                    'ip'         => $request->getClientIp(),
                    'referer'    => $request->headers->get('referer'),
                    'user-agent' => $request->headers->get('User-Agent'),
                ]);
                $this->eventDispatcher->dispatch('user.password.reset', $event);
            }

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $this->session->getFlashBag()
                ->add('success', $this->translator->trans('password.reset.success', [], 'UserBundle'));

            $url      = $this->router->generate('harmony_user_login');
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('@HarmonyUser/Password/reset.html.twig', [
            'token' => $token,
            'form'  => $form->createView(),
        ]);
    }
}
