<?php

namespace ImapDump\Command;

use Ddeboer\Imap\ConnectionInterface;
use Ddeboer\Imap\MessageInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends AuthCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('backup')
             ->setDescription('Backup your imap mailboxes.')
             ->addArgument('destination', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConnectionInterface $connection */
        $connection  = parent::execute($input, $output);
        $destination = $input->getArgument('destination');

        $mailboxes = $connection->getMailboxes();

        foreach ($mailboxes as $mailbox) {
            // Skip container-only mailboxes
            // @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
            if ($mailbox->getAttributes() & \LATT_NOSELECT) {
                continue;
            }

            printf('Backup Mailbox "%s" with %s messages' . PHP_EOL, $mailbox->getName(), $mailbox->count());

            $mailboxName      = $mailbox->getName();
            $mailboxDirectory = $destination . DIRECTORY_SEPARATOR . $mailboxName . DIRECTORY_SEPARATOR;

            if (!is_dir($mailboxDirectory)) {
                mkdir($mailboxDirectory);
            }

            foreach ($mailbox as $message) {

                $filename      = rawurlencode($message->getId());
                $filenameFlags = rawurlencode($message->getId()) . '.flags';
                $filepath      = $mailboxDirectory . DIRECTORY_SEPARATOR . $filename;
                $filepathFlags = $mailboxDirectory . DIRECTORY_SEPARATOR . $filenameFlags;

                $rawMessage = $message->getRawMessage();

                if (empty($filename) || empty($rawMessage)) {
                    continue;
                }

                file_put_contents($filepath, $rawMessage);
                file_put_contents($filepathFlags, $this->getMessageFlags($message));
            }
        }
    }

    /**
     * Create flags string of imap message
     *
     * @param MessageInterface $message
     *
     * @return string
     */
    protected function getMessageFlags(MessageInterface $message)
    {
        $flags = '';

        if ($message->isAnswered()) {
            $flags .= '\\Answered ';
        }
        if ($message->isFlagged()) {
            $flags .= '\\Flagged ';
        }
        if ($message->isDeleted()) {
            $flags .= '\\Deleted ';
        }
        if ($message->isSeen()) {
            $flags .= '\\Seen ';
        }
        if ($message->isDraft()) {
            $flags .= '\\Draft ';
        }

        return $flags;
    }
}