<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\CustomerFields;
use JTL\Backend\Permissions;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\PlausiKundenfeld;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CustomerFieldsController
 * @package JTL\Router\Controller\Backend
 */
class CustomerFieldsController extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::ORDER_CUSTOMERFIELDS_VIEW);
        $this->getText->loadAdminLocale('pages/kundenfeld');
        $this->setLanguage();
        $cf         = CustomerFields::getInstance($this->currentLanguageID, $this->db);
        $step       = 'uebersicht';
        $invalidate = false;
        $smarty->assign('cTab', $step)
            ->assign('route', $this->route);
        if (Request::pInt('einstellungen') > 0) {
            $this->saveAdminSectionSettings(\CONF_KUNDENFELD, $_POST);
        } elseif (Request::pInt('kundenfelder') === 1 && Form::validateToken()) {
            $success = true;
            if (isset($_POST['loeschen'])) {
                $fieldIDs = $_POST['kKundenfeld'];
                if (\is_array($fieldIDs) && \count($fieldIDs) > 0) {
                    foreach ($fieldIDs as $fieldID) {
                        $success = $success && $cf->delete((int)$fieldID);
                    }
                    if ($success) {
                        $this->alertService->addSuccess(
                            \__('successCustomerFieldDelete'),
                            'successCustomerFieldDelete'
                        );
                    } else {
                        $this->alertService->addError(\__('errorCustomerFieldDelete'), 'errorCustomerFieldDelete');
                    }
                    $invalidate = true;
                } else {
                    $this->alertService->addError(\__('errorAtLeastOneCustomerField'), 'errorAtLeastOneCustomerField');
                }
            } elseif (isset($_POST['aktualisieren'])) {
                foreach ($cf->getCustomerFields() as $customerField) {
                    $customerField->nSort = (int)$_POST['nSort_' . $customerField->kKundenfeld];
                    $success              = $success && $cf->save($customerField);
                }
                if ($success) {
                    $this->alertService->addSuccess(\__('successCustomerFieldUpdate'), 'successCustomerFieldUpdate');
                } else {
                    $this->alertService->addError(\__('errorCustomerFieldUpdate'), 'errorCustomerFieldUpdate');
                }
                $invalidate = true;
            } else { // Speichern
                $customerField = (object)[
                    'kKundenfeld' => (int)($_POST['kKundenfeld'] ?? 0),
                    'kSprache'    => $this->currentLanguageID,
                    'cName'       => Text::htmlspecialchars(
                        Text::filterXSS($_POST['cName']),
                        \ENT_COMPAT | \ENT_HTML401
                    ),
                    'cWawi'       => Text::filterXSS(\str_replace(['"', "'"], '', $_POST['cWawi'])),
                    'cTyp'        => Text::filterXSS($_POST['cTyp']),
                    'nSort'       => Request::pInt('nSort'),
                    'nPflicht'    => Request::pInt('nPflicht'),
                    'nEditierbar' => Request::pInt('nEdit'),
                ];
                $invalidate    = true;
                $cfValues      = $_POST['cfValues'] ?? null;
                $check         = new PlausiKundenfeld();
                $check->setPostVar($_POST);
                $check->doPlausi($customerField->cTyp, $customerField->kKundenfeld > 0);

                if (\count($check->getPlausiVar()) === 0) {
                    if ($cf->save($customerField, $cfValues)) {
                        $this->alertService->addSuccess(\__('successCustomerFieldSave'), 'successCustomerFieldSave');
                    } else {
                        $this->alertService->addError(\__('errorCustomerFieldSave'), 'errorCustomerFieldSave');
                    }
                } else {
                    $erroneousFields = $check->getPlausiVar();
                    if (isset($erroneousFields['cName']) && $erroneousFields['cName'] === 2) {
                        $this->alertService->addError(
                            \__('errorCustomerFieldNameExists'),
                            'errorCustomerFieldNameExists'
                        );
                    } else {
                        $this->alertService->addError(\__('errorFillRequired'), 'errorFillRequired');
                    }
                    $smarty->assign('xPlausiVar_arr', $check->getPlausiVar())
                        ->assign('xPostVar_arr', $check->getPostVar())
                        ->assign('kKundenfeld', $customerField->kKundenfeld);
                }
            }
        } elseif (Request::verifyGPDataString('a') === 'edit') {
            $fieldID = Request::verifyGPCDataInt('kKundenfeld');
            if ($fieldID > 0) {
                $customerField = $cf->getCustomerField($fieldID);
                if ($customerField !== null) {
                    $customerField->oKundenfeldWert_arr = $cf->getCustomerFieldValues($customerField);
                    $smarty->assign('oKundenfeld', $customerField);
                }
            }
        }
        $fields = $cf->getCustomerFields();
        foreach ($fields as $field) {
            if ($field->cTyp === 'auswahl') {
                $field->oKundenfeldWert_arr = $cf->getCustomerFieldValues($field);
            }
        }
        if ($invalidate === true) {
            $this->cache->flushTags([\CACHING_GROUP_OBJECT]);
        }
        // calculate the highest sort-order number (based on the 'ORDER BY' above)
        // to recommend the user the next sort-order-value, instead of a placeholder
        $lastElement      = \end($fields);
        $lastSort         = $lastElement !== false ? $lastElement->nSort : 0;
        $highestSortValue = $lastSort;
        $preLastElement   = \prev($fields);
        if ($preLastElement === false) {
            $highestSortDiff = ($lastSort === 0) ? 1 : $lastSort;
        } else {
            $highestSortDiff = $lastSort - $preLastElement->nSort;
        }
        \reset($fields); // we leave the array in a safe state
        $this->getAdminSectionSettings(\CONF_KUNDENFELD);

        return $smarty->assign('oKundenfeld_arr', $fields)
            ->assign('nHighestSortValue', $highestSortValue)
            ->assign('nHighestSortDiff', $highestSortDiff)
            ->assign('step', $step)
            ->getResponse('kundenfeld.tpl');
    }
}
