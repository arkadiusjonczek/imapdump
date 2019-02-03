<?php

namespace ImapDump\Command;

use Ddeboer\Imap\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class AuthCommand extends Command
{
    protected function configure()
    {
        $this->addArgument('host', InputArgument::REQUIRED)
             ->addArgument('username', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host     = $input->getArgument('host');
        $username = $input->getArgument('username');

        $question = new Question('Password: ');
        $question->setHidden(true);

        $questionHelper = new QuestionHelper();
        $password = $questionHelper->ask($input, $output, $question);

        $server = new Server($host);
        $connection = $server->authenticate($username, $password);

        return $connection;
    }
}