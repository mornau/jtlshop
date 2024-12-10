<?php

declare(strict_types=1);

namespace JTL\OPC;

use Exception;
use JTL\Backend\Revision;
use JTL\DB\DbInterface;
use JTL\Events\Dispatcher;
use JTL\Events\Event;
use JTL\Update\Updater;
use stdClass;

/**
 * Class PageDB
 * @package JTL\OPC
 */
class PageDB
{
    /**
     * PageDB constructor.
     * @param DbInterface $shopDB
     */
    public function __construct(protected DbInterface $shopDB)
    {
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function shopHasPendingUpdates(): bool
    {
        return (new Updater($this->shopDB))->hasPendingUpdates();
    }

    /**
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->shopDB->getSingleInt('SELECT COUNT(DISTINCT cPageId) AS cnt FROM topcpage', 'cnt');
    }

    /**
     * @return stdClass[]
     */
    public function getPages(): array
    {
        return $this->shopDB->getObjects('SELECT cPageId, cPageUrl FROM topcpage GROUP BY cPageId, cPageUrl');
    }

    /**
     * @param string $id
     * @return stdClass[]
     */
    public function getDraftRows(string $id): array
    {
        return $this->shopDB->selectAll('topcpage', 'cPageId', $id);
    }

    /**
     * @param string $id
     * @return int
     */
    public function getDraftCount(string $id): int
    {
        return $this->shopDB->getSingleInt(
            'SELECT COUNT(kPage) AS cnt FROM topcpage WHERE cPageId = :id',
            'cnt',
            ['id' => $id]
        );
    }

    /**
     * @param int $key
     * @return stdClass
     * @throws Exception
     */
    public function getDraftRow(int $key): stdClass
    {
        $draftRow = $this->shopDB->select('topcpage', 'kPage', $key);
        if (!\is_object($draftRow)) {
            throw new Exception('The OPC page draft could not be found in the database.');
        }

        return $draftRow;
    }

    /**
     * @param int $id
     * @return object
     * @throws Exception
     * @throws \JsonException
     */
    public function getRevisionRow(int $id)
    {
        $revision    = new Revision($this->shopDB);
        $revisionRow = $revision->getRevision($id);
        if ($revisionRow === null) {
            throw new Exception('The OPC page revision could not be found in the database.');
        }

        return \json_decode($revisionRow->content, false, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $id
     * @return null|stdClass
     */
    public function getPublicPageRow(string $id): ?stdClass
    {
        $res = $this->shopDB->getSingleObject(
            'SELECT * FROM topcpage
                WHERE cPageId = :pageID
                    AND dPublishFrom IS NOT NULL
                    AND dPublishFrom <= NOW()
                    AND (dPublishTo > NOW() OR dPublishTo IS NULL)
                ORDER BY dPublishFrom DESC',
            ['pageID' => $id]
        );
        if ($res !== null) {
            $res->kPage = (int)$res->kPage;
        }

        return $res;
    }

    /**
     * @param string $id
     * @return Page[]
     * @throws Exception
     */
    public function getDrafts(string $id): array
    {
        $drafts = [];
        foreach ($this->getDraftRows($id) as $draftRow) {
            $drafts[] = $this->getPageFromRow($draftRow);
        }

        return $drafts;
    }

    /**
     * @param int $key
     * @return Page
     * @throws Exception
     */
    public function getDraft(int $key): Page
    {
        $draftRow = $this->getDraftRow($key);
        $seo      = $this->getPageSeo($draftRow->cPageId);
        if (!empty($seo)) {
            $draftRow->cPageUrl = $seo;
        }

        return $this->getPageFromRow($draftRow);
    }

    /**
     * @param int $id
     * @return Page
     * @throws Exception
     */
    public function getRevision(int $id): Page
    {
        return $this->getPageFromRow($this->getRevisionRow($id));
    }

    /**
     * @param int $key
     * @return stdClass[]
     */
    public function getRevisionList(int $key): array
    {
        return (new Revision($this->shopDB))->getRevisions('opcpage', $key);
    }

    /**
     * @param string $id
     * @return Page|null
     * @throws Exception
     */
    public function getPublicPage(string $id): ?Page
    {
        $publicRow = $this->getPublicPageRow($id);
        $page      = $publicRow === null ? null : $this->getPageFromRow($publicRow);

        Dispatcher::getInstance()->fire(Event::OPC_PAGEDB_GETPUBLICPAGE, [
            'id'   => $id,
            'page' => &$page
        ]);

        return $page;
    }

    /**
     * @param string $pageID
     * @return string|null
     * @todo!! generate better URLs
     */
    public function getPageSeo(string $pageID): ?string
    {
        try {
            /** @var stdClass $pageIdObj */
            $pageIdObj = \json_decode($pageID, false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        if (empty($pageIdObj)) {
            return null;
        }

        $key = match ($pageIdObj->type) {
            'product'      => 'kArtikel',
            'category'     => 'kKategorie',
            'manufacturer' => 'kHersteller',
            'link'         => 'kLink',
            'attrib'       => 'kMerkmalWert',
            'special'      => 'suchspecial',
            'news'         => 'kNews',
            'newscat'      => 'kNewsKategorie',
            default        => null,
        };

        if (empty($key)) {
            return null;
        }
        $seo = $this->shopDB->getSingleObject(
            'SELECT cSeo FROM tseo WHERE cKey = :ckey AND kKey = :key AND kSprache = :lang',
            ['ckey' => $key, 'key' => $pageIdObj->id, 'lang' => $pageIdObj->lang]
        );
        if ($seo === null) {
            return null;
        }
        if (!empty($pageIdObj->attribs)) {
            $attribSeos = $this->shopDB->getObjects(
                "SELECT cSeo 
                    FROM tseo 
                    WHERE cKey = 'kMerkmalWert'
                        AND kKey IN (" . \implode(',', $pageIdObj->attribs) . ')
                        AND kSprache = :lang',
                ['lang' => $pageIdObj->lang]
            );
            if (\count($attribSeos) !== \count($pageIdObj->attribs)) {
                return null;
            }
        }
        $manufacturerSeo = null;
        if (!empty($pageIdObj->manufacturerFilter)) {
            $manufacturerSeo = $this->shopDB->getSingleObject(
                "SELECT cSeo FROM tseo WHERE cKey = 'kHersteller'
                     AND kKey = :kKey
                     AND kSprache = :lang",
                ['kKey' => $pageIdObj->manufacturerFilter, 'lang' => $pageIdObj->lang]
            );
            if ($manufacturerSeo === null) {
                return null;
            }
        }
        $result = '/' . $seo->cSeo;
        if (!empty($attribSeos)) {
            foreach ($attribSeos as $seo) {
                $result .= '__' . $seo->cSeo;
            }
        }
        if ($manufacturerSeo !== null) {
            $result .= '::' . $manufacturerSeo->cSeo;
        }

        return $result;
    }

    /**
     * @param Page $page
     * @return $this
     * @throws Exception
     */
    public function saveDraft(Page $page): self
    {
        if (
            $page->getUrl() === ''
            || $page->getLastModified() === ''
            || $page->getLockedAt() === ''
            || $page->getId() === ''
        ) {
            throw new Exception('The OPC page data to be saved is incomplete or invalid.');
        }

        Dispatcher::getInstance()->fire(Event::OPC_PAGEDB_SAVEDRAFT_POSTVALIDATE, [
            'page' => &$page
        ]);

        $page->setLastModified(\date('Y-m-d H:i:s'));

        $pageDB = (object)[
            'cPageId'       => $page->getId(),
            'dPublishFrom'  => $page->getPublishFrom() ?? '_DBNULL_',
            'dPublishTo'    => $page->getPublishTo() ?? '_DBNULL_',
            'cName'         => $page->getName(),
            'cPageUrl'      => $page->getUrl(),
            'cAreasJson'    => \json_encode($page->getAreaList(), \JSON_THROW_ON_ERROR),
            'dLastModified' => $page->getLastModified() ?? '_DBNULL_',
            'cLockedBy'     => $page->getLockedBy(),
            'dLockedAt'     => $page->getLockedAt() ?? '_DBNULL_',
        ];

        if ($page->getKey() > 0) {
            $dbPage = $this->shopDB->select('topcpage', 'kPage', $page->getKey());
            if ($dbPage === null) {
                throw new Exception('The OPC page could not be found in the DB.');
            }
            $oldAreasJson = $dbPage->cAreasJson;
            $newAreasJson = $pageDB->cAreasJson;

            if ($oldAreasJson !== $newAreasJson) {
                $revision = new Revision($this->shopDB);
                $revision->addRevision('opcpage', (int)$dbPage->kPage);
            }

            if ($this->shopDB->update('topcpage', 'kPage', $page->getKey(), $pageDB) === -1) {
                throw new Exception('The OPC page could not be updated in the DB.');
            }
        } else {
            $key = $this->shopDB->insert('topcpage', $pageDB);

            if ($key === 0) {
                throw new Exception('The OPC page could not be inserted into the DB.');
            }

            $page->setKey($key);
        }

        return $this;
    }

    /**
     * @param Page $page - existing page draft
     * @return $this
     * @throws Exception
     */
    public function saveDraftLockStatus(Page $page): self
    {
        $pageDB = (object)[
            'cLockedBy' => $page->getLockedBy(),
            'dLockedAt' => $page->getLockedAt() ?? '_DBNULL_',
        ];

        if ($this->shopDB->update('topcpage', 'kPage', $page->getKey(), $pageDB) === -1) {
            throw new Exception('The OPC page could not be updated in the DB.');
        }

        return $this;
    }

    /**
     * @param Page $page - existing page draft
     * @return $this
     * @throws Exception
     */
    public function saveDraftPublicationStatus(Page $page): self
    {
        $pageDB = (object)[
            'dPublishFrom' => $page->getPublishFrom() ?? '_DBNULL_',
            'dPublishTo'   => $page->getPublishTo() ?? '_DBNULL_',
            'cName'        => $page->getName(),
        ];

        if ($this->shopDB->update('topcpage', 'kPage', $page->getKey(), $pageDB) === -1) {
            throw new Exception('The OPC page publication status could not be updated in the DB.');
        }

        return $this;
    }

    /**
     * @param int    $draftKey
     * @param string $draftName
     * @return PageDB
     * @throws Exception
     */
    public function saveDraftName(int $draftKey, string $draftName): self
    {
        $pageDB = (object)[
            'cName' => $draftName,
        ];

        if ($this->shopDB->update('topcpage', 'kPage', $draftKey, $pageDB) === -1) {
            throw new Exception('The OPC draft name could not be updated in the DB.');
        }

        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function deletePage(string $id): self
    {
        $this->shopDB->delete('topcpage', 'cPageId', $id);

        return $this;
    }

    /**
     * @param int $key
     * @return $this
     */
    public function deleteDraft(int $key): self
    {
        $this->shopDB->delete('topcpage', 'kPage', $key);

        return $this;
    }

    /**
     * @param stdClass $row
     * @return Page
     * @throws Exception
     */
    protected function getPageFromRow(stdClass $row): Page
    {
        $page = (new Page())
            ->setKey((int)$row->kPage)
            ->setId($row->cPageId)
            ->setPublishFrom($row->dPublishFrom)
            ->setPublishTo($row->dPublishTo)
            ->setName($row->cName)
            ->setUrl($row->cPageUrl)
            ->setLastModified($row->dLastModified)
            ->setLockedBy($row->cLockedBy)
            ->setLockedAt($row->dLockedAt);

        $areaData = \json_decode($row->cAreasJson, true, 512, \JSON_THROW_ON_ERROR);

        if ($areaData !== null) {
            $page->getAreaList()->deserialize($areaData);
        }

        Dispatcher::getInstance()->fire(Event::OPC_PAGEDB_GETPAGEROW, [
            'row'  => &$row,
            'page' => &$page
        ]);

        return $page;
    }
}