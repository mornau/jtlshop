<?php

declare(strict_types=1);

namespace JTL\CSV;

use InvalidArgumentException;
use JTL\DB\DbInterface;
use JTL\Helpers\Request;

class Importer
{
    public const STRATEGY_TRUNCATE_BEFORE = 0;

    public const STRATEGY_OVERWRITE_EXISTING = 1;

    public const STRATEGY_INSERT_NEW_ONLY = 2;

    protected ?string $fileName = null;

    protected ?string $delimiter = null;

    /**
     * @var string[]
     */
    protected array $fieldNames = [];

    /**
     * @var array<string, string>
     */
    protected array $fieldNameMapping = [];

    protected ?string $targetTable = null;

    protected bool $truncateBefore = false;

    protected bool $overwriteExisting = false;

    /**
     * @var int[]
     */
    protected array $errorLineNums = [];

    /**
     * @var int[]
     */
    protected array $successLineNums = [];

    public function __construct(protected DbInterface $db)
    {
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function setDelimiter(?string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param string[] $fieldNames
     * @return void
     */
    public function setFieldNames(array $fieldNames): void
    {
        $this->fieldNames = $fieldNames;
    }

    public function addFieldNameMapping(string $alias, string $original): void
    {
        $this->fieldNameMapping[$alias] = $original;
    }

    public function setTruncateBefore(bool $truncateBefore): void
    {
        $this->truncateBefore = $truncateBefore;
    }

    public function isTruncateBefore(): bool
    {
        return $this->truncateBefore;
    }

    public function setOverwriteExisting(bool $overwriteExisting): void
    {
        $this->overwriteExisting = $overwriteExisting;
    }

    public function isOverwriteExisting(): bool
    {
        return $this->overwriteExisting;
    }

    /**
     * @param int $importStrategy
     * @return void
     */
    public function setStrategy(int $importStrategy): void
    {
        if ($importStrategy === static::STRATEGY_TRUNCATE_BEFORE) {
            $this->setTruncateBefore(true);
            $this->setOverwriteExisting(false);
        } elseif ($importStrategy === static::STRATEGY_OVERWRITE_EXISTING) {
            $this->setTruncateBefore(false);
            $this->setOverwriteExisting(true);
        } elseif ($importStrategy === static::STRATEGY_INSERT_NEW_ONLY) {
            $this->setTruncateBefore(false);
            $this->setOverwriteExisting(false);
        } else {
            throw new InvalidArgumentException('importStrategy must be 0, 1 or 2');
        }
    }

    public function setTargetTable(?string $targetTable): void
    {
        $this->targetTable = $targetTable;
    }

    /**
     * @return int[]
     */
    public function getErrorLineNums(): array
    {
        return $this->errorLineNums;
    }

    /**
     * @return int[]
     */
    public function getSuccessLineNums(): array
    {
        return $this->successLineNums;
    }

    public function setFromUserInput(): void
    {
        $this->setFileName($_FILES['csvfile']['tmp_name']);
        $this->setStrategy(Request::verifyGPCDataInt('importType'));
    }

    protected function detectDelimiter(): string
    {
        $file      = \fopen($this->fileName, 'r');
        $firstLine = \fgets($file);

        foreach ([';', ',', '|', '\t'] as $delim) {
            if (\str_contains($firstLine, $delim)) {
                \fclose($file);

                return $delim;
            }
        }

        \fclose($file);

        return ';';
    }

    /**
     * @param resource $csvFile
     */
    protected function loadCsvHeader($csvFile, string $delimiter): array|bool
    {
        $fieldNames = \fgetcsv($csvFile, null, $delimiter);

        foreach ($fieldNames as $i => $fieldName) {
            if (isset($this->fieldNameMapping[$fieldName])) {
                $fieldNames[$i] = $this->fieldNameMapping[$fieldName];
            }
        }

        if ($this->verifyFieldNames($fieldNames) === false) {
            return false;
        }

        return $fieldNames;
    }

    protected function pretruncate(): void
    {
        $this->db->query('TRUNCATE ' . $this->targetTable);
    }

    /**
     * @return bool true when import of object was successful
     */
    protected function importObject(object $obj, int $lineNum): bool
    {
        if ($this->overwriteExisting) {
            return $this->db->upsert($this->targetTable, $obj) !== -1;
        }
        return $this->db->insert($this->targetTable, $obj) !== 0;
    }

    /**
     * Validate field names found in the CSV header
     * @param string[] $fieldNames
     * @return bool true if the combination of field names is valid
     */
    protected function verifyFieldNames(array $fieldNames): bool
    {
        return true;
    }

    /**
     * Modify $obj before import
     * @return bool true if transformation was successful
     */
    protected function preprocessObject(object $obj, int $lineNum): bool
    {
        return true;
    }

    /**
     * @return bool false if any errors happened
     */
    public function runImport(): bool
    {
        if ($this->fileName === null) {
            throw new InvalidArgumentException(\__('No input file provided.'));
        }

        $csvFile    = \fopen($this->fileName, 'r');
        $delimiter  = $this->delimiter ?? $this->detectDelimiter();
        $fieldNames = $this->fieldNames;
        $lineNum    = 1;

        if (\count($fieldNames) === 0) {
            $lineNum++;
            $fieldNames = $this->loadCsvHeader($csvFile, $delimiter);
            if ($fieldNames === false) {
                return false;
            }
        }

        if ($this->truncateBefore && $this->targetTable !== null) {
            $this->pretruncate();
        }

        $this->errorLineNums   = [];
        $this->successLineNums = [];
        while (($row = \fgetcsv($csvFile, null, $delimiter)) !== false) {
            $obj = new \stdClass();
            foreach ($fieldNames as $i => $fieldName) {
                $obj->$fieldName = $row[$i];
            }

            if ($this->preprocessObject($obj, $lineNum) === false) {
                $this->errorLineNums[] = $lineNum;
                continue;
            }

            if ($this->importObject($obj, $lineNum) === false) {
                $this->errorLineNums[] = $lineNum;
            } else {
                $this->successLineNums[] = $lineNum;
            }

            $lineNum++;
        }

        return \count($this->errorLineNums) === 0;
    }
}
