<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\Checkout\Bestellung;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Pagination\Pagination;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class OrderController
 * @package JTL\Router\Controller\Backend
 */
class OrderController extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->getText->loadAdminLocale('pages/bestellungen');
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::ORDER_VIEW);

        $searchFilter = '';
        if (Request::verifyGPCDataInt('zuruecksetzen') === 1 && Form::validateToken()) {
            if (isset($_POST['kBestellung']) && $this->resetSyncStatus($_POST['kBestellung'])) {
                $this->alertService->addSuccess(\__('successOrderReset'), 'successOrderReset');
            } else {
                $this->alertService->addError(\__('errorAtLeastOneOrder'), 'errorAtLeastOneOrder');
            }
        } elseif (Request::verifyGPCDataInt('Suche') === 1 && Form::validateToken()) {
            $query = Text::filterXSS(Request::verifyGPDataString('cSuche'));
            if (\mb_strlen($query) > 0) {
                $searchFilter = $query;
            } else {
                $this->alertService->addError(\__('errorMissingOrderNumber'), 'errorMissingOrderNumber');
            }
        }

        $pagination = (new Pagination('bestellungen'))
            ->setItemCount($this->getOrderCount($searchFilter))
            ->assemble();

        return $smarty->assign('step', 'bestellungen_uebersicht')
            ->assign('orders', $this->getOrders(' LIMIT ' . $pagination->getLimitSQL(), $searchFilter))
            ->assign('pagination', $pagination)
            ->assign('cSuche', $searchFilter)
            ->assign('route', $this->route)
            ->getResponse('bestellungen.tpl');
    }

    /**
     * @param string $limitSQL
     * @param string $query
     * @return Bestellung[]
     * @former gibBestellungsUebersicht()
     */
    public function getOrders(string $limitSQL, string $query): array
    {
        $prep         = [];
        $searchFilter = '';
        if (\mb_strlen($query) > 0) {
            $searchFilter = ' WHERE cBestellNr LIKE :fltr';
            $prep['fltr'] = '%' . $query . '%';
        }
        $items = $this->db->getInts(
            'SELECT kBestellung
                FROM tbestellung
                ' . $searchFilter . '
                ORDER BY dErstellt DESC' . $limitSQL,
            'kBestellung',
            $prep
        );

        return $this->getArrayOfOrders($items);
    }

    /**
     * @param string $limitSQL
     * @return Bestellung[]
     */
    public function getOrdersWithoutCancellations(string $limitSQL): array
    {
        $prep  = [];
        $items = $this->db->getInts(
            'SELECT kBestellung
                FROM tbestellung
                WHERE cStatus > -1
                ORDER BY dErstellt DESC' . $limitSQL,
            'kBestellung',
            $prep
        );

        return $this->getArrayOfOrders($items);
    }

    /**
     * @param string $query
     * @return int
     * @former gibAnzahlBestellungen()
     */
    private function getOrderCount(string $query): int
    {
        $prep         = [];
        $searchFilter = '';
        if (\mb_strlen($query) > 0) {
            $searchFilter = ' WHERE cBestellNr LIKE :fltr';
            $prep['fltr'] = '%' . $query . '%';
        }

        return $this->db->getSingleInt(
            'SELECT COUNT(*) AS cnt
                FROM tbestellung' . $searchFilter,
            'cnt',
            $prep
        );
    }

    /**
     * @param int[]|numeric-string[] $orderIDs
     * @return bool
     * @former setzeAbgeholtZurueck()
     */
    private function resetSyncStatus(array $orderIDs): bool
    {
        if (\count($orderIDs) === 0) {
            return false;
        }
        $orderList = \implode(',', \array_map('\intval', $orderIDs));
        /** @var int[] $customers */
        $customers = $this->db->getCollection(
            'SELECT kKunde
                FROM tbestellung
                WHERE kBestellung IN (' . $orderList . ")
                    AND cAbgeholt = 'Y'"
        )->pluck('kKunde')->map(static function (string $item): int {
            return (int)$item;
        })->unique()->toArray();
        if (\count($customers) > 0) {
            $this->db->query(
                "UPDATE tkunde
                    SET cAbgeholt = 'N'
                    WHERE kKunde IN (" . \implode(',', $customers) . ')'
            );
        }
        $this->db->query(
            "UPDATE tbestellung
                SET cAbgeholt = 'N'
                WHERE kBestellung IN (" . $orderList . ")
                    AND cAbgeholt = 'Y'"
        );
        $this->db->query(
            "UPDATE tzahlungsinfo
                SET cAbgeholt = 'N'
                WHERE kBestellung IN (" . $orderList . ")
                    AND cAbgeholt = 'Y'"
        );

        return true;
    }

    /**
     * @param int[] $items
     * @return Bestellung[]
     */
    private function getArrayOfOrders(array $items): array
    {
        $orders = [];
        foreach ($items as $orderID) {
            if ($orderID <= 0) {
                continue;
            }
            $order = new Bestellung($orderID, false, $this->db);
            $order->fuelleBestellung(true, 0, false);
            $orders[] = $order;
        }

        return $orders;
    }
}
