<?php

declare(strict_types=1);

namespace JTL\RMA\DomainObjects\dbeS;

use JTL\Abstracts\AbstractDbeSObject;

/**
 * Class RMAItemSyncObject
 *
 * @package JTL\RMA\DomainObjects\dbeS
 * @description Container for RMA data imported from WAWI via dbeS. Is a child from SyncDomainObject
 */
class RMAItemSyncObject extends AbstractDbeSObject
{
    /**
     * @param int    $kArtikel
     * @param int    $kRMGrund
     * @param string $cName
     * @param float  $fAnzahl
     * @param int    $kLieferscheinPos
     * @param bool   $nGutschreiben
     * @param string $dErstellt
     */
    public function __construct(
        public int $kArtikel,
        public int $kRMGrund,
        public string $cName,
        public float $fAnzahl,
        public int $kLieferscheinPos,
        public bool $nGutschreiben,
        public string $dErstellt,
    ) {
        parent::__construct();
    }
}