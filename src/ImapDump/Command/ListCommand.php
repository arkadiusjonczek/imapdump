<?php

namespace ImapDump\Command;

use Ddeboer\Imap\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ListCommand extends Command
{
    protected function configure()
    {
        $this->setName('ls')
             ->setDescription('List your imap mailboxes.')
             ->addArgument('host', InputArgument::REQUIRED)
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
        $mailboxes = $connection->getMailboxes();

        foreach ($mailboxes as $mailbox) {
            // Skip container-only mailboxes
            // @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
            if ($mailbox->getAttributes() & \LATT_NOSELECT) {
                continue;
            }

            // $mailbox is instance of \Ddeboer\Imap\Mailbox
            printf('Mailbox "%s" has %s messages' . "\r\n", $mailbox->getName(), $mailbox->count());
        }
    }
}