<?php

namespace Omnidrive\Sftp;

use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use Omnidrive\Config;
use Omnidrive\Drive;
use Omnidrive\Flysystem\FlysystemDriveFactory;

/**
 * A directory of an SFTP server, over SSH, through league/flysystem-sftp-v3
 * (phpseclib 3, pure PHP: no ssh2 extension):
 *
 *   (new SftpDriveFactory())->create(['host' => 'backup.example.org', 'username' => 'site', 'private_key' => '/run/secrets/id_ed25519', 'root' => '/srv/backup']);
 *
 * Options: host and username (required), password, private_key (a path or the
 * key itself) and its passphrase, use_agent (the SSH agent's keys), port (22),
 * root (the directory behind the drive's paths, "/"), host_fingerprint (the
 * server's key checked, "SHA256:..." or the MD5 form phpseclib gives), timeout
 * (10 s), max_tries (4), public_url, visibility, directory_visibility,
 * permissions (as every drive).
 *
 * The connection is opened on the first operation, not here.
 */
final class SftpDriveFactory extends FlysystemDriveFactory
{
    protected function populate(Config $c): void
    {
        $c->defaults([
            'omnidrive.factory_name' => 'sftp',
            'omnidrive.factory_title' => 'An SFTP server (SSH)',
            'omnidrive.required_options' => ['host', 'username'],
            'port' => 22,
            'root' => '/',
            'use_agent' => false,
            'timeout' => 10,
            'max_tries' => 4,
        ]);
    }

    protected function build(Config $c, string $name): Drive
    {
        $provider = new SftpConnectionProvider(
            (string) $c->string('host'),
            (string) $c->string('username'),
            $c->string('password'),
            $c->string('private_key'),
            $c->string('passphrase'),
            $c->int('port', 22),
            $c->bool('use_agent'),
            $c->int('timeout', 10),
            $c->int('max_tries', 4),
            $c->string('host_fingerprint'),
        );

        return $this->drive($name, new SftpAdapter($provider, $c->string('root') ?? '/', $this->unixVisibility($c)), $c);
    }
}
