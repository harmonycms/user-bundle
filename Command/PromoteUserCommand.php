<?php

namespace Harmony\Bundle\UserBundle\Command;

use Harmony\Bundle\UserBundle\Manager\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class PromoteUserCommand
 * Inspired by CreateUserCommand by FOSUserBundle
 *
 * @see     https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Command/PromoteUserCommand.php.
 * @package Harmony\Bundle\UserBundle\Command
 */
class PromoteUserCommand extends Command
{

    /**
     * @var UserManagerInterface
     */
    private $manager;

    /**
     * PromoteUserCommand constructor.
     *
     * @param UserManagerInterface $manager
     */
    public function __construct(UserManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('user:promote')->setDescription('Promotes a user by adding a role')->setHelp(<<<'EOT'
The <info>%command.name%</info> command promotes a user by adding a role
  <info>%command.full_name% garak@example.com ROLE_CUSTOM</info>
EOT
        )->setDefinition([
            new InputArgument('email', InputArgument::REQUIRED, 'The email'),
            new InputArgument('role', InputArgument::REQUIRED, 'The role'),
        ]);
    }

    /**
     * Executes the current command.
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null null or 0 if everything went fine, or an error code
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $role  = $input->getArgument('role');
        $user  = $this->manager->getUser($email);
        if (null === $user) {
            $output->writeln(sprintf('<error>Error</error>: user <comment>%s</comment> not found.', $email));

            return 1;
        }
        if ($user->hasRole($role)) {
            $output->writeln(sprintf('User <comment>%s</comment> did already have <comment>%s</comment> role.', $email,
                $role));
        } else {
            $user->addRole($role);
            $this->manager->update($user);
            $output->writeln(sprintf('Role <comment>%s</comment> has been added to user <comment>%s</comment>.', $role,
                $email));
        }

        return 0;
    }

    /**
     * Interacts with the user.
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \InvalidArgumentException('Email can not be empty');
                }

                return $email;
            });
            $email = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $email);
        }
        if (!$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \InvalidArgumentException('Role can not be empty');
                }

                return $role;
            });
            $role = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('role', $role);
        }
    }
}