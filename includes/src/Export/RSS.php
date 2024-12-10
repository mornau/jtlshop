<?php

declare(strict_types=1);

namespace JTL\Export;

use DateTime;
use JTL\Customer\CustomerGroup;
use JTL\DB\DbInterface;
use JTL\Helpers\Text;
use JTL\Helpers\URL;
use JTL\Language\LanguageHelper;
use JTL\Shop;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Class RSS
 * @package JTL\Export
 */
class RSS
{
    private string $shopURL;

    /**
     * @var array<string, string>
     */
    private array $conf;

    private int $days;

    /**
     * @param DbInterface     $db
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly DbInterface $db, private readonly LoggerInterface $logger)
    {
        $this->shopURL = Shop::getURL();
        $this->conf    = Shop::getSettingSection(\CONF_RSS);
        $days          = (int)$this->conf['rss_alterTage'];
        if (!$days) {
            $days = 14;
        }
        $this->days = $days;
    }

    /**
     * @return bool
     * @former generiereRSSXML()
     */
    public function generateXML(): bool
    {
        if ($this->conf['rss_nutzen'] !== 'Y') {
            return false;
        }
        if (!\is_writable(\PFAD_ROOT . \FILE_RSS_FEED)) {
            $this->logger->error('RSS Verzeichnis {dir} nicht beschreibbar!', ['dir' => \PFAD_ROOT . \FILE_RSS_FEED]);

            return false;
        }
        $this->logger->debug('RSS wird erstellt');

        $language = LanguageHelper::getDefaultLanguage();

        $_SESSION['kSprache']    = $language->getId();
        $_SESSION['cISOSprache'] = $language->getCode();
        // ISO-8859-1
        $xml = $this->getXmlHead(Text::convertISO2ISO639($language->getCode()));
        $xml .= $this->getProductXML();
        $xml .= $this->getNewsXML();
        $xml .= $this->getReviewXML();
        $xml .= '
                </channel>
            </rss>
        ';

        $file = \fopen(\PFAD_ROOT . \FILE_RSS_FEED, 'wb+');
        if ($file === false) {
            return false;
        }
        \fwrite($file, $xml);
        \fclose($file);

        return true;
    }

    /**
     * @param string $dateString
     * @return bool|string
     * @former bauerfc2822datum()
     */
    public function asRFC2822(string $dateString): bool|string
    {
        return \mb_strlen($dateString) > 0
            ? (new DateTime($dateString))->format(\DATE_RSS)
            : false;
    }

    /**
     * @param string $text
     * @return string
     * @former wandelXMLEntitiesUm()
     */
    public function asEntity(string $text): string
    {
        return \mb_strlen($text) > 0
            ? '<![CDATA[ ' . Text::htmlentitydecode($text) . ' ]]>'
            : '';
    }

    private function getProductXML(): string
    {
        if ($this->conf['rss_artikel_beachten'] !== 'Y') {
            return '';
        }
        $customerGroup = CustomerGroup::getDefault($this->db);
        $xml           = '';
        foreach ($this->getProductData($customerGroup->kKundengruppe ?? 0) as $product) {
            $url = URL::buildURL($product, \URLART_ARTIKEL, true, $this->shopURL . '/');
            $xml .= '
                <item>
                    <title>' . $this->asEntity($product->cName) . '</title>
                    <description>' . $this->asEntity($product->cKurzBeschreibung) . '</description>
                    <link>' . $url . '</link>
                    <guid>' . $url . '</guid>
                    <pubDate>' . $this->asRFC2822($product->dLetzteAktualisierung) . '</pubDate>
                </item>';
        }

        return $xml;
    }

    private function getReviewXML(): string
    {
        if ($this->conf['rss_bewertungen_beachten'] !== 'Y') {
            return '';
        }
        $xml = '';
        foreach ($this->getReviews() as $review) {
            $url = URL::buildURL($review, \URLART_ARTIKEL, true, $this->shopURL . '/');
            $xml .= '
                <item>
                    <title>Bewertung ' . $this->asEntity($review->cTitel) . ' von '
                . $this->asEntity($review->cName) . '</title>
                    <description>' . $this->asEntity($review->cText) . '</description>
                    <link>' . $url . '</link>
                    <guid>' . $url . '</guid>
                    <pubDate>' . $this->asRFC2822($review->dDatum) . '</pubDate>
                </item>';
        }

        return $xml;
    }

