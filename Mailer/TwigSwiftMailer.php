<?php

namespace Harmony\UserBundle\Mailer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

/**
 * Class TwigSwiftMailer
 *
 * @package Harmony\UserBundle\Mailer
 */
class TwigSwiftMailer
{

    /** @var \Swift_Mailer $mailer */
    protected $mailer;

    /** @var UrlGeneratorInterface $router */
    protected $router;

    /** @var Environment $twig */
    protected $twig;

    /** @var array $parameters */
    protected $parameters;

    /**
     * TwigSwiftMailer constructor.
     *
     * @param \Swift_Mailer         $mailer
     * @param UrlGeneratorInterface $router
     * @param Environment           $twig
     * @param array                 $parameters
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, Environment $twig,
                                array $parameters)
    {
        $this->mailer     = $mailer;
        $this->router     = $router;
        $this->twig       = $twig;
        $this->parameters = $parameters;
    }

    /**
     * @param UserInterface $user
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendResetMessage(UserInterface $user)
    {
        $template = '@HarmonyUser/Password/request_email.html.twig';
        $url      = $this->router->generate('harmony_user_password_reset', ['token' => $user->getResetToken()],
            UrlGeneratorInterface::ABSOLUTE_URL);
        $context  = [
            'user'            => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($template, $context, $this->parameters['email_from'], $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context  = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject  = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = (new \Swift_Message())->setSubject($subject)->setFrom($fromEmail)->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}
