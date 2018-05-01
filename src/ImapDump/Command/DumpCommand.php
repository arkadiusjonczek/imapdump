<?php

namespace ImapDump\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    protected function configure()
    {
        $this->setName('dump')
             ->setDescription('Backup and restore your imap mailboxes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // execute
    }
}