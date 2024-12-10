<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\CheckBox;
use JTL\Checkbox\CheckboxDomainObject;
use JTL\Customer\CustomerGroup;
use JTL\Exceptions\PermissionException;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Language\LanguageHelper;
use JTL\Language\LanguageModel;
use JTL\Pagination\Pagination;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CheckboxController
 * @package JTL\Router\Controller\Backend
 */
class CheckboxController extends AbstractBackendController
{
    /**
     * @var array<string, array<string, string|bool|int>>
     */
    private array $internalBoxDefaults = [
        CheckBox::CHECKBOX_DOWNLOAD_ORDER_COMPLETE => [
            'nInternal'         => true,
            'cAnzeigeOrt'       => ';2;',
            'nPflicht'          => true,
            'nLink'             => false,
            'nAktiv'            => true,
            'nLogging'          => true,
            'kCheckBoxFunktion' => 0,
        ],
    ];

    /**
     * @inheritdoc
     * @throws PermissionException
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::CHECKBOXES_VIEW);
        $this->getText->loadAdminLocale('pages/checkbox');

        $step     = 'uebersicht';
        $checkbox = new CheckBox(0, $this->db);
        $tab      = $step;
        if (\mb_strlen(Request::verifyGPDataString('tab')) > 0) {
            $tab = Request::verifyGPDataString('tab');
        }
        if (isset($_POST['erstellenShowButton'])) {
            $tab = 'erstellen';
        } elseif (Request::verifyGPCDataInt('uebersicht') === 1 && Form::validateToken()) {
            $checkboxIDs = Request::verifyGPDataIntegerArray('kCheckBox');
            if (isset($_POST['checkboxAktivierenSubmit'])) {
                $checkbox->activate($checkboxIDs);
                $this->alertService->addSuccess(\__('successCheckboxActivate'), 'successCheckboxActivate');
            } elseif (isset($_POST['checkboxDeaktivierenSubmit'])) {
                $checkbox->deactivate($checkboxIDs);
                $this->alertService->addSuccess(\__('successCheckboxDeactivate'), 'successCheckboxDeactivate');
            } elseif (isset($_POST['checkboxLoeschenSubmit'])) {
                $checkbox->delete($checkboxIDs);
                $this->alertService->addSuccess(\__('successCheckboxDelete'), 'successCheckboxDelete');
            }
        } elseif (Request::verifyGPCDataInt('edit') > 0) {
            $checkboxID = Request::verifyGPCDataInt('edit');
            $step       = 'erstellen';
            $tab        = $step;
            $smarty->assign('oCheckBox', new CheckBox($checkboxID, $this->db));
        } elseif (Request::verifyGPCDataInt('erstellen') === 1 && Form::validateToken()) {
            $post        = Text::filterXSS($_POST);
            $step        = 'erstellen';
            $checkboxID  = Request::verifyGPCDataInt('kCheckBox');
            $languages   = LanguageHelper::getAllLanguages(0, true, true);
            $checkboxDTO = $checkbox->getCheckboxDomainObject(
                \array_merge(
                    $post,
                    (isset($this->internalBoxDefaults[$post['cName']])
                        && \is_array($this->internalBoxDefaults[$post['cName']]))
                        ? $this->internalBoxDefaults[$post['cName']] : []
                ),
                $languages
            );
            $checks      = $this->validate($checkboxDTO, $languages);
            if (\count($checks) === 0) {
                $checkbox = $this->save($checkboxDTO);
                $step     = 'uebersicht';
                $this->alertService->addSuccess(\__('successCheckboxCreate'), 'successCheckboxCreate');
            } else {
                $this->handleMissingFields($post, $checks, $checkboxID);
            }
            $tab = $step;
        }

        $pagination = (new Pagination())
            ->setItemCount($checkbox->getTotalCount())
            ->assemble();

        return $smarty->assign('oCheckBox_arr', $checkbox->getAll('LIMIT ' . $pagination->getLimitSQL()))
            ->assign('pagination', $pagination)
            ->assign('cAnzeigeOrt_arr', CheckBox::gibCheckBoxAnzeigeOrte())
            ->assign('customerGroups', CustomerGroup::getGroups())
            ->assign(
                'oLink_arr',
                $this->db->getObjects(
                    'SELECT * 
                         FROM tlink 
                         ORDER BY cName'
                )
            )
            ->assign('oCheckBoxFunktion_arr', $checkbox->getCheckBoxFunctions())
            ->assign('step', $step)
            ->assign('cTab', $tab)
            ->assign('route', $this->route)
            ->getResponse('checkbox.tpl');
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @param LanguageModel[]      $languages
     * @return array<string, int>
     * @former plausiCheckBox()
     */
    private function validate(CheckboxDomainObject $checkboxDO, array $languages): array
    {
        $checks = [];
        if (\count($languages) === 0) {
            $checks['oSprache_arr'] = 1;

            return $checks;
        }
        if (\mb_strlen($checkboxDO->getName()) === 0) {
            $checks['cName'] = 1;
        }
        $text = false;
        $link = true;
        foreach ($languages as $language) {
            if (\mb_strlen($checkboxDO->getLanguages()[$language->getIso()]['text']) > 0) {
                $text = true;
                break;
            }
        }
        if (!$text) {
            $checks['cText'] = 1;
        }
        if ($checkboxDO->getHasLink() === true) {
            $link = $checkboxDO->getLinkID() > 0;
        }
        if ($link === false) {
            $checks['kLink'] = 1;
        }
        if (\mb_strlen($checkboxDO->getDisplayAt()) === 0) {
            $checks['cAnzeigeOrt'] = 1;
        } else {
            foreach (\explode(';', $checkboxDO->getDisplayAt()) as $item) {
                if ((int)$item === 3 && $checkboxDO->getCheckboxFunctionID() === 1) {
                    $checks['cAnzeigeOrt'] = 2;
                }
            }
        }
        if ($checkboxDO->getSort() === 0) {
            $checks['nSort'] = 1;
        }
        if (\mb_strlen($checkboxDO->getCustomerGroupsSelected()) === 0) {
            $checks['cKundengruppe'] = 1;
        }

        return $checks;
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @return CheckBox
     * @former speicherCheckBox()
     */
    private function save(CheckboxDomainObject $checkboxDO): CheckBox
    {
        return (new CheckBox(0, $this->db))->save($checkboxDO);
    }

    /**
     * @param array              $post
     * @param array<string, int> $checks
     * @param int                $checkboxID
     */
    public function handleMissingFields(array $post, array $checks, int $checkboxID): void
    {
        $this->alertService->addError(\__('errorFillRequired'), 'errorFillRequired');
        $this->getSmarty()->assign('cPost_arr', $post)
            ->assign('cPlausi_arr', $checks);
        if ($checkboxID <= 0) {
            return;
        }
        $customerGroupID = $post['kKundengruppe'] ?? ';;';
        $postBox         = [
            'nInternal'         => $post['nInternal'],
            'kCheckBox'         => $checkboxID,
            'cAnzeigeOrt'       => $post['cAnzeigeOrt'] ?? ';;',
            'cName'             => $post['cName'],
            'nPflicht'          => $post['nPflicht'],
            'nLink'             => $post['nLink'],
            'kLink'             => $post['kLink'],
            'nAktiv'            => $post['nAktiv'],
            'nLogging'          => $post['nLogging'],
            'kCheckBoxFunktion' => $post['kCheckBoxFunktion'],
            'kKundengruppe'     => $customerGroupID,
        ];
        if ((int)$post['nInternal'] === 1) {
            $postBox['cAnzeigeOrt'] = ';' . $post['cAnzeigeOrt'][0] . ';';
        }
        $this->getSmarty()->assign('kCheckBox', $checkboxID)
            ->assign('oCheckBox', (object)$postBox);
    }
}
