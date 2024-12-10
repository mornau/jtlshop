<?php

declare(strict_types=1);

namespace JTL\dbeS;

use Generator;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\dbeS\Push\AbstractPush;
use JTL\dbeS\Push\Customers;
use JTL\dbeS\Push\Data as PushData;
use JTL\dbeS\Push\DeletedCustomers;
use JTL\dbeS\Push\ImageAPI;
use JTL\dbeS\Push\Invoice;
use JTL\dbeS\Push\MediaFiles;
use JTL\dbeS\Push\Orders as PushOrders;
use JTL\dbeS\Push\Payments;
use JTL\dbeS\Push\Returns as PushReturns;
use JTL\dbeS\Sync\AbstractSync;
use JTL\dbeS\Sync\Brocken;
use JTL\dbeS\Sync\Categories;
use JTL\dbeS\Sync\Characteristics;
use JTL\dbeS\Sync\ConfigGroups;
use JTL\dbeS\Sync\Customer;
use JTL\dbeS\Sync\Data;
use JTL\dbeS\Sync\DeliveryNotes;
use JTL\dbeS\Sync\Downloads;
use JTL\dbeS\Sync\Globals;
use JTL\dbeS\Sync\ImageCheck;
use JTL\dbeS\Sync\ImageLink;
use JTL\dbeS\Sync\Images;
use JTL\dbeS\Sync\ImageUpload;
use JTL\dbeS\Sync\Manufacturers;
use JTL\dbeS\Sync\Orders;
use JTL\dbeS\Sync\Products;
use JTL\dbeS\Sync\QuickSync;
use JTL\dbeS\Sync\Returns;
use JTL\Helpers\Text;
use JTL\Settings\Option\Image;
use JTL\Settings\Settings;
use JTL\XML;
use Psr\Log\LoggerInterface;

/**
 * Class Starter
 * @package JTL\dbeS
 */
class Starter
{
    public const ERROR_NOT_AUTHORIZED = 3;

    public const ERROR_UNZIP = 2;

    public const OK = 0;

    private const DIRECTION_PUSH = 'push';

    private const DIRECTION_PULL = 'pull';

    /**
     * @var array<string, class-string<AbstractSync>>
     */
    private static array $pullMapping = [
        'Artikel_xml'      => Products::class,
        'Bestellungen_xml' => Orders::class,
        'Returns_xml'      => Returns::class,
        'Bilder_xml'       => Images::class,
        'Brocken_xml'      => Brocken::class,
        'Data_xml'         => Data::class,
        'Download_xml'     => Downloads::class,
        'Globals_xml'      => Globals::class,
        'Hersteller_xml'   => Manufacturers::class,
        'img_check'        => ImageCheck::class,
        'img_link'         => ImageLink::class,
        'img_upload'       => ImageUpload::class,
        'Kategorien_xml'   => Categories::class,
        'Konfig_xml'       => ConfigGroups::class,
        'Kunden_xml'       => Customer::class,
        'Lieferschein_xml' => DeliveryNotes::class,
        'Merkmal_xml'      => Characteristics::class,
        'QuickSync_xml'    => QuickSync::class,
        'SetKunde_xml'     => Customer::class
    ];

    /**
     * @var array<string, class-string<AbstractPush>>
     */
    private static array $pushMapping = [
        'GetBestellungen_xml'     => PushOrders::class,
        'GetReturns_xml'          => PushReturns::class,
        'GetData_xml'             => PushData::class,
        'GetKunden_xml'           => Customers::class,
        'GetMediendateien_xml'    => MediaFiles::class,
        'GetZahlungen_xml'        => Payments::class,
        'Invoice_xml'             => Invoice::class,
        'bild'                    => ImageAPI::class,
        'GetDeletedCustomers_xml' => DeletedCustomers::class,
    ];

    /**
     * @var array<string, class-string<NetSyncHandler>>
     */
    private static array $netSyncMapping = [
        'Cronjob_xml'           => SyncCronjob::class,
        'GetDownloadStruct_xml' => ProductDownloads::class,
        'Upload_xml'            => Uploader::class
    ];

