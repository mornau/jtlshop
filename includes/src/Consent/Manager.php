<?php

declare(strict_types=1);

namespace JTL\Consent;

use Illuminate\Support\Collection;
use JTL\Cache\JTLCacheInterface;
use JTL\Consent\Statistics\Services\ConsentStatisticsService;
use JTL\DB\DbInterface;
use JTL\Session\Frontend;

/**
 * Class Manager
 * @package JTL\Consent
 */
class Manager implements ManagerInterface
{
    /**
     * @var array{int, Collection<ItemInterface>}|array{}
     */
    private array $activeItems = [];
    private ConsentStatisticsService $consentStatisticsService;

    /**
     * Manager constructor.
     *
     * @param DbInterface                   $db
     * @param JTLCacheInterface             $cache
     * @param ConsentStatisticsService|null $consentStatisticsService
     */
    public function __construct(
        private readonly DbInterface $db,
        private readonly JTLCacheInterface $cache,
        ?ConsentStatisticsService $consentStatisticsService = null,
    ) {
        $this->consentStatisticsService = $consentStatisticsService ?? new ConsentStatisticsService();
    }

    /**
     * @inheritdoc
     */
    public function getConsents(): array
    {
        return Frontend::get('consents') ?? [];
    }

    /**
     * @inheritdoc
     */
    public function itemRevokeConsent(ItemInterface $item): void
    {
        $consents                     = $this->getConsents();
        $consents[$item->getItemID()] = false;
        Frontend::set('consents', $consents);
    }

    /**
     * @inheritdoc
     */
    public function itemGiveConsent(ItemInterface $item): void
    {
        $consents                     = $this->getConsents();
        $consents[$item->getItemID()] = true;
        Frontend::set('consents', $consents);
    }

    /**
     * @inheritdoc
     */
    public function itemHasConsent(ItemInterface $item): bool
    {
        return $this->hasConsent($item->getItemID());
    }

    /**
     * @inheritdoc
     */
    public function hasConsent(string $itemID): bool
    {
        return (($this->getConsents())[$itemID]) ?? false;
    }

    /**
     * @inheritdoc
     */
    public function save(array|string $data): array
    {
        if (!\is_array($data)) {
            return [];
        }

        $consents = [];
        foreach ($data as $item => $value) {
            if (!\is_string($item) || !\in_array($value, ['true', 'false'], true)) {
                continue;
            }
            $consents[$item] = $value === 'true';
        }

        $previouslySavedConsents = Frontend::get('consents');
        $visitorID               = Frontend::get('oBesucher')->kBesucher ?? null;
        if ($visitorID === null) {
            return Frontend::get('consents') ?? [];
        }

        // Use previously saved consents to check if any consent item has changed and save changes into DB
        $consentStatistics                 = $consents;
        $consentStatistics['accepted_all'] = 0;
        if ($previouslySavedConsents === null) {
            if ($this->consentStatisticsService->hasAcceptedAll($consents)) {
                $consentStatistics['accepted_all'] = 1;
            }

            $this->consentStatisticsService->saveConsentValues(
                visitorID: $visitorID,
                consents: $consentStatistics
            );
        } elseif (\count($consentStatistics) > 0) {
            if ($this->consentStatisticsService->hasAcceptedAll($consentStatistics)) {
                $consentStatistics['accepted_all'] = 1;
            }

            $this->consentStatisticsService->saveConsentValues(
                visitorID: $visitorID,
                consents: $consentStatistics,
                date: Frontend::get('oBesucher')->dZeit ?? null
            );
        }

        Frontend::set('consents', $consents);

        return $consents;
    }

    /**
     * @inheritdoc
     */
    public function initActiveItems(int $languageID): Collection
    {
        $cached  = true;
        $cacheID = 'jtl_consent_models_' . $languageID;
        /** @var Collection|false $models */
        $models = $this->cache->get($cacheID);
        if ($models === false) {
            /** @var Collection $models */
            $models = ConsentModel::loadAll($this->db, 'active', 1)
                ->map(static function (ConsentModel $model) use ($languageID): Item {
                    return (new Item($languageID))->loadFromModel($model);
                })
                ->sortBy(static function (Item $item): bool {
                    return $item->getItemID() !== 'necessary';
                });
            $this->cache->set($cacheID, $models, [\CACHING_GROUP_CORE]);
            $cached = false;
        }
        \executeHook(\CONSENT_MANAGER_GET_ACTIVE_ITEMS, ['items' => $models, 'cached' => $cached]);
        $this->activeItems[$languageID] = $models;

        return $models;
    }

    /**
     * @inheritdoc
     */
    public function getActiveItems(int $languageID): Collection
    {
        return $this->activeItems[$languageID] ?? $this->initActiveItems($languageID);
    }
}
