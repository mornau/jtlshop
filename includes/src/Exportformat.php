<?php

declare(strict_types=1);

namespace JTL;

use Exception;
use JTL\Cron\QueueEntry;
use JTL\DB\DbInterface;
use JTL\Smarty\JTLSmarty;
use Psr\Log\LoggerInterface;
use SmartyException;
use stdClass;

/**
 * Class Exportformat
 * @package JTL
 * @deprecated since 5.1.0
 */
class Exportformat
{
    public const SYNTAX_FAIL        = 1;
    public const SYNTAX_NOT_CHECKED = -1;
    public const SYNTAX_OK          = 0;

    /**
     * @var int
     */
    protected $kExportformat;

    /**
     * @var int
     */
    protected $kKundengruppe;

    /**
     * @var int
     */
    protected $kSprache;

    /**
     * @var int
     */
    protected $kWaehrung;

    /**
     * @var int
     */
    protected $kKampagne;

    /**
     * @var int
     */
    protected $kPlugin;

    /**
     * @var string
     */
    protected $cName;

    /**
     * @var string
     */
    protected $cDateiname;

    /**
     * @var string
     */
    protected $cKopfzeile;

    /**
     * @var string
     */
    protected $cContent;

    /**
     * @var string
     */
    protected $cFusszeile;

    /**
     * @var string
     */
    protected $cKodierung;

    /**
     * @var int
     */
    protected $nSpecial;

    /**
     * @var int
     */
    protected $nVarKombiOption;

    /**
     * @var int
     */
    protected $nSplitgroesse;

    /**
     * @var string
     */
    protected $dZuletztErstellt;

    /**
     * @var int
     */
    protected $nUseCache = 1;

    /**
     * @var JTLSmarty
     */
    protected $smarty;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var QueueEntry
     */
    protected $queue;

    /**
     * @var object
     */
    protected $currency;

    /**
     * @var bool
     */
    private $isOk = false;

    /**
     * @var string
     */
    private $tempFileName;

    /**
     * @var string
     */
    private $tempFile;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var DbInterface
     */
    private $db;

    /**
     * @var int
     */
    protected $nFehlerhaft = 0;

    /**
     * Exportformat constructor.
     *
     * @param int              $id
     * @param DbInterface|null $db
     */
    public function __construct(int $id = 0, DbInterface $db = null)
    {
        \trigger_error(__CLASS__ . ' is deprecated and should not be used anymore.', \E_USER_DEPRECATED);
        $this->db            = $db ?? Shop::Container()->getDB();
        $this->kExportformat = $id;
    }

    /**
     * @return DbInterface
     */
    public function getDB(): DbInterface
    {
        return $this->db;
    }