    /**
     * @var mixed|null
     */
    private $data;

    /**
     * @var array|null
     */
    private ?array $postData = null;

    /**
     * @var array|null
     */
    private ?array $files = null;

    /**
     * @var string
     */
    private string $unzipPath;

    /**
     * @var string
     */
    private string $wawiVersion = 'unknown';

    /**
     * Starter constructor.
     * @param Synclogin         $auth
     * @param FileHandler       $fileHandler
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     * @param LoggerInterface   $logger
     */
    public function __construct(
        private readonly Synclogin $auth,
        private readonly FileHandler $fileHandler,
        private readonly DbInterface $db,
        private readonly JTLCacheInterface $cache,
        private readonly LoggerInterface $logger
    ) {
        $this->checkPermissions();
    }

    private function checkPermissions(): void
    {
        $tmpDir = \PFAD_ROOT . \PFAD_DBES . \PFAD_SYNC_TMP;
        if (!\is_writable($tmpDir)) {
            \syncException(
                'Fehler beim Abgleich: Das Verzeichnis ' . $tmpDir . ' ist nicht beschreibbar!',
                \FREIDEFINIERBARER_FEHLER
            );
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param string|null $index
     * @return array|string
     */
    public function getPostData(string $index = null)
    {
        return $index === null ? $this->postData : ($this->postData[$index] ?? '');
    }

    /**
     * @param array $postData
     */
    public function setPostData(array $postData): void
    {
        $this->postData = $postData;
    }

    /**
     * @return string
     */
    public function getUnzipPath(): string
    {
        return $this->unzipPath;
    }

    /**
     * @param string $unzipPath
     */
    public function setUnzipPath(string $unzipPath): void
    {
        $this->unzipPath = $unzipPath;
    }

    /**
     * @param array $post
     * @return bool
     * @throws \Exception
     */
    public function checkAuth(array $post): bool
    {
        if (!isset($post['userID'], $post['userPWD'])) {
            return false;
        }
        $userID = Text::convertUTF8($post['userID']);
        $pass   = Text::convertUTF8($post['userPWD']);

        return $this->auth->checkLogin($userID, $pass) === true;
    }

    /**
     * @param array $files
     * @return array|null
     */
    public function getFiles(array $files): ?array
    {
        return $this->fileHandler->getSyncFiles($files);
    }

    /**
     * @param string $handledFile
     */
    private function executeNetSync(string $handledFile): void
    {
        $mapping = self::$netSyncMapping[$handledFile] ?? null;
        if ($mapping === null) {
            return;
        }
        NetSyncHandler::create($mapping, $this->db, $this->logger);
        exit;
    }

    /**
     * handling of files that do not fit the general push/pull scheme
     *
     * @param string $handledFile
     * @param array  $post
     * @throws \Exception
     */
    private function handleSpecialCases(string $handledFile, array $post): void
    {
        if (!\in_array($handledFile, ['lastjobs', 'mytest', 'bild'], true)) {
            return;
        }
        $res = $this->init($post, [], false);
        switch ($handledFile) {
            case 'lastjobs':
                if ($res === self::OK) {
                    $lastjobs = new LastJob($this->db, $this->logger);
                    $lastjobs->execute();
                }
                echo $res;
                break;
            case 'mytest':
                if ($res === self::OK) {
                    $test = new Test($this->db);
                    echo $test->execute();
                } else {
                    \syncException(\APPLICATION_VERSION, $res);
                }
                break;
            case 'bild':
                $conf = Settings::stringValue(Image::EXTERNAL_INTERFACE_ENABLED);
                if ($conf === 'N') {
                    exit; // api disabled
                }
                if ($conf === 'W' && $res !== self::OK) {
                    exit; // api is wawi only
                }
                $api = new ImageAPI($this->db, $this->cache, $this->logger);
                $api->getData();
                break;
        }
        exit;
    }

    /**
     * @param string $handledFile
     * @param array  $post
     * @param array  $files
     * @throws \Exception
     */
    public function start(string $handledFile, array $post, array $files): never
    {
        if (isset($post['uID'], $post['uPWD']) && !isset($post['userID'], $post['userPWD'])) {
            // for some reason, wawi sometimes sends uID/uPWD and sometimes userID/userPWD
            $post['userID']  = $post['uID'];
            $post['userPWD'] = $post['uPWD'];
        }
        $this->setVersionByUserAgent();
        $this->handleSpecialCases($handledFile, $post);
        $this->executeNetSync($handledFile);
        $direction = self::DIRECTION_PULL;
        $handler   = self::$pullMapping[$handledFile] ?? null;
        if ($handler === null) {
            $handler = self::$pushMapping[$handledFile] ?? null;
            if ($handler !== null) {
                $direction = self::DIRECTION_PUSH;
            }
        }
        if ($handler === null) {
            die();
        }
        $this->setPostData($post);
        $this->setData($files['data']['tmp_name'] ?? null);
        if ($direction === self::DIRECTION_PULL) {
            $res    = '';
            $unzip  = $handler !== Brocken::class;
            $return = $this->init($post, $files, $unzip);
            if ($return === self::OK) {
                /** @var AbstractSync $sync */
                $sync = new $handler($this->db, $this->cache, $this->logger);
                $res  = $sync->handle($this);
            }
            if ($handledFile !== 'SetKunde_xml') {
                echo $return;
                exit;
            }
            if (\is_array($res)) {
                $serializedXML = $this->getWawiVersion() === 'unknown'
                    ? Text::convertISO(XML::serialize($res))
                    : XML::serialize($res);
                echo $return . ";\n" . $serializedXML;
            } else {
                echo $return . ';' . $res;
            }
        } else {
            $res = $this->init($post, [], false);
            if ($res === self::OK) {
                /** @var AbstractPush $pusher */
                $pusher = new $handler($this->db, $this->cache, $this->logger);
                $xml    = $pusher->getData();
                if (\is_array($xml) && \count($xml) > 0) {
                    $pusher->zipRedirect(\time() . '.jtl', $xml, $this->getWawiVersion());
                }
            }
            echo $res;
        }
        exit;
    }

    /**
     * @param array $post
     * @param array $files
     * @param bool  $unzip
     * @return int
     * @throws \Exception
     */
    public function init(array $post, array $files, bool $unzip = true): int
    {
        if (!$this->checkAuth($post)) {
            return self::ERROR_NOT_AUTHORIZED;
        }
        require_once \PFAD_ROOT . \PFAD_INCLUDES . 'sprachfunktionen.php';
        $this->setPostData($post);
        $this->setData($files['data']['tmp_name'] ?? null);
        if ($unzip !== true) {
            return self::OK;
        }
        $this->files     = $this->getFiles($files);
        $this->unzipPath = $this->fileHandler->getUnzipPath();
        if ($this->files === null) {
            return self::ERROR_UNZIP;
        }

        return self::OK;
    }

    /**
     * @param bool $string
     * @return ($string is true ? Generator<array<string, string>> : Generator<array<string, array>>)
     */
    public function getXML(bool $string = false): Generator
    {
        foreach ($this->files as $xmlFile) {
            if (!\str_contains($xmlFile, '.xml')) {
                continue;
            }
            $data = \file_get_contents($xmlFile) ?: '';

            yield [$xmlFile => $string ? \simplexml_load_string($data) : XML::unserialize($data)];
        }
    }

    public function setVersionByUserAgent(): void
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $matches   = [];
        if ($useragent !== null) {
            \preg_match('/JTL-Wawi\/(\d+(\.\d+)+)/', $useragent, $matches);
            if (\count($matches) > 0 && isset($matches[1])) {
                $this->setWawiVersion($matches[1]);
            }
        }
    }

    /**
     * @return string
     */
    public function getWawiVersion(): string
    {
        return $this->wawiVersion;
    }

    /**
     * @param string $wawiVersion
     */
    public function setWawiVersion(string $wawiVersion): void
    {
        $this->wawiVersion = $wawiVersion;
    }
}
