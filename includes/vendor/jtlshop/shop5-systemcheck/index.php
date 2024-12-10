<?php

declare(strict_types=1);

use Systemcheck\Environment;
use Systemcheck\Platform\Hosting;

require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * @param array                    $params
 * @param Smarty_Internal_Template $smarty
 * @return string
 */
function getResults(array $params, $smarty): string
{
    return $smarty->assign('test', $params['test'])->fetch('testResult.tpl');
}

$templatePath = __DIR__ . '/templates';
$smarty       = new Smarty();
$systemcheck  = new Environment();
$tests        = $systemcheck->executeTestGroup('Shop5');
$platform     = new Hosting();

header('Content-Type: text/html; charset=utf-8');
try {
    $smarty->assign('passed', $systemcheck->getIsPassed())
        ->assign('tests', $tests)
        ->registerPlugin(Smarty::PLUGIN_FUNCTION, 'getResults', 'getResults')
        ->assign('platform', $platform)
        ->setCacheDir($templatePath)
        ->setCompileDir($templatePath)
        ->display('systemcheck.tpl');
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
