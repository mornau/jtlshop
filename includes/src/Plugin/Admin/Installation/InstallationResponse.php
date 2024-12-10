<?php

declare(strict_types=1);

namespace JTL\Plugin\Admin\Installation;

use stdClass;

/**
 * Class InstallationResponse
 * @package JTL\Plugin\Admin\Installation
 */
class InstallationResponse
{
    public const STATUS_OK = 'OK';

    public const STATUS_FAILED = 'FAILED';

    /**
     * @var string
     */
    private string $status = self::STATUS_OK;

    /**
     * @var string|null
     */
    private ?string $errorMessage;

    /**
     * @var string|null
     */
    private ?string $dir_name;

    /**
     * @var string|null
     */
    private ?string $path;

    /**
     * @var string[]
     */
    private array $files_unpacked = [];

    /**
     * @var string[]
     */
    private array $files_failed = [];

    /**
     * @var string[]
     */
    private array $messages = [];

    /**
     * @var stdClass
     */
    private stdClass $html;

    /**
     * @var string|null
     */
    private ?string $license;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return InstallationResponse
     */
    public function setStatus(string $status): InstallationResponse
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param string|null $errorMessage
     * @return InstallationResponse
     */
    public function setError(?string $errorMessage): InstallationResponse
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirName(): ?string
    {
        return $this->dir_name;
    }

    /**
     * @param string|null $dir_name
     * @return InstallationResponse
     */
    public function setDirName(?string $dir_name): InstallationResponse
    {
        $this->dir_name = $dir_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return InstallationResponse
     */
    public function setPath(?string $path): InstallationResponse
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFilesUnpacked(): array
    {
        return $this->files_unpacked;
    }

    /**
     * @param string[] $files_unpacked
     * @return InstallationResponse
     */
    public function setFilesUnpacked(array $files_unpacked): InstallationResponse
    {
        $this->files_unpacked = $files_unpacked;

        return $this;
    }

    /**
     * @param string $file
     * @return InstallationResponse
     */
    public function addFileUnpacked(string $file): InstallationResponse
    {
        $this->files_unpacked[] = $file;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFilesFailed(): array
    {
        return $this->files_failed;
    }

    /**
     * @param string[] $files_failed
     * @return InstallationResponse
     */
    public function setFilesFailed(array $files_failed): InstallationResponse
    {
        $this->files_failed = $files_failed;

        return $this;
    }

    /**
     * @param string $file
     * @return InstallationResponse
     */
    public function addFileFailed(string $file): InstallationResponse
    {
        $this->files_failed[] = $file;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     * @return InstallationResponse
     */
    public function setMessages(array $messages): InstallationResponse
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @param string $message
     * @return InstallationResponse
     */
    public function addMessage(string $message): InstallationResponse
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return stdClass
     */
    public function getHtml(): stdClass
    {
        return $this->html;
    }

    /**
     * @param stdClass $html
     * @return InstallationResponse
     */
    public function setHtml(stdClass $html): InstallationResponse
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLicense(): ?string
    {
        return $this->license;
    }

    /**
     * @param string|null $license
     */
    public function setLicense(?string $license): void
    {
        $this->license = $license;
    }

    /**
     * @return string
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return \json_encode(\get_object_vars($this), JSON_THROW_ON_ERROR) ?: '';
    }
}
