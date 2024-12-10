<?php

declare(strict_types=1);

namespace JTL;

use Exception;
use JTL\DB\DbInterface;
use stdClass;

/**
 * Class Emailhistory
 * @package JTL
 */
class Emailhistory
{
    /**
     * @var int
     */
    public int $kEmailhistory = 0;

    /**
     * @var int
     */
    public int $kEmailvorlage = 0;

    /**
     * @var string
     */
    public string $cSubject = '';

    /**
     * @var string
     */
    public string $cFromName = '';

    /**
     * @var string
     */
    public string $cFromEmail = '';

    /**
     * @var string
     */
    public string $cToName = '';

    /**
     * @var string
     */
    public string $cToEmail = '';

    /**
     * @var string - date
     */
    public string $dSent = '';

    /**
     * @var DbInterface
     */
    private DbInterface $db;

    /**
     * Emailhistory constructor.
     * @param null|int         $id
     * @param null|object      $data
     * @param null|DbInterface $db
     */
    public function __construct(int $id = null, ?object $data = null, DbInterface $db = null)
    {
        $this->db = $db ?? Shop::Container()->getDB();
        if ($id > 0) {
            $this->loadFromDB($id);
        } elseif ($data !== null) {
            foreach (\array_keys(\get_object_vars($data)) as $member) {
                $methodName = 'set' . \mb_substr($member, 1);
                if (\method_exists($this, $methodName)) {
                    $this->$methodName($data->$member);
                }
            }
        }
    }

    /**
     * @param int $id
     * @return $this
     */
    protected function loadFromDB(int $id): self
    {
        $data = $this->db->select('temailhistory', 'kEmailhistory', $id);
        if ($data !== null && $data->kEmailhistory > 0) {
            $this->kEmailhistory = (int)$data->kEmailhistory;
            $this->kEmailvorlage = (int)$data->kEmailvorlage;
            $this->cSubject      = $data->cSubject;
            $this->cFromName     = $data->cFromName;
            $this->cFromEmail    = $data->cFromEmail;
            $this->cToName       = $data->cToName;
            $this->cToEmail      = $data->cToEmail;
            $this->dSent         = $data->dSent;
        }

        return $this;
    }

    /**
     * @param bool $primary
     * @return ($primary is true ? int|false : bool)
     * @throws Exception
     */
    public function save(bool $primary = true)
    {
        if ($this->kEmailhistory > 0) {
            return $this->update();
        }
        $ins                = new stdClass();
        $ins->kEmailvorlage = $this->kEmailvorlage;
        $ins->cSubject      = $this->cSubject;
        $ins->cFromName     = $this->cFromName;
        $ins->cFromEmail    = $this->cFromEmail;
        $ins->cToName       = $this->cToName;
        $ins->cToEmail      = $this->cToEmail;
        $ins->dSent         = $this->dSent;

        $key = $this->db->insert('temailhistory', $ins);
        if ($key > 0) {
            return $primary ? $key : true;
        }

        return false;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function update(): int
    {
        $upd                = new stdClass();
        $upd->kEmailhistory = $this->kEmailhistory;
        $upd->kEmailvorlage = $this->kEmailvorlage;
        $upd->cSubject      = $this->cSubject;
        $upd->cFromName     = $this->cFromName;
        $upd->cFromEmail    = $this->cFromEmail;
        $upd->cToName       = $this->cToName;
        $upd->cToEmail      = $this->cToEmail;
        $upd->dSent         = $this->dSent;

        return $this->db->updateRow('temailhistory', 'kEmailhistory', $this->getEmailhistory(), $upd);
    }

    /**
     * @return int
     */
    public function delete(): int
    {
        return $this->db->delete('temailhistory', 'kEmailhistory', $this->getEmailhistory());
    }

    /**
     * @param string $limitSQL
     * @return Emailhistory[]
     */
    public function getAll(string $limitSQL = ''): array
    {
        $historyData = $this->db->getObjects(
            'SELECT * 
                FROM temailhistory 
                ORDER BY dSent DESC' . $limitSQL
        );
        $history     = [];
        foreach ($historyData as $item) {
            $item->kEmailhistory = (int)$item->kEmailhistory;
            $item->kEmailvorlage = (int)$item->kEmailvorlage;
            $history[]           = new self(null, $item, $this->db);
        }

        return $history;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->db->getSingleInt('SELECT COUNT(*) AS cnt FROM temailhistory', 'cnt');
    }

    /**
     * @param int[]|numeric-string[] $ids
     * @return int
     */
    public function deletePack(array $ids): int
    {
        if (\count($ids) === 0) {
            return -1;
        }

        return $this->db->getAffectedRows(
            'DELETE 
                FROM temailhistory 
                WHERE kEmailhistory IN (' . \implode(',', \array_map('\intval', $ids)) . ')'
        );
    }

    /**
     * truncate the email-history-table
     * @return int
     */
    public function deleteAll(): int
    {
        Shop::Container()->getLogService()->notice('eMail-History gelöscht');
        $res = $this->db->getAffectedRows('DELETE FROM temailhistory');
        $this->db->query('TRUNCATE TABLE temailhistory');

        return $res;
    }

    /**
     * @return int
     */
    public function getEmailhistory(): int
    {
        return $this->kEmailhistory;
    }

    /**
     * @param int $kEmailhistory
     * @return $this
     */
    public function setEmailhistory(int $kEmailhistory): self
    {
        $this->kEmailhistory = $kEmailhistory;

        return $this;
    }

    /**
     * @return int
     */
    public function getEmailvorlage(): int
    {
        return $this->kEmailvorlage;
    }

    /**
     * @param int $kEmailvorlage
     * @return $this
     */
    public function setEmailvorlage(int $kEmailvorlage): self
    {
        $this->kEmailvorlage = $kEmailvorlage;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->cSubject;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): self
    {
        $this->cSubject = $subject;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFromName(): ?string
    {
        return $this->cFromName;
    }

    /**
     * @param string $fromName
     * @return $this
     */
    public function setFromName(string $fromName): self
    {
        $this->cFromName = $fromName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFromEmail(): ?string
    {
        return $this->cFromEmail;
    }

    /**
     * @param string $fromEmail
     * @return $this
     */
    public function setFromEmail(string $fromEmail): self
    {
        $this->cFromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToName(): ?string
    {
        return $this->cToName;
    }

    /**
     * @param string $toName
     * @return $this
     */
    public function setToName(string $toName): self
    {
        $this->cToName = $toName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToEmail(): ?string
    {
        return $this->cToEmail;
    }

    /**
     * @param string $toEmail
     * @return $this
     */
    public function setToEmail(string $toEmail): self
    {
        $this->cToEmail = $toEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSent(): ?string
    {
        return $this->dSent;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setSent(string $date): self
    {
        $this->dSent = $date;

        return $this;
    }
}
