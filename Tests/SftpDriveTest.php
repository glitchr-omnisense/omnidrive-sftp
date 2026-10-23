<?php

namespace Omnidrive\Sftp\Tests;

use Omnidrive\Exception\DriveException;
use Omnidrive\Exception\InvalidConfigException;
use Omnidrive\Sftp\SftpDriveFactory;
use PHPUnit\Framework\TestCase;

final class SftpDriveTest extends TestCase
{
    public function testNothingConnectsUntilTheFirstOperation(): void
    {
        $drive = (new SftpDriveFactory())->create(['host' => '192.0.2.1', 'username' => 'site', 'password' => 'x', 'timeout' => 1, 'max_tries' => 0], 'backup');

        self::assertSame('backup', $drive->name());
        self::assertSame(['local' => false, 'publicUrls' => false, 'temporaryUrls' => false], $drive->capabilities()->toArray());
        self::assertNull($drive->localPath('a.txt'));

        try {
            $drive->exists('a.txt');
            self::fail('a server that does not answer');
        } catch (DriveException $e) {
            self::assertSame(['exists', 'backup', 'a.txt'], [$e->operation, $e->drive, $e->path]);
        }
    }

    public function testAPublicAddressIsThePrefixGiven(): void
    {
        $drive = (new SftpDriveFactory())->create(['host' => 'files.example.org', 'username' => 'site', 'root' => '/var/www/files', 'public_url' => 'https://files.example.org']);

        self::assertSame('https://files.example.org/a/b.pdf', $drive->publicUrl('a/b.pdf'));
    }

    public function testTheHostAndTheUserAreRequired(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "sftp" drive needs: host, username.');
        (new SftpDriveFactory())->create(['password' => 'x']);
    }
}