    /**
     * @param int $customerGroupID
     * @return stdClass[]
     */
    private function getProductData(int $customerGroupID): array
    {
        return $this->db->getObjects(
            "SELECT tartikel.kArtikel, tartikel.cName, tartikel.cKurzBeschreibung, tseo.cSeo, 
                tartikel.dLetzteAktualisierung, tartikel.dErstellt, 
                DATE_FORMAT(tartikel.dErstellt, '%a, %d %b %Y %H:%i:%s UTC') AS erstellt
                    FROM tartikel
                    LEFT JOIN tartikelsichtbarkeit 
                        ON tartikel.kArtikel = tartikelsichtbarkeit.kArtikel
                        AND tartikelsichtbarkeit.kKundengruppe = :cgid
                    LEFT JOIN tseo 
                        ON tseo.cKey = 'kArtikel'
                        AND tseo.kKey = tartikel.kArtikel
                        AND tseo.kSprache = :lid
                    WHERE tartikelsichtbarkeit.kArtikel IS NULL
                        AND tartikel.cNeu = 'Y' " . Shop::getProductFilter()->getFilterSQL()->getStockFilterSQL() . "
                        AND cNeu = 'Y' 
                        AND DATE_SUB(now(), INTERVAL :ds DAY) < dErstellt
                    ORDER BY dLetzteAktualisierung DESC",
            ['lid' => $_SESSION['kSprache'], 'cgid' => $customerGroupID, 'ds' => $this->days]
        );
    }

    /**
     * @return stdClass[]
     */
    private function getNewsData(): array
    {
        return $this->db->getObjects(
            "SELECT tnews.*, t.title, t.preview, 
                DATE_FORMAT(dGueltigVon, '%a, %d %b %Y %H:%i:%s UTC') AS dErstellt_RSS
                    FROM tnews
                    JOIN tnewssprache t 
                        ON tnews.kNews = t.kNews
                    WHERE DATE_SUB(now(), INTERVAL :ds DAY) < dGueltigVon
                        AND nAktiv = 1
                        AND dGueltigVon <= now()
                    ORDER BY dGueltigVon DESC",
            ['ds' => $this->days]
        );
    }

    /**
     * @return stdClass[]
     */
    private function getReviews(): array
    {
        return $this->db->getObjects(
            "SELECT *, dDatum, DATE_FORMAT(dDatum, '%a, %d %b %y %h:%i:%s +0100') AS dErstellt_RSS
                    FROM tbewertung
                    WHERE DATE_SUB(NOW(), INTERVAL :ds DAY) < dDatum
                        AND nAktiv = 1",
            ['ds' => $this->days]
        );
    }

    /**
     * @param string $language
     * @return string
     */
    private function getXmlHead(string $language): string
    {
        return '<?xml version="1.0" encoding="' . \JTL_CHARSET . '"?>
                <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
                    <channel>
                        <title>' . $this->conf['rss_titel'] . '</title>
                        <link>' . $this->shopURL . '</link>
                        <description>' . $this->conf['rss_description'] . '</description>
                        <language>' . $language . '</language>
                        <copyright>' . $this->conf['rss_copyright'] . '</copyright>
                        <pubDate>' . \date('r') . '</pubDate>
                        <atom:link href="' . $this->shopURL . '/rss.xml" rel="self" type="application/rss+xml" />
                        <image>
                            <url>' . $this->conf['rss_logoURL'] . '</url>
                            <title>' . $this->conf['rss_titel'] . '</title>
                            <link>' . $this->shopURL . '</link>
                        </image>';
    }

    /**
     * @return string
     */
    public function getNewsXML(): string
    {
        if ($this->conf['rss_news_beachten'] !== 'Y') {
            return '';
        }
        $xml = '';
        foreach ($this->getNewsData() as $item) {
            $url = URL::buildURL($item, \URLART_NEWS, true, $this->shopURL . '/');
            $xml .= '
                <item>
                    <title>' . $this->asEntity($item->title) . '</title>
                    <description>' . $this->asEntity($item->preview) . '</description>
                    <link>' . $url . '</link>
                    <guid>' . $url . '</guid>
                    <pubDate>' . $this->asRFC2822($item->dGueltigVon) . '</pubDate>
                </item>';
        }

        return $xml;
    }
}
