<?php

declare(strict_types=1);

namespace JTL\Update;

use DateTime;
use JsonSerializable;
use JTL\DB\DbInterface;

/**
 * Class Migration
 * @package JTL\Update
 */
class Migration implements JsonSerializable
{
    use MigrationTableTrait;
    use MigrationTrait;

    /**
     * @var string|null
     */
    protected $author = '';

    /**
     * @var string|null
     */
    protected $description = '';

    /**
     * @var bool
     */
    protected bool $deleteData = true;

    /**
     * Migration constructor.
     *
     * @param DbInterface   $db
     * @param null|string   $info
     * @param DateTime|null $executed
     */
    public function __construct(DbInterface $db, protected ?string $info = null, protected ?DateTime $executed = null)
    {
        $this->setDB($db);
        $this->info = \ucfirst(\strtolower($info ?? ''));
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return MigrationHelper::mapClassNameToId($this->getName());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return \str_replace('JTL\Migrations\\', '', \get_class($this));
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?: $this->info ?? '';
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return DateTime::createFromFormat('YmdHis', (string)$this->getId())
            ?: throw new \InvalidArgumentException('Invalid migration ID');
    }

    /**
     * @return DateTime|null
     */
    public function getExecuted(): ?DateTime
    {
        return $this->executed;
    }

    /**
     * @return bool
     */
    public function doDeleteData(): bool
    {
        return $this->deleteData;
    }

    /**
     * @param bool $deleteData
     */
    public function setDeleteData(bool $deleteData): void
    {
        $this->deleteData = $deleteData;
    }

    /**
     * @return array{id: int|null, name: string, author: string|null, description: string,
     *     executed: DateTime|null, created: DateTime}
     */
    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->getId(),
            'name'        => $this->getName(),
            'author'      => $this->getAuthor(),
            'description' => $this->getDescription(),
            'executed'    => $this->getExecuted(),
            'created'     => $this->getCreated()
        ];
    }
}
