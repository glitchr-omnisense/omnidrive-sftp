# omnidrive/sftp

A directory of an SFTP server, over SSH, as an
[omnisense/omnidrive](https://github.com/glitchr-omnisense/omnidrive) drive, through
league/flysystem-sftp-v3 (`SftpAdapter`, phpseclib 3: pure PHP, no `ssh2` extension).

## Installation

```sh
composer require omnisense/omnidrive omnidrive/sftp
```

Requires PHP 8.2, `omnisense/omnidrive` (1.x) and `league/flysystem-sftp-v3` (^3.0, with
`phpseclib/phpseclib` 3).

## Credentials

An SSH account on the server: a password, or a private key (a path or the key itself, with its
passphrase), or the keys of the SSH agent (`use_agent`). The server's own key is checked when
`host_fingerprint` is given - the only protection against a server impersonated on the way; get it
from the server's administrator or `ssh-keygen -lf` on its host key.

## Options

`new SftpDriveFactory()`; factory name `sftp`.

| Option | Type | Default | Required | |
|---|---|---|---|---|
| `host` | string | - | yes | |
| `username` | string | - | yes | |
| `password` | string | none | no | |
| `private_key` | string | none | no | a path to the key, or the key itself |
| `passphrase` | string | none | no | the key's |
| `use_agent` | bool | `false` | no | the SSH agent's keys |
| `port` | int | `22` | no | |
| `root` | string | `/` | no | the directory behind the drive's paths |
| `host_fingerprint` | string | none | no | `SHA256:...` or the MD5 form phpseclib gives: the server's key checked before anything is sent |
| `timeout` | int | `10` | no | seconds |
| `max_tries` | int | `4` | no | connection attempts |
| `permissions` | map | `{file: {public: 0644, private: 0600}, dir: {public: 0755, private: 0700}}` | no | the Unix mode of each visibility; octal strings accepted, a mode left out keeps its default |
| `public_url` | string or list | none | no | the prefix files are served from, or several (sharded) |
| `visibility` | `public`/`private` | `public` (the adapter's) | no | a write's visibility when it does not say |
| `directory_visibility` | `public`/`private` | `private` | no | the same for directories |
| `retain_visibility` | bool | `true` | no | whether a copy keeps its source's visibility |

## Capabilities

| Capability | |
|---|---|
| `local` | no |
| `publicUrls` | with `public_url` (the files served by a web server over the same directory) |
| `temporaryUrls` | no |
| visibility per file | yes: the file's Unix mode, by `permissions` |
| checksum | any algorithm `hash()` knows (the file downloaded and hashed) |

## Behaviour

The connection (`SftpConnectionProvider`) is opened on the first operation and kept for the
process; a dropped one is opened again, up to `max_tries`. Every path is under `root`. A write
makes the missing directories with the `directory_visibility` mode (`0700` by default). A file's
visibility is read from its mode against `permissions`.

## Example

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Omnidrive\Exception\NotSupportedException;
use Omnidrive\Registry;
use Omnidrive\Sftp\SftpDriveFactory;

$drives = new Registry([new SftpDriveFactory()], [
    'backup' => ['factory' => 'sftp', 'options' => ['host' => 'sftp', 'username' => 'omnidrive', 'password' => 'omnidrive-harness', 'root' => '/upload', 'timeout' => 5]],
]);
$backup = $drives->get('backup');                     // nothing connected yet

$backup->write('2026-10-25/site.sql', "-- dump\n", ['visibility' => 'private']);
echo json_encode($backup->capabilities()->toArray()), "\n";
echo $backup->size('2026-10-25/site.sql'), ' ', $backup->visibility('2026-10-25/site.sql')->value, ' ', $backup->lastModified('2026-10-25/site.sql')->format('Y-m-d'), "\n";
try {
    $backup->publicUrl('2026-10-25/site.sql');
} catch (NotSupportedException $e) {
    echo $e->getMessage(), "\n";
}
$backup->deleteDirectory('2026-10-25');
var_dump($backup->directoryExists('2026-10-25'));
```

```
{"local":false,"publicUrls":false,"temporaryUrls":false}
8 private 2026-10-25
The "backup" drive does not give public addresses: no "public_url" configured and its adapter gives none.
bool(false)
```

(run on 2026-10-25 in omnidrive's harness against its OpenSSH server, atmoz/sftp, a password)

## Errors

A server that does not answer, a refused login, a refused key or fingerprint are `DriveException`s
of the operation asked, with phpseclib's message (`Unable to connect to host: ...`); a missing
file read is a `FileNotFoundException`. A missing `host` or `username` is an
`InvalidConfigException`.

## Verified, and not

Verified (2026-10-23 and 2026-10-25, omnidrive's harness, PHP 8.4): the contract's tests against
OpenSSH's SFTP (atmoz/sftp, a password, a chrooted user); in plain PHP (`bare --live`); a server
that does not answer is a `DriveException` of the operation asked, nothing connecting when the
drive is built. Not verified: a private key, the SSH agent, the server's key checked
(`host_fingerprint`).

## Responsibility

An independent, unofficial client, not affiliated with The PHP League or phpseclib. Whoever runs it is responsible for the data it stores and the
access it gives (a public or temporary address serves the file to whoever holds it); see the core's
Responsibility section ([omnisense/omnidrive](https://github.com/glitchr-omnisense/omnidrive#responsibility)).
