<?php

namespace Harmony\Bundle\UserBundle\Command;

use Harmony\Bundle\UserBundle\Manager\UserManagerInterface;
use Harmony\Bundle\UserBundle\Security\UserInterface;
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
        $this->setName('user:promote')
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command promotes a user by adding a role
  <info>php %command.full_name% user ROLE_CUSTOM</info>
  <info>php %command.full_name% --super user</info>
EOT
            )
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super', null, InputOption::VALUE_NONE,
                    'Instead specifying role, use this to quickly add the super administrator role')
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
        $email = $input->getArgument('username');
        $role  = true === $input->getOption('super') ? UserInterface::ROLE_SUPER_ADMIN : $input->getArgument('role');

        $user = $this->manager->getUser($email);
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
        $questions = [];

        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Username can not be empty');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \Exception('Role can not be empty');
                }

                return $role;
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')
                ->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}