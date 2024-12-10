<?php

declare(strict_types=1);

namespace JTL\dbeS;

use Exception;
use JTL\DB\DbInterface;
use JTL\Helpers\Text;
use Psr\Log\LoggerInterface;
use stdClass;
use Throwable;

/**
 * Class NetSyncHandler
 * @package JTL\dbeS
 */
class NetSyncHandler
{
    /**
     * @var NetSyncHandler|null
     */
    protected static ?NetSyncHandler $instance = null;

    /**
     * NetSyncHandler constructor.
     * @param DbInterface     $db
     * @param LoggerInterface $logger
     * @throws Exception
     */
    public function __construct(protected DbInterface $db, protected LoggerInterface $logger)
    {
        self::$instance = $this;
        if (!$this->isAuthenticated()) {
            static::throwResponse(NetSyncResponse::ERRORLOGIN);
        }
        $this->request((int)$_REQUEST['e']);
    }

    /**
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        // by token
        if (isset($_REQUEST['t'])) {
            \session_id($_REQUEST['t']);
            \session_start();

            return $_SESSION['bAuthed'];
        }
        // by syncdata
        $name          = Text::convertUTF8($_REQUEST['uid']);
        $pass          = Text::convertUTF8($_REQUEST['upwd']);
        $authenticated = \strlen($name) > 0
            && \strlen($pass) > 0
            && (new Synclogin($this->db, $this->logger))->checkLogin($name, $pass);
        if ($authenticated) {
            \session_start();
            $_SESSION['bAuthed'] = true;
        }

        return $authenticated;
    }

    /**
     * @param int        $code
     * @param null|mixed $data
     */
    protected static function throwResponse(int $code, $data = null): never
    {
        $response         = new stdClass();
        $response->nCode  = $code;
        $response->cToken = '';
        $response->oData  = null;
        if ($code === 0) {
            $response->cToken = \session_id();
            $response->oData  = $data;
        }
        echo \json_encode($response, \JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * @param int $request
     */
    protected function request($request): void
    {
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    public static function exception(Throwable $exception): void
    {
        // will be used in self::create as exception handler
    }

    /**
     * @param class-string    $class
     * @param DbInterface     $db
     * @param LoggerInterface $logger
     * @uses self::exception
     */
    public static function create(string $class, DbInterface $db, LoggerInterface $logger): void
    {
        if (self::$instance === null && \class_exists($class)) {
            /** @var NetSyncHandler $instance */
            $instance = new $class($db, $logger);
            \set_exception_handler($instance->exception(...));
        }
    }

    public function streamFile(string $filename, string $mimetype, string $outname = ''): never
    {
        $browser = $this->getBrowser($_SERVER['HTTP_USER_AGENT'] ?? '');
        if (($mimetype === 'application/octet-stream') || ($mimetype === 'application/octetstream')) {
            $mimetype = 'application/octet-stream';
            if (($browser === 'ie') || ($browser === 'opera')) {
                $mimetype = 'application/octetstream';
            }
        }

        @\ob_end_clean();
        @\ini_set('zlib.output_compression', 'Off');

        \header('Pragma: public');
        \header('Content-Transfer-Encoding: none');

        if ($outname === '') {
            $outname = \basename($filename);
        }
        if ($browser === 'ie') {
            \header('Content-Type: ' . $mimetype);
            \header('Content-Disposition: inline; filename="' . $outname . '"');
        } else {
            \header('Content-Type: ' . $mimetype . '; name="' . $outname . '"');
            \header('Content-Disposition: attachment; filename=' . $outname);
        }
        $size = @\filesize($filename);
        if ($size) {
            \header('Content-length: ' . $size);
        }
        \readfile($filename);
        \unlink($filename);
        exit;
    }

    /**
     * @param string $userAgent
     * @return string
     */
    private function getBrowser(string $userAgent): string
    {
        $browser = 'other';
        if (\preg_match('/^Opera(\/| )(\d.\d{1,2})/', $userAgent) === 1) {
            $browser = 'opera';
        } elseif (\preg_match('/^MSIE (\d.\d{1,2})/', $userAgent) === 1) {
            $browser = 'ie';
        }

        return $browser;
    }

    /**
     * @param string $baseDir
     * @return SystemFolder[]
     */
    protected function getFolderStruct(string $baseDir): array
    {
        $folders = [];
        $baseDir = \realpath($baseDir);
        if ($baseDir === false) {
            return $folders;
        }
        foreach (\scandir($baseDir, \SCANDIR_SORT_ASCENDING) ?: [] as $folder) {
            if ($folder === '.' || $folder === '..' || $folder[0] === '.') {
                continue;
            }
            $pathName = $baseDir . \DIRECTORY_SEPARATOR . $folder;
            if (\is_dir($pathName)) {
                $systemFolder              = new SystemFolder($folder, $pathName);
                $systemFolder->oSubFolders = $this->getFolderStruct($pathName);
                $folders[]                 = $systemFolder;
            }
        }

        return $folders;
    }

    /**
     * @param string $baseDir
     * @return SystemFile[]
     */
    protected function getFilesStruct(string $baseDir): array
    {
        $index   = 0;
        $files   = [];
        $baseDir = \realpath($baseDir);
        if ($baseDir === false) {
            return $files;
        }
        foreach (\scandir($baseDir, \SCANDIR_SORT_ASCENDING) ?: [] as $file) {
            if ($file === '.' || $file === '..' || $file[0] === '.') {
                continue;
            }
            $pathName = $baseDir . \DIRECTORY_SEPARATOR . $file;
            if (!\is_file($pathName)) {
                continue;
            }
            $pathinfo = \pathinfo($pathName);
            $files[]  = new SystemFile(
                $index++,
                $pathName,
                \str_replace([\PFAD_DOWNLOADS_PREVIEW, \PFAD_DOWNLOADS], '', $pathName),
                $pathinfo['filename'],
                $pathinfo['dirname'],
                $pathinfo['extension'] ?? '',
                \filemtime($pathName) ?: 0,
                \filesize($pathName) ?: 0
            );
        }

        return $files;
    }
}