    /**
     * @param DbInterface $db
     */
    public function setDB(DbInterface $db): void
    {
        $this->db = $db;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function isOK(): bool
    {
        return $this->isOk;
    }

    /**
     * @param bool $bPrim
     * @return bool|int
     */
    public function save(bool $bPrim = true)
    {
        $ins                   = new stdClass();
        $ins->kKundengruppe    = (int)$this->kKundengruppe;
        $ins->kSprache         = (int)$this->kSprache;
        $ins->kWaehrung        = (int)$this->kWaehrung;
        $ins->kKampagne        = (int)$this->kKampagne;
        $ins->kPlugin          = (int)$this->kPlugin;
        $ins->cName            = $this->cName;
        $ins->cDateiname       = $this->cDateiname;
        $ins->cKopfzeile       = $this->cKopfzeile;
        $ins->cContent         = $this->cContent;
        $ins->cFusszeile       = $this->cFusszeile;
        $ins->cKodierung       = $this->cKodierung;
        $ins->nSpecial         = (int)$this->nSpecial;
        $ins->nVarKombiOption  = (int)$this->nVarKombiOption;
        $ins->nSplitgroesse    = (int)$this->nSplitgroesse;
        $ins->dZuletztErstellt = empty($this->dZuletztErstellt) ? '_DBNULL_' : $this->dZuletztErstellt;
        $ins->nUseCache        = $this->nUseCache;
        $ins->nFehlerhaft      = self::SYNTAX_NOT_CHECKED;

        $this->kExportformat = $this->db->insert('texportformat', $ins);
        if ($this->kExportformat > 0) {
            return $bPrim ? $this->kExportformat : true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function update(): int
    {
        $upd                   = new stdClass();
        $upd->kKundengruppe    = (int)$this->kKundengruppe;
        $upd->kSprache         = (int)$this->kSprache;
        $upd->kWaehrung        = (int)$this->kWaehrung;
        $upd->kKampagne        = (int)$this->kKampagne;
        $upd->kPlugin          = (int)$this->kPlugin;
        $upd->cName            = $this->cName;
        $upd->cDateiname       = $this->cDateiname;
        $upd->cKopfzeile       = $this->cKopfzeile;
        $upd->cContent         = $this->cContent;
        $upd->cFusszeile       = $this->cFusszeile;
        $upd->cKodierung       = $this->cKodierung;
        $upd->nSpecial         = (int)$this->nSpecial;
        $upd->nVarKombiOption  = (int)$this->nVarKombiOption;
        $upd->nSplitgroesse    = (int)$this->nSplitgroesse;
        $upd->dZuletztErstellt = empty($this->dZuletztErstellt) ? '_DBNULL_' : $this->dZuletztErstellt;
        $upd->nUseCache        = $this->nUseCache;
        $upd->nFehlerhaft      = self::SYNTAX_NOT_CHECKED;

        return $this->db->update('texportformat', 'kExportformat', $this->getExportformat(), $upd);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setTempFileName(string $name): self
    {
        $this->tempFileName = \basename($name);
        $this->tempFile     = \PFAD_ROOT . \PFAD_EXPORT . $this->tempFileName;

        return $this;
    }

    /**
     * @return int
     */
    public function delete(): int
    {
        return $this->db->delete('texportformat', 'kExportformat', $this->getExportformat());
    }

    /**
     * @param int $kExportformat
     * @return $this
     */
    public function setExportformat(int $kExportformat): self
    {
        $this->kExportformat = $kExportformat;

        return $this;
    }

    /**
     * @param int $customerGroupID
     * @return $this
     */
    public function setKundengruppe(int $customerGroupID): self
    {
        $this->kKundengruppe = $customerGroupID;

        return $this;
    }

    /**
     * /**
     * @param int $languageID
     * @return $this
     */
    public function setSprache(int $languageID): self
    {
        $this->kSprache = $languageID;

        return $this;
    }

    /**
     * @param int $kWaehrung
     * @return $this
     */
    public function setWaehrung(int $kWaehrung): self
    {
        $this->kWaehrung = $kWaehrung;

        return $this;
    }

    /**
     * @param int $kKampagne
     * @return $this
     */
    public function setKampagne(int $kKampagne): self
    {
        $this->kKampagne = $kKampagne;

        return $this;
    }

    /**
     * @param int $kPlugin
     * @return $this
     */
    public function setPlugin(int $kPlugin): self
    {
        $this->kPlugin = $kPlugin;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->cName = $name;

        return $this;
    }

    /**
     * @param string $cDateiname
     * @return $this
     */
    public function setDateiname(string $cDateiname): self
    {
        $this->cDateiname = $cDateiname;

        return $this;
    }

    /**
     * @param string $cKopfzeile
     * @return $this
     */
    public function setKopfzeile($cKopfzeile): self
    {
        $this->cKopfzeile = $cKopfzeile;

        return $this;
    }

    /**
     * @param string $cContent
     * @return $this
     */
    public function setContent($cContent): self
    {
        $this->cContent = $cContent;

        return $this;
    }

    /**
     * @param string $cFusszeile
     * @return $this
     */
    public function setFusszeile($cFusszeile): self
    {
        $this->cFusszeile = $cFusszeile;

        return $this;
    }

    /**
     * @param string $cKodierung
     * @return $this
     */
    public function setKodierung($cKodierung): self
    {
        $this->cKodierung = $cKodierung;

        return $this;
    }

    /**
     * @param int $nSpecial
     * @return $this
     */
    public function setSpecial(int $nSpecial): self
    {
        $this->nSpecial = $nSpecial;

        return $this;
    }

    /**
     * @param int $nVarKombiOption
     * @return $this
     */
    public function setVarKombiOption(int $nVarKombiOption): self
    {
        $this->nVarKombiOption = $nVarKombiOption;

        return $this;
    }

    /**
     * @param int $nSplitgroesse
     * @return $this
     */
    public function setSplitgroesse(int $nSplitgroesse): self
    {
        $this->nSplitgroesse = $nSplitgroesse;

        return $this;
    }

    /**
     * @param string $dZuletztErstellt
     * @return $this
     */
    public function setZuletztErstellt($dZuletztErstellt): self
    {
        $this->dZuletztErstellt = $dZuletztErstellt;

        return $this;
    }

    /**
     * @return int
     */
    public function getExportformat(): int
    {
        return (int)$this->kExportformat;
    }

    /**
     * @return int
     */
    public function getKundengruppe(): int
    {
        return (int)$this->kKundengruppe;
    }

    /**
     * @return int
     */
    public function getSprache(): int
    {
        return (int)$this->kSprache;
    }

    /**
     * @return int
     */
    public function getWaehrung(): int
    {
        return (int)$this->kWaehrung;
    }

    /**
     * @return int
     */
    public function getKampagne(): int
    {
        return (int)$this->kKampagne;
    }

    /**
     * @return int
     */
    public function getPlugin(): int
    {
        return (int)$this->kPlugin;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->cName;
    }

    /**
     * @return string|null
     */
    public function getDateiname(): ?string
    {
        return $this->cDateiname;
    }

    /**
     * @return string|null
     */
    public function getKopfzeile(): ?string
    {
        return $this->cKopfzeile;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->cContent;
    }

    /**
     * @return string|null
     */
    public function getFusszeile(): ?string
    {
        return $this->cFusszeile;
    }

    /**
     * @return string|null
     */
    public function getKodierung(): ?string
    {
        return $this->cKodierung;
    }

    /**
     * @return int|null
     */
    public function getSpecial(): ?int
    {
        return $this->nSpecial;
    }

    /**
     * @return int|null
     */
    public function getVarKombiOption(): ?int
    {
        return $this->nVarKombiOption;
    }

    /**
     * @return int|null
     */
    public function getSplitgroesse(): ?int
    {
        return $this->nSplitgroesse;
    }

    /**
     * @return string|null
     */
    public function getZuletztErstellt(): ?string
    {
        return $this->dZuletztErstellt;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getExportProductCount(): int
    {
        return 0;
    }

    /**
     * @return QueueEntry|null
     */
    public function getQueue(): ?QueueEntry
    {
        return $this->queue;
    }

    /**
     * @return bool
     */
    public function useCache(): bool
    {
        return (int)$this->nUseCache === 1;
    }

    /**
     * @param int $caching
     * @return $this
     */
    public function setCaching(int $caching): self
    {
        $this->nUseCache = $caching;

        return $this;
    }

    /**
     * @return int
     */
    public function getCaching(): int
    {
        return (int)$this->nUseCache;
    }

    /**
     * @return bool
     */
    public function startExport(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function check()
    {
        return true;
    }

    /**
     * @param int $error
     * @return string
     */
    private static function getHTMLState(int $error): string
    {
        try {
            return Shop::Smarty()->assign('exportformat', (object)['nFehlerhaft' => $error])
                ->fetch('snippets/exportformat_state.tpl');
        } catch (SmartyException | Exception) {
            return '';
        }
    }

    /**
     * @param string $out
     * @param string $message
     * @return string
     */
    private static function stripMessage(string $out, string $message): string
    {
        $message = \strip_tags($message);
        // strip possible call stack
        if (\preg_match('/(Stack trace|Call Stack):/', $message, $hits)) {
            $callstackPos = \mb_strpos($message, $hits[0]);
            if ($callstackPos !== false) {
                $message = \mb_substr($message, 0, $callstackPos);
            }
        }
        $errText  = '';
        $fatalPos = \mb_strlen($out);
        // strip smarty output if fatal error occurs
        if (\preg_match('/((Recoverable )?Fatal error|Uncaught Error):/ui', $out, $hits)) {
            $fatalPos = \mb_strpos($out, $hits[0]);
            if ($fatalPos !== false) {
                $errText = \mb_substr($out, 0, $fatalPos);
            }
        }
        // strip possible error position from smarty output
        $errText = (string)\preg_replace('/[\t\n]/', ' ', \mb_substr($errText, 0, $fatalPos));
        $len     = \mb_strlen($errText);
        if ($len > 75) {
            $errText = '...' . \mb_substr($errText, $len - 75);
        }

        return \htmlentities($message) . ($len > 0 ? '<br/>on line: ' . \htmlentities($errText) : '');
    }

    /**
     * @return stdClass
     */
    public static function ioCheckSyntax(): stdClass
    {
        return (object)[
            'result'  => 'fail',
            'message' => 'Class is no longer supported.',
        ];
    }

    /**
     * @return bool
     * @deprecated since 5.0.1 - do syntax check only with io-method because smarty syntax check can throw fatal error
     */
    public function checkSyntax(): bool
    {
        return false;
    }

    /**
     * @return bool
     * @deprecated since 5.0.1 - do syntax check only with io-method because smarty syntax check can throw fatal error
     */
    public function doCheckSyntax(): bool
    {
        return false;
    }

    /**
     * @return array
     * @deprecated since 5.0.1 - do syntax check only with io-method because smarty syntax check can throw fatal error
     */
    public function checkAll(): array
    {
        return [];
    }

    public function updateError(): void
    {
    }
}
