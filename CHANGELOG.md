# Changelog

Every version of omnidrive/sftp, newest first ([Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
[Semantic Versioning](https://semver.org/)). A release's notes are its section here: the release
workflow refuses a tag without one.

## [Unreleased]

## [1.0.0] - 2026-11-13

The first stable version. It requires `omnisense/omnidrive` `^1.0` with no stability flag, so `composer require omnidrive/sftp` installs it in a project with Composer's default minimum-stability; the 1.x series follows semantic versioning. A tag `vX.Y.Z` is released by `.github/workflows/release.yml` once the package's CI passes on it.

Omnidrive SFTP: a directory of an SFTP server, over SSH, as an omnidrive drive, through league/flysystem-sftp-v3 (phpseclib 3, pure PHP) - a password, a private key or the SSH agent, the server's key checked, Unix modes.
