# imapdump

Backup and restore your imap mailboxes.

## Installation

```bash
clone https://github.com/arkadiusjonczek/imapdump.git
cd imapdump
composer install
chmod u+x imapdump
./imapdump
```

## Usage

Info about your mailbox usage and limit quota:

```bash
./imapdump info <host> <username>
```

List all your mailboxes with number of messages:

```bash
./imapdump ls <host> <username>
```

Backup your mailboxes to your filesystem:

```bash
./imapdump backup <host> <username> <filepath>
```

Restore your mailboxes from filesystem backup:

```bash
./imapdump restore <host> <username> <filepath>
```

Add email passwords in a configuration file:

```bash
cp config.sample.ini config.ini
```

```bash
[accounts]
user@mail.com=password
user2@mail.com=password
```