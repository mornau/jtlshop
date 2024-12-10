<?php

declare(strict_types=1);

namespace JTL\Backend\Wizard;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JsonException;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Helpers\Text;
use JTL\License\AjaxResponse;
use JTL\License\Exception\ApiResultCodeException;
use JTL\License\Exception\AuthException;
use JTL\License\Exception\ChecksumValidationException;
use JTL\License\Exception\DownloadValidationException;
use JTL\License\Exception\FilePermissionException;
use JTL\License\Installer\Helper;
use JTL\License\Manager as LicenseManager;
use JTL\License\Struct\Link;
use JTL\Mapper\PluginValidation;
use JTL\Plugin\InstallCode;
use JTL\Recommendation\Recommendation;
use JTL\Shop;

/**
 * Class ExtensionInstaller
 * @package JTL\Backend\Wizard
 */
class ExtensionInstaller
{
    /**
     * @var Helper
     */
    private Helper $helper;

    /**
     * @var Collection<Recommendation>
     */
    private Collection $recommendations;

    /**
     * @var LicenseManager
     */
    private LicenseManager $manager;

    /**
     * @var string
     */
    private string $errorMessage;

    /**
     * ExtensionInstaller constructor.
     * @param DbInterface            $db
     * @param JTLCacheInterface|null $cache
     */
    public function __construct(DbInterface $db, ?JTLCacheInterface $cache = null)
    {
        $cache                 = $cache ?? Shop::Container()->getCache();
        $this->recommendations = new Collection();
        $this->manager         = new LicenseManager($db, $cache);
        $this->helper          = new Helper($this->manager, $db, $cache);
    }

    /**
     * @param string $id
     * @return Recommendation|null
     */
    private function getRecommendationByID(string $id): ?Recommendation
    {
        return $this->recommendations->first(static function (Recommendation $rec) use ($id): bool {
            return $rec->getId() === $id;
        });
    }

    /**
     * @return Collection<Recommendation>
     */
    public function getRecommendations(): Collection
    {
        return $this->recommendations;
    }

    /**
     * @param Collection<Recommendation> $recommendations
     */
    public function setRecommendations(Collection $recommendations): void
    {
        $this->recommendations = $recommendations;
    }

    /**
     * @param string[] $requested
     * @return string
     * @throws ApiResultCodeException
     * @throws ChecksumValidationException
     * @throws DownloadValidationException
     * @throws FilePermissionException
     * @throws GuzzleException
     */
    public function onSaveStep(array $requested): string
    {
        $createdLicenseKeys = [];
        $this->errorMessage = '';
        foreach ($requested as $id) {
            $recom = $this->getRecommendationByID($id);
            if ($recom === null) {
                continue;
            }
            foreach ($recom->getLinks() as $link) {
                if ($link->getRel() !== 'createLicense') {
                    continue;
                }
                $createdLicenseKeys = $this->getCreatedLicenses($link, $createdLicenseKeys, $id, $recom);
            }
        }
        if (!empty($createdLicenseKeys)) {
            $this->installExtensions($createdLicenseKeys);
        }

        return $this->errorMessage;
    }

    /**
     * @param string[] $createdLicenseKeys
     * @throws ApiResultCodeException
     * @throws AuthException
     * @throws ChecksumValidationException
     * @throws DownloadValidationException
     * @throws FilePermissionException
     * @throws GuzzleException
     */
    public function installExtensions(array $createdLicenseKeys): void
    {
        $this->manager->update(true);
        foreach ($createdLicenseKeys as $key) {
            $ajaxResponse = new AjaxResponse();
            $license      = $this->manager->getLicenseByLicenseKey($key);
            if ($license === null) {
                continue;
            }
            $itemID = $license->getID();
            try {
                $this->helper->validatePrerequisites($itemID);
                $installer   = $this->helper->getInstaller($itemID);
                $download    = $this->helper->getDownload($itemID);
                $installCode = $installer->install($itemID, $download, $ajaxResponse);
                if ($installCode === InstallCode::DUPLICATE_PLUGIN_ID) {
                    $download    = $this->helper->getDownload($itemID);
                    $installCode = $installer->forceUpdate($download, $ajaxResponse);
                }
            } catch (InvalidArgumentException $e) {
                $this->errorMessage .= \sprintf('%s: %s <br>', $license->getName(), \__($e->getMessage()));
            }
            if (isset($installCode) && $installCode !== InstallCode::OK) {
                $mapper             = new PluginValidation();
                $this->errorMessage .= \sprintf('%s: %s <br>', $license->getName(), $mapper->map($installCode));
            }
        }
    }

    /**
     * @param Link           $link
     * @param string[]       $keys
     * @param string         $id
     * @param Recommendation $recom
     * @return string[]
     */
    public function getCreatedLicenses(Link $link, array $keys, string $id, Recommendation $recom): array
    {
        try {
            $res = $this->manager->createLicense($link->getHref());
            try {
                $data = \json_decode($res, false, 512, \JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $data = null;
            }
            if (isset($data->meta)) {
                $keys[] = $data->meta->exs_key;
            }
        } catch (ClientException | GuzzleException $e) {
            if (
                $e->getResponse()->getStatusCode() === 400
                && ($license = $this->manager->getLicenseByExsID($id)) !== null
            ) {
                $keys[] = $license->getLicense()->getKey();
            } else {
                $this->errorMessage .= \sprintf(
                    '%s: %s <br>',
                    $recom->getTitle(),
                    Text::htmlentities($e->getMessage())
                );
            }
        } catch (AuthException $e) {
            $this->errorMessage .= $e->getMessage();
        }

        return $keys;
    }
}
