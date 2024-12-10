<?php

declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use Exception;
use JTL\Backend\Permissions;
use JTL\Helpers\Request;
use JTL\Router\Route;
use JTL\ServiceReport\ReportService;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ReportController
 * @package JTL\Router\Controller\Backend
 */
class ReportController extends AbstractBackendController
{
    protected ReportService $service;

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->service = new ReportService(Shop::Container()->getPasswordService());
        $this->smarty  = $smarty->assign('route', $this->route);
        $this->checkPermissions(Permissions::REPORT_VIEW);
        $this->getText->loadAdminLocale('pages/report');
        if (($id = Request::pInt('download')) > 0) {
            try {
                return $this->download($id);
            } catch (Exception $e) {
                $this->alertService->addError($e->getMessage(), 'download-error');
            }
        }
        if (Request::pInt('create') === 1) {
            $this->actionCreate();
        } elseif (($id = Request::pInt('delete')) > 0) {
            $this->actionDelete($id);
        } elseif (($id = Request::pInt('share')) > 0) {
            $this->actionShare($id);
        }

        return $smarty->assign('reports', $this->service->getReports([], []))
            ->getResponse('report.tpl');
    }

    protected function download(int $id, string $ext = '.html'): ResponseInterface
    {
        $file     = $this->service->getReportByID($id)->file . $ext;
        $fullPath = $this->service::BASE_PATH . $file;
        if (!\file_exists($fullPath)) {
            throw new \InvalidArgumentException(\__('Report file not found'));
        }
        $headers  = [
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => 'attachment; filename="' . \basename($file) . '"',
            'Expires'             => '0',
            'Cache-Control'       => 'must-revalidate',
            'Pragma'              => 'public',
            'Content-Length'      => \filesize($fullPath),
        ];
        $response = new HtmlResponse(\file_get_contents($fullPath), 200, $headers);

        return $ext === '.html'
            ? $response
            : $response->withHeader('Content-Type', 'application/json');
    }

    private function actionCreate(): void
    {
        try {
            $this->service->createReport();
            $this->alertService->addSuccess(\__('Report successfully created.'), 'create-success');
        } catch (Exception $e) {
            $this->alertService->addError(
                \sprintf(\__('Report could not be created: %s.'), $e->getMessage()),
                'create-error'
            );
        }
    }

    private function actionShare(int $id): void
    {
        try {
            $res  = $this->service->authorize($id);
            $link = Shop::getAdminURL() . '/' . Route::REPORT_VIEW . '/' . $res;
            $this->alertService->addInfo(
                \sprintf(\__('Report authorized. Use the following one time link: %s'), $link),
                'authorize-success'
            );
        } catch (Exception $e) {
            $this->alertService->addError(
                \sprintf(\__('Report could not be authorized: %s.'), $e->getMessage()),
                'authorize-error'
            );
        }
    }

    private function actionDelete(int $id): void
    {
        $error = \__('Unknown error');
        try {
            $deleted = $this->service->deleteReportByID($id);
        } catch (Exception $e) {
            $deleted = false;
            $error   = $e->getMessage();
        }
        if ($deleted === true) {
            $this->alertService->addSuccess(\__('Report successfully deleted.'), 'delete-success');
        } else {
            $this->alertService->addError(
                \sprintf(\__('Report could not be not deleted: %s.'), $error),
                'delete-error'
            );
        }
    }
}
