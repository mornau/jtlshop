<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\CSV\Export;
use JTL\CSV\RedirectImporter;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Pagination\DataType;
use JTL\Pagination\Filter;
use JTL\Pagination\Operation;
use JTL\Pagination\Pagination;
use JTL\Redirect;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RedirectController
 * @package JTL\Router\Controller\Backend
 */
class RedirectController extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::REDIRECT_VIEW);
        $this->getText->loadAdminLocale('pages/redirect');
        $action = Request::verifyGPDataString('action');
        if (Request::verifyGPDataString('importcsv') === 'redirects') {
            $action = 'csvImport';
        }
        $redirects = $_POST['redirects'] ?? [];
        $filter    = new Filter('redirectFilter');
        $filter->addTextfield(\__('redirectFrom'), 'cFromUrl', Operation::CONTAINS, DataType::TEXT, 'rdfrom');
        $filter->addTextfield(\__('redirectTo'), 'cToUrl', Operation::CONTAINS, DataType::TEXT, 'rdto');
        $select = $filter->addSelectfield(\__('redirection'), 'cToUrl', 0, 'redirect');
        $select->addSelectOption(\__('all'), '');
        $select->addSelectOption(\__('available'), '', Operation::NOT_EQUAL);
        $select->addSelectOption(\__('missing'), '', Operation::EQUALS);
        $type = $filter->addSelectfield(\__('Type'), 'type', 0, 'rdtype');
        $type->addSelectOption(\__('all'), -1);
        $type->addSelectOption(\__('Manual'), Redirect::TYPE_MANUAL, Operation::EQUALS);
        $type->addSelectOption(\__('Import'), Redirect::TYPE_IMPORT, Operation::EQUALS);
        $type->addSelectOption(\__('Wawi sync'), Redirect::TYPE_WAWI, Operation::EQUALS);
        $type->addSelectOption(\__('Unknown'), Redirect::TYPE_UNKNOWN, Operation::EQUALS);
        $filter->addTextfield(\__('calls'), 'nCount', Operation::CUSTOM, DataType::NUMBER, 'rdcount');
        $filter->addDaterangefield(\__('Date created'), 'dateCreated', '', 'rdcreated');
        $filter->assemble();
        $pagination = new Pagination();
        $pagination->setSortByOptions([
            ['cFromUrl', \__('redirectFrom')],
            ['cToUrl', \__('redirectTo')],
            ['nCount', \__('calls')],
            ['type', \__('Type')]
        ]);
        if (Form::validateToken()) {
            switch ($action) {
                case 'csvImport':
                    $this->actionImport();
                    break;
                case 'csvExport':
                    $this->actionExport($filter, $pagination);
                    break;
                case 'save':
                    $this->actionSave($redirects);
                    break;
                case 'delete':
                    foreach ($redirects as $id => $item) {
                        if (isset($item['enabled']) && (int)$item['enabled'] === 1) {
                            Redirect::deleteRedirect((int)$id);
                        }
                    }
                    break;
                case 'delete_all':
                    Redirect::deleteUnassigned();
                    break;
                case 'new':
                    if (Request::pInt('redirect-id') > 0) {
                        $redirect = new Redirect(Request::pInt('redirect-id'), $this->db);
                        $data     = [
                            'kRedirect'     => Request::pInt('redirect-id'),
                            'cToUrl'        => Request::pString('cToUrl'),
                            'cFromUrl'      => Request::pString('cFromUrl'),
                            'paramHandling' => Request::pInt('paramHandling')
                        ];
                        $ok       = $this->updateItem($redirect, $data);
                        if ($ok === true) {
                            $this->alertService->addSuccess(\__('successRedirectSave'), 'successRedirectSave');
                        } else {
                            $this->alertService->addError(
                                \sprintf(\__('errorURLNotReachable'), $data['cToUrl']),
                                'errorURLNotReachable'
                            );
                        }
                    } else {
                        $this->actionCreate();
                    }
                    break;
                case 'edit':
                    $this->actionEdit(Request::gInt('id'));
                    break;
                default:
                    break;
            }
        }
        $redirectCount = Redirect::getRedirectCount($filter->getWhereSQL());
        $pagination->setItemCount($redirectCount)->assemble();
        $list = Redirect::getRedirects(
            $filter->getWhereSQL(),
            $pagination->getOrderSQL(),
            $pagination->getLimitSQL()
        );

        return $smarty->assign('oFilter', $filter)
            ->assign('pagination', $pagination)
            ->assign('route', $this->route)
            ->assign('redirects', $list)
            ->assign('totalRedirectCount', Redirect::getRedirectCount())
            ->getResponse('redirect.tpl');
    }

    /**
     * @param Filter     $filter
     * @param Pagination $pagination
     * @return void
     */
    private function actionExport(Filter $filter, Pagination $pagination): void
    {
        $redirectCount = Redirect::getRedirectCount($filter->getWhereSQL());
        $pagination->setItemCount($redirectCount)->assemble();
        $export = new Export();
        $export->export(
            'redirects',
            'redirects.csv',
            function () use ($filter, $pagination, $redirectCount) {
                $where = $filter->getWhereSQL();
                $order = $pagination->getOrderSQL();
                for ($i = 0; $i < $redirectCount; $i += 1000) {
                    $iter = $this->db->getPDOStatement(
                        'SELECT cFromUrl, cToUrl
                            FROM tredirect'
                        . ($where !== '' ? ' WHERE ' . $where : '')
                        . ($order !== '' ? ' ORDER BY ' . $order : '')
                        . ' LIMIT ' . $i . ', 1000'
                    );

                    foreach ($iter as $oRedirect) {
                        yield (object)$oRedirect;
                    }
                }
            }
        );
    }

    private function actionImport(): void
    {
        $importer = new RedirectImporter($this->db);
        $result   = $importer->runImport();

        if ($result === false) {
            $errors = $importer->getErrors();
            $this->alertService->addError(\__('errorImport') . '<br><br>' . \implode('<br>', $errors), 'errorImport');
        } else {
            $this->alertService->addSuccess(\__('successImport'), 'successImport');
        }
    }

    /**
     * @param array<int|string, array{cToUrl: string}> $redirects
     * @return void
     */
    private function actionSave(array $redirects): void
    {
        foreach ($redirects as $id => $item) {
            $redirect = new Redirect((int)$id, $this->db);
            if ($redirect->kRedirect <= 0 || $redirect->cToUrl === $item['cToUrl']) {
                continue;
            }
            if (!$this->updateItem($redirect, $item)) {
                $this->alertService->addError(
                    \sprintf(\__('errorURLNotReachable'), $item['cToUrl']),
                    'errorURLNotReachable'
                );
            }
        }
    }

    /**
     * @param Redirect                                                                       $redirect
     * @param array{cToUrl: string, paramHandling?: int, kRedirect?: int, cFromUrl?: string} $item
     * @return bool
     */
    private function updateItem(Redirect $redirect, array $item): bool
    {
        if ($redirect->kRedirect === null || !Redirect::checkAvailability($item['cToUrl'])) {
            return false;
        }
        $redirect->cFromUrl   = $item['cFromUrl'];
        $redirect->cToUrl     = $item['cToUrl'];
        $redirect->cAvailable = 'y';
        if (isset($item['paramHandling'])) {
            $redirect->paramHandling = $item['paramHandling'];
        }
        $this->db->update('tredirect', 'kRedirect', $redirect->kRedirect, $redirect);

        return true;
    }

    /**
     * @param int $id
     * @return void
     */
    private function actionEdit(int $id): void
    {
        $redirect = new Redirect($id, $this->db);
        $this->getSmarty()->assign('cTab', 'new_redirect')
            ->assign('cFromUrl', $redirect->cFromUrl)
            ->assign('cToUrl', $redirect->cToUrl)
            ->assign('redirectID', $redirect->kRedirect)
            ->assign('cAvailable', $redirect->cAvailable)
            ->assign('nCount', $redirect->nCount)
            ->assign('paramHandling', $redirect->paramHandling)
            ->assign('cFromUrl', $redirect->cFromUrl);
    }

    /**
     * @return void
     */
    private function actionCreate(): void
    {
        $redirect = new Redirect(0, $this->db);
        if (
            $redirect->saveExt(
                Request::verifyGPDataString('cFromUrl'),
                Request::verifyGPDataString('cToUrl'),
                false,
                Request::verifyGPCDataInt('paramHandling'),
                false,
                Redirect::TYPE_MANUAL
            )
        ) {
            $this->alertService->addSuccess(\__('successRedirectSave'), 'successRedirectSave');
        } else {
            $this->alertService->addError(\__('errorCheckInput'), 'errorCheckInput');
            $this->getSmarty()->assign('cTab', 'new_redirect')
                ->assign('cFromUrl', Text::filterXSS(Request::verifyGPDataString('cFromUrl')))
                ->assign('cToUrl', Text::filterXSS(Request::verifyGPDataString('cToUrl')));
        }
    }
}
