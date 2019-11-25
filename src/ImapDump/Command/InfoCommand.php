<?php

namespace ImapDump\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends AuthCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('info')
             ->setDescription('Information about your imap mailbox.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = parent::execute($input, $output);

        $imapQuota = $connection->getQuota();

        $imapUsageInMb = $imapQuota['usage'] / 1024;
        $imapLimitInMb = $imapQuota['limit'] / 1024;

        printf(
            'Mailbox has %s MB usage of %s MB',
            number_format($imapUsageInMb, 2, '.', ''),
            number_format($imapLimitInMb, 2, '.', '')
        );
    }
}