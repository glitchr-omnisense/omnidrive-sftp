# omnidrive/sftp

A directory of an SFTP server, over SSH, as an [omnidrive](https://github.com/glitchr-omnisense/omnidrive)
drive, through league/flysystem-sftp-v3 (phpseclib 3, pure PHP: no ssh2 extension).

```sh
composer require omnisense/omnidrive omnidrive/sftp
```

```php
use Omnidrive\Registry;
use Omnidrive\Sftp\SftpDriveFactory;

$drives = new Registry([new SftpDriveFactory()], [
    'backup' => ['factory' => 'sftp', 'options' => ['host' => 'backup.example.org', 'username' => 'site', 'private_key' => '/run/secrets/id_ed25519', 'root' => '/srv/backup', 'host_fingerprint' => 'SHA256:...']],
]);
```

| Option | Default | |
|---|---|---|
| `host`, `username` | required | |
| `password` | | |
| `private_key`, `passphrase` | | a path to the key, or the key itself |
| `use_agent` | false | the SSH agent's keys |
| `port` | 22 | |
| `root` | `/` | the directory behind the drive's paths |
| `host_fingerprint` | none | the server's key checked before anything is sent |
| `timeout`, `max_tries` | 10 s, 4 | |
| `permissions`, `public_url`, `visibility`, `directory_visibility`, `retain_visibility` | | as every drive |

The connection is opened on the first operation. `capabilities()`: not local, public addresses with
`public_url`, no temporary address.

## Verified, and not

- Verified (2026-10-23, omnidrive's harness, PHP 8.4): the contract's tests against OpenSSH's SFTP
  (atmoz/sftp, a password, a chrooted user); in plain PHP (`bare --live`); a server that does not
  answer is a `DriveException` of the operation asked, nothing connecting when the drive is built.
- Not verified: a private key, the SSH agent, the server's key checked (`host_fingerprint`).

License: MIT. Unofficial; not affiliated with The PHP League or phpseclib.
