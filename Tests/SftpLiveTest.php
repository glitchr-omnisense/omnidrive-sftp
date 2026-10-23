<?php

namespace Omnidrive\Sftp\Tests;

use Omnidrive\Drive;
use Omnidrive\Sftp\SftpDriveFactory;
use Omnidrive\Testing\DriveContractTests;
use PHPUnit\Framework\TestCase;

/**
 * The contract against a real SFTP server: the harness's
 * (docker compose run --rm omnidrive test), or the one OMNIDRIVE_SFTP_* name.
 * Skipped when none is named.
 */
final class SftpLiveTest extends TestCase
{
    use DriveContractTests;

    protected function drive(): Drive
    {
        $host = getenv('OMNIDRIVE_SFTP_HOST');
        if (false === $host || '' === $host) {
            self::markTestSkipped('No SFTP server named (OMNIDRIVE_SFTP_HOST).');
        }

        return (new SftpDriveFactory())->create([
            'host' => $host,
            'port' => getenv('OMNIDRIVE_SFTP_PORT') ?: null,
            'username' => (string) getenv('OMNIDRIVE_SFTP_USERNAME'),
            'password' => (string) getenv('OMNIDRIVE_SFTP_PASSWORD'),
            'root' => getenv('OMNIDRIVE_SFTP_ROOT') ?: '/',
        ], 'sftp');
    }
}
