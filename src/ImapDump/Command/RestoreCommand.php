<?php

namespace ImapDump\Command;

use Ddeboer\Imap\ConnectionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestoreCommand extends AuthCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('restore')
             ->setDescription('Restore your imap mailboxes.')
             ->addArgument('source', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConnectionInterface $connection */
        $connection  = parent::execute($input, $output);
        $destination = $input->getArgument('source');

        $rootFiles = scandir($destination);

        foreach ($rootFiles as $file) {

            $sysFile   = in_array($file, ['.', '..']);
            $flagsFile = substr($file, -6) == '.flags';

            if ($sysFile || $flagsFile) {
                continue;
            }

            $filepath = $destination . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filepath)) {

                $mailboxName = $file;

                printf('Restoring Mailbox "%s"' . PHP_EOL, $mailboxName);

                if (!$connection->hasMailbox($mailboxName)) {
                    printf('Mailbox "%s" not found -> Creating..' . PHP_EOL, $mailboxName);
                    $mailbox = $connection->createMailbox($mailboxName);
                } else {
                    printf('Mailbox "%s" already exists' . PHP_EOL, $mailboxName);
                    $mailbox = $connection->getMailbox($mailboxName);
                }

                $subFiles = scandir($filepath);

                foreach ($subFiles as $subFile) {

                    $sysFile   = in_array($subFile, ['.', '..']);
                    $flagsFile = substr($subFile, -6) == '.flags';

                    if ($sysFile || $flagsFile) {
                        continue;
                    }

                    printf('Restoring Message "%s"' . PHP_EOL, $subFile);

                    $subFilePath      = $filepath . DIRECTORY_SEPARATOR . $subFile;
                    $subFileFlagsPath = $filepath . DIRECTORY_SEPARATOR . $subFile . '.flags';

                    $message = file_get_contents($subFilePath);
                    $flags   = null;

                    if (is_file($subFileFlagsPath)) {
                        $flags = file_get_contents($subFileFlagsPath);
                    }

                    $mailbox->addMessage($message, $flags);
                }
            }
        }
    }
}