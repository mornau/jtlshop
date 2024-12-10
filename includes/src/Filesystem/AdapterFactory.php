<?php

declare(strict_types=1);

namespace JTL\Filesystem;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;

/**
 * Class AdapterFactory
 * @package JTL\Filesystem
 */
class AdapterFactory
{
    /**
     * AdapterFactory constructor.
     *
     * @param array{'fs_adapter': string, 'fs_timeout': int, 'ftp_port': int, 'ftp_user': string,
     *      'ftp_pass': string, 'ftp_ssl': int, 'ftp_path': string, 'ftp_hostname': string,
     *      'sftp_user': string, 'sftp_pass': string, 'sftp_privkey': string,
     *      'sftp_port': int, 'sftp_path': string, 'sftp_user': string, 'sftp_hostname': string} $config
     */
    public function __construct(private array $config)
    {
    }

    /**
     * @return FilesystemAdapter
     */
    public function getAdapter(): FilesystemAdapter
    {
        return match ($this->config['fs_adapter']) {
            'ftp'   => new FtpAdapter(FtpConnectionOptions::fromArray($this->getFtpConfig())),
            'sftp'  => new SftpAdapter($this->getSftpConfig(), \rtrim($this->config['sftp_path'], '/') . '/'),
            default => new LocalFilesystemAdapter(\PFAD_ROOT),
        };
    }

    /**
     * @param string $adapter
     */
    public function setAdapter(string $adapter): void
    {
        $this->config['fs_adapter'] = $adapter;
    }

    /**
     * @param array{ftp_host?: string, ftp_port?: int, ftp_username?: string,
     *        ftp_password?: string, ftp_root?: string, ftp_ssl?: int} $config
     */
    public function setFtpConfig(array $config): void
    {
        $this->config = \array_merge($this->config, $config);
    }

    /**
     * @param array{sftp_host?: string, sftp_port?: int, sftp_username?: string,
     *       sftp_password?: string, sftp_root?: string, sftp_privkey: string} $config
     */
    public function setSftpConfig(array $config): void
    {
        $this->config = \array_merge($this->config, $config);
    }

    /**
     * @return array{'host': string, 'port': int, 'username': string,
     *      'password': string, 'ssl': bool, 'root': string,
     *      'timeout': int, 'passive': bool, 'ignorePassiveAddress': bool}
     */
    private function getFtpConfig(): array
    {
        return [
            'host'                 => $this->config['ftp_hostname'],
            'port'                 => $this->config['ftp_port'],
            'username'             => $this->config['ftp_user'],
            'password'             => $this->config['ftp_pass'],
            'ssl'                  => (int)$this->config['ftp_ssl'] === 1,
            'root'                 => \rtrim($this->config['ftp_path'], '/') . '/',
            'timeout'              => $this->config['fs_timeout'],
            'passive'              => true,
            'ignorePassiveAddress' => false
        ];
    }

    /**
     * @return SftpConnectionProvider
     */
    public function getSftpConfig(): SftpConnectionProvider
    {
        $pass    = empty($this->config['sftp_pass']) ? null : $this->config['sftp_pass'];
        $key     = empty($this->config['sftp_privkey']) ? null : $this->config['sftp_privkey'];
        $keyPass = null;
        if ($key !== null && $pass !== null) {
            $keyPass = $pass;
        }

        return new SftpConnectionProvider(
            $this->config['sftp_hostname'],
            $this->config['sftp_user'],
            $pass,
            $key,
            $keyPass,
            $this->config['sftp_port'],
            false,
            $this->config['fs_timeout']
        );
    }
}
