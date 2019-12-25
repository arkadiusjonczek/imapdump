<?php

namespace ImapDump\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AuthCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('ls')
             ->setDescription('List your imap mailboxes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = parent::execute($input, $output);

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

        $connection->close();
    }
}