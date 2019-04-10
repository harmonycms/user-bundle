<?php

namespace Harmony\Bundle\UserBundle\Command;

use Harmony\Bundle\UserBundle\Manager\UserManagerInterface;
use Harmony\Bundle\UserBundle\Security\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreateUserCommand
 * Inspired by CreateUserCommand by FOSUserBundle
 *
 * @see     https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Command/CreateUserCommand.php.
 * @package Harmony\Bundle\UserBundle\Command
 */
class CreateUserCommand extends Command
{

    /**
     * @var UserManagerInterface
     */
    private $manager;

    /**
     * CreateUserCommand constructor.
     *
     * @param UserManagerInterface $manager
     */
    public function __construct(UserManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('user:create')->setDescription('Create a user.')->setDefinition([
            new InputArgument('username', InputArgument::REQUIRED, 'The username'),
            new InputArgument('email', InputArgument::REQUIRED, 'The email'),
            new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
        ])->setHelp(<<<'EOT'
The <info>%command.name%</info> command creates a user:
  <info>%command.full_name%</info>
This interactive shell will ask you for an email and then a password.
You can alternatively specify the email and password as arguments:
  <info>%command.full_name% user@example.com mypassword</info>
EOT
        );
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
     * @return void null or 0 if everything went fine, or an error code
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $email    = $input->getArgument('email');
        if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        $password   = $input->getArgument('password');
        $superadmin = $input->getOption('super-admin');

        $user = $this->manager->getInstance();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->addRole(UserInterface::ROLE_USER);
        if ($superadmin) {
            $user->addRole(UserInterface::ROLE_SUPER_ADMIN);
        }
        try {
            $this->manager->create($user);
            $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
        }
        catch (\Exception $e) {
            $output->writeln(sprintf('<error>Error</error>, user <comment>%s</comment> not created. %s', $email,
                $e->getMessage()));
        }
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
    protected function interact(InputInterface $input, OutputInterface $output)
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

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email) || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \InvalidArgumentException('Invalid email');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \InvalidArgumentException('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}