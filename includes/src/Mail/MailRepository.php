<?php

declare(strict_types=1);

namespace JTL\Mail;

use JTL\Abstracts\AbstractDBRepository;
use JTL\DataObjects\DataTableObjectInterface;

/**
 * Class MailRepository
 * @package JTL\Mail
 */
class MailRepository extends AbstractDBRepository
{
    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'emails';
    }

    /**
     * @inheritdoc
     */
    public function getKeyName(): string
    {
        return 'id';
    }

    /**
     * @param DataTableObjectInterface $mailDataTableObject
     * @return int
     */
    public function queueMailDataTableObject(DataTableObjectInterface $mailDataTableObject): int
    {
        return $this->insertMail($mailDataTableObject);
    }

    /**
     * @param DataTableObjectInterface $insertDTO
     * @return int
     */
    public function insertMail(DataTableObjectInterface $insertDTO): int
    {
        return $this->getDB()->insertRow($this->getTableName(), $insertDTO->toObject());
    }

    /**
     * @param int $chunkSize
     * @return array
     */
    public function getNextMailsFromQueue(int $chunkSize): array
    {
        $stmt = 'SELECT * FROM ' . $this->getTableName() .
            ' WHERE (isSendingNow = 0 AND sendCount < 3 AND errorCount < 3) OR reSend = 1' .
            ' ORDER BY priority, id LIMIT :chunkSize';

        return $this->getDB()->getArrays($stmt, ['chunkSize' => $chunkSize]);
    }

    /**
     * @param int $mailId
     * @return array
     */
    public function getMailByQueueId(int $mailId): array
    {
        $stmt = 'SELECT * FROM ' . $this->getTableName() .
            ' WHERE id = :mailId';

        return $this->getDB()->getArrays($stmt, ['mailId' => $mailId]);
    }

    /**
     * @param int[] $mailIds
     * @param int   $isSendingNow
     * @return bool
     */
    public function setMailStatus(array $mailIds, int $isSendingNow): bool
    {
        $ids  = \implode(',', $this->ensureIntValuesInArray($mailIds));
        $stmt = 'UPDATE ' .
            $this->getTableName() . ' SET reSend = 0, isSendingNow = :isSendingNow, sendCount = sendCount + 1 ' .
            'WHERE id IN (:mailId)';

        return $this->getDB()->queryPrepared($stmt, [
            'isSendingNow' => $isSendingNow,
            'mailId'       => $ids
        ]);
    }

    /**
     * @param int    $mailID
     * @param string $errorMsg
     * @return int
     */
    public function setError(int $mailID, string $errorMsg): int
    {
        $stmt = 'UPDATE emails ' .
            'SET isSendingNow = 0, sendCount = sendCount + 1, errorCount = errorCount + 1, lastError = :errorMsg ' .
            'WHERE id = :mailID';

        return (int)$this->getDB()->queryPrepared($stmt, [
            'errorMsg' => $errorMsg,
            'mailID'   => $mailID,
        ]);
    }

    /**
     * @param int|string $value
     * @return bool
     */
    public function deleteQueuedMail(int|string $value): bool
    {
        $deleted = $this->getDB()->deleteRow(
            $this->getTableName(),
            $this->getKeyName(),
            $value
        );

        return $deleted !== self::DELETE_FAILED;
    }
}
