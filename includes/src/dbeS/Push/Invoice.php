<?php

declare(strict_types=1);

namespace JTL\dbeS\Push;

use JTL\Helpers\Request;
use JTL\Plugin\Payment\LegacyMethod;
use JTL\Plugin\Payment\MethodInterface;

/**
 * Class Invoice
 * @package JTL\dbeS\Push
 */
final class Invoice extends AbstractPush
{
    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (!isset($_POST['kBestellung'], $_POST['kSprache'])) {
            return [];
        }
        $orderID = Request::pInt('kBestellung');
        $langID  = Request::pInt('kSprache');
        if ($orderID <= 0 || $langID <= 0) {
            return $this->pushError('Wrong params (kBestellung: ' . $orderID . ', kSprache: ' . $langID . ').');
        }
        $paymentMethod = $this->getPaymentMethodByOrderID($orderID);
        if ($paymentMethod === null) {
            return $this->pushError('Keine Bestellung mit kBestellung ' . $orderID . ' gefunden!');
        }
        $invoice = $paymentMethod->createInvoice($orderID, $langID);
        if (!isset($invoice->nType)) {
            return $this->pushError('Fehler beim Erstellen der Rechnung (kBestellung: ' . $orderID . ').');
        }
        if ($invoice->nType === 0 && \strlen($invoice->cInfo) === 0) {
            $invoice->cInfo = 'Funktion in Zahlungsmethode nicht implementiert';
        }

        return $this->createResponse(
            $orderID,
            ($invoice->nType === 0 ? 'FAILURE' : 'SUCCESS'),
            $invoice->cInfo
        );
    }

    /**
     * @param int $id
     * @return MethodInterface|null
     */
    private function getPaymentMethodByOrderID(int $id): ?MethodInterface
    {
        $order = $this->db->getSingleObject(
            'SELECT tbestellung.kBestellung, tbestellung.fGesamtsumme, tzahlungsart.cModulId
                FROM tbestellung
                LEFT JOIN tzahlungsart
                  ON tbestellung.kZahlungsart = tzahlungsart.kZahlungsart
                WHERE tbestellung.kBestellung = :oid 
                LIMIT 1',
            ['oid' => $id]
        );
        if ($order === null) {
            return null;
        }

        return LegacyMethod::create($order->cModulId);
    }

    /**
     * @param int    $orderID
     * @param string $type
     * @param string $comment
     * @return array<string, array<string, int|string>>
     */
    private function createResponse(int $orderID, string $type, string $comment): array
    {
        $res                               = ['tbestellung' => []];
        $res['tbestellung']['kBestellung'] = $orderID;
        $res['tbestellung']['cTyp']        = $type;
        $res['tbestellung']['cKommentar']  = \html_entity_decode(
            $comment,
            \ENT_COMPAT | \ENT_HTML401,
            'ISO-8859-1'
        ); // decode entities for jtl-wawi.
        // Entities are html-encoded since
        // https://gitlab.jtl-software.de/jtlshop/jtl-shop/commit/e81f7a93797d8e57d00a1705cc5f13191eee9ca1

        return $res;
    }

    /**
     * @param string $message
     * @return array<string, array<string, int|string>>
     */
    private function pushError(string $message): array
    {
        $this->logger->error('Error @ invoice_xml: {msg}', ['msg' => $message]);

        return $this->createResponse(0, 'FAILURE', $message);
    }
}
